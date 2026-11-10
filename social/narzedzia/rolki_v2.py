#!/usr/bin/env python3
"""Guliwera 4 · rolki v2: dynamiczny montaż ze zdjęć (cięcia co ~1 s, różne plany, napisy słowo po słowie).
Uruchom z katalogu repozytorium:  python3 social/narzedzia/rolki_v2.py [nazwa_rolki ...]
Wynik: social/instagram/rolki/v2/<nazwa>.mp4 (1080×1920, 30 kl./s) + okładka JPG.

Zasady montażu (social/research/rolki-montaz.md):
- hook w PIERWSZEJ klatce (bez przenikania), tekst czytelny bez dźwięku,
- cięcie co 0,7–1,5 s, każde cięcie zmienia plan (szeroki / średni / zbliżenie / detal),
- na cięciu „punch-in” (szybki najazd 1,12→1,0 w 0,2 s), potem powolny dryf,
- cięcia w siatce 0,5 s = 120 BPM, więc muzyka z Instagrama siada w rytm,
- pasek postępu u góry, plansza końcowa krótka (rolka się zapętla).
Zdjęcia: tylko kadr i ruch kamery – bez zmian twarzy (twarz tylko: portret w jasnym garniturze, 07, 09)."""
import os, subprocess, sys
from PIL import Image, ImageDraw, ImageFont

REPO = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
OUT = os.path.join(REPO, 'social', 'instagram', 'rolki', 'v2')
os.makedirs(OUT, exist_ok=True)
W, H, FPS = 1080, 1920, 30
KREM, ATR, ZOL, KORAL = (255, 246, 238), (58, 35, 64), (255, 200, 74), (255, 107, 61)
FONTY = os.path.expanduser('~/.fonts')


def font(plik, wariant, rozmiar):
    f = ImageFont.truetype(os.path.join(FONTY, plik), rozmiar)
    try:
        f.set_variation_by_name(wariant)
    except Exception:
        pass
    return f


F_NAPIS = font('DMSans%5Bopsz,wght%5D.ttf', 'ExtraBold', 76)
F_HOOK = font('Fraunces%5BSOFT,WONK,opsz,wght%5D.ttf', 'SemiBold', 92)
F_MALY = font('Caveat%5Bwght%5D.ttf', 'Bold', 64)

# Zdjęcia: ścieżka + środek twarzy (x, y) i wysokość twarzy w pikselach źródła (do kadrów)
ZDJ = {
    'B':   dict(p='logo/angelika-portret-bialy.jpg', twarz=(575, 250)),
    '07':  dict(p='brand/zdjecia-sesja/07-portret-boucle.jpg', twarz=(432, 470)),
    '09':  dict(p='brand/zdjecia-sesja/09-kanapa-bialy-garnitur.jpg', twarz=(575, 268)),
}
_cache = {}


def zdjecie(k):
    if k not in _cache:
        _cache[k] = Image.open(os.path.join(REPO, ZDJ[k]['p'])).convert('RGB')
    return _cache[k]


def kadr(k, plan, detal=None):
    """Zwraca (cx, cy, szer) prostokąta 9:16 w źródle. Oczy ok. 36% wysokości kadru."""
    im = zdjecie(k); sw, sh = im.size
    fx, fy = ZDJ[k]['twarz']
    if plan == 'detal':
        cx, cy, szer = detal
    else:
        szer = {'szeroki': min(sw, sh * 9 / 16), 'sredni': 680, 'blisko': 520}[plan]
        wys = szer * 16 / 9
        cx = fx
        cy = fy - 0.36 * wys + wys / 2
    wys = szer * 16 / 9
    cx = min(max(cx, szer / 2), sw - szer / 2)
    cy = min(max(cy, wys / 2), sh - wys / 2)
    return cx, cy, szer


def klatka(k, plan, t, dl, detal=None, kier=1):
    cx, cy, szer = kadr(k, plan, detal)
    # punch-in: 1.12 → 1.0 w 0,2 s (ease-out), potem dryf 1.0 → 1.035
    if t < 0.2:
        e = t / 0.2
        z = 1.0 + 0.12 * (1 - e) ** 3
    else:
        z = 1.0 + 0.035 * (t - 0.2) / max(dl - 0.2, 0.01)
    s = W / szer * z
    im = zdjecie(k); sw, sh = im.size
    cx += kier * 6 * t  # minimalny ruch w bok – kadr „żyje”
    cx = min(max(cx, W / 2 / s), sw - W / 2 / s)
    cy = min(max(cy, H / 2 / s), sh - H / 2 / s)
    return im.transform((W, H), Image.AFFINE, (1 / s, 0, cx - W / 2 / s, 0, 1 / s, cy - H / 2 / s), Image.BICUBIC)


def zlam(slowa, f, maks=900):
    linie, akt = [], []
    for s in slowa:
        test = ' '.join(x.strip('*') for x in akt + [s])
        if akt and f.getlength(test) > maks:
            linie.append(akt); akt = [s]
        else:
            akt.append(s)
    if akt:
        linie.append(akt)
    return linie


def napis(ram, tekst, t, y_srodek=1240, hook=False, maly=None):
    """Napis słowo po słowie (co 0,08 s). *słowo* = żółte podświetlenie. Hook: całość od pierwszej klatki."""
    d = ImageDraw.Draw(ram)
    f = F_HOOK if hook else F_NAPIS
    slowa = tekst.split()
    # zakres podświetlenia może obejmować kilka słów: *Nie dziesięć.*
    swiec, w = set(), False
    for i, sl in enumerate(slowa):
        if sl.startswith('*'):
            w = True
        if w:
            swiec.add(i)
        if sl.endswith('*'):
            w = False
    linie = zlam(slowa, f)
    lh = int(f.size * 1.22)
    pad_x, pad_y = 34, 16
    total = len(linie) * (lh + 14)
    y = y_srodek - total // 2
    licz = 0
    widoczne = len(slowa) if hook else int(t / 0.08) + 1
    for lin in linie:
        tekst_lin = ' '.join(s.strip('*') for s in lin)
        szer = f.getlength(tekst_lin)
        x0 = (W - szer) / 2
        d.rounded_rectangle((x0 - pad_x, y - pad_y + 6, x0 + szer + pad_x, y + lh - 2), 24, fill=KREM)
        x = x0
        for s in lin:
            czysty = s.strip('*')
            sz = f.getlength(czysty)
            if licz < widoczne:
                if licz in swiec:
                    d.rounded_rectangle((x - 10, y + 10, x + sz + 10, y + lh - 10), 14, fill=ZOL)
                d.text((x, y + 4), czysty, font=f, fill=ATR)
            licz += 1
            x += sz + f.getlength(' ')
        y += lh + 14
    if maly:  # numer / etykieta jako żółta pigułka nad napisem
        fm = font('DMSans%5Bopsz,wght%5D.ttf', 'ExtraBold', 44)
        etyk = maly.upper() if not maly.isdigit() else maly
        sz = fm.getlength(etyk)
        yc = y_srodek - total // 2 - 62
        d.rounded_rectangle((W / 2 - sz / 2 - 30, yc - 36, W / 2 + sz / 2 + 30, yc + 36), 36, fill=ZOL)
        d.text((W / 2, yc), etyk, font=fm, fill=ATR, anchor='mm')


def plansza(t, tytul, pod):
    ram = Image.new('RGB', (W, H), KREM)
    d = ImageDraw.Draw(ram)
    d.ellipse((560, -380, 1460, 520), fill=(255, 217, 228))
    d.ellipse((-320, 1420, 420, 2160), fill=(237, 229, 251))
    e = min(1.0, t / 0.18)
    z = 1.0 + 0.12 * (1 - e) ** 3
    rozm = 118
    while font('Fraunces%5BSOFT,WONK,opsz,wght%5D.ttf', 'SemiBold', rozm).getlength(tytul) > 900:
        rozm -= 4
    f = font('Fraunces%5BSOFT,WONK,opsz,wght%5D.ttf', 'SemiBold', int(rozm * z))
    d.text((W / 2, 820), tytul, font=f, fill=ATR, anchor='mm')
    d.rounded_rectangle((260, 980, 820, 1110), 65, fill=(232, 81, 42))
    d.text((W / 2, 1045), pod, font=font('DMSans%5Bopsz,wght%5D.ttf', 'Bold', 52), fill='white', anchor='mm')
    d.text((W / 2, 1230), 'Akademia Guliwera · guliwera4.pl', font=font('DMSans%5Bopsz,wght%5D.ttf', 'Medium', 40), fill=(122, 101, 128), anchor='mm')
    d.text((W / 2, 1330), 'Angelika Pruchniak-Gąszczyk', font=F_MALY, fill=(232, 81, 42), anchor='mm')
    return ram


# Scenariusze: (zdjęcie, plan, długość s, tekst, opcje). Długości w siatce 0,5 s (120 BPM).
ROLKI = {
 'r1-trzy-pytania': [
    ('B', 'blisko', 1.0, 'Zanim powiesz *„tak”*', dict(hook=True)),
    ('09', 'sredni', 1.0, 'nowemu projektowi…', {}),
    ('07', 'szeroki', 1.5, 'Czy to przybliża mnie do *celu?*', dict(maly='pytanie 1')),
    ('B', 'sredni', 1.5, 'Co *przestanę* robić, żeby to zmieścić?', dict(maly='pytanie 2')),
    ('09', 'blisko', 1.5, 'Ile to zarobi i *kiedy?*', dict(maly='pytanie 3')),
    ('07', 'detal', 1.0, 'Odpowiedź „nic” na pytanie 2…', dict(detal=(720, 1230, 560))),
    ('B', 'blisko', 1.0, '…oznacza *sen.*', {}),
    ('KONIEC', None, 1.5, 'Zapisz na później', dict(pod='zapisz')),
 ],
 'r2-etat-i-wlasna-marka': [
    ('09', 'blisko', 1.0, 'Etat + *własna marka*', dict(hook=True)),
    ('B', 'szeroki', 1.0, 'Co mi naprawdę pomaga?', {}),
    ('07', 'sredni', 1.5, 'Jeden cel na kwartał. *Nie dziesięć.*', dict(maly='1')),
    ('09', 'szeroki', 1.5, 'Stałe *bloki* w kalendarzu', dict(maly='2')),
    ('B', 'detal', 1.5, 'Produkty cyfrowe zamiast *godzin*', dict(maly='3', detal=(600, 1260, 540))),
    ('07', 'blisko', 1.0, 'A co pomaga *Tobie?*', {}),
    ('KONIEC', None, 1.5, 'Napisz w komentarzu', dict(pod='Twoja rada')),
 ],
 'r3-zacznij-za-darmo': [
    ('07', 'blisko', 1.0, 'Liczby w firmie *stresują?*', dict(hook=True)),
    ('B', 'sredni', 1.0, 'To nie Twoja wina.', {}),
    ('09', 'sredni', 1.0, 'Nikt ich nie tłumaczy *po ludzku.*', {}),
    ('07', 'detal', 1.5, 'Krótkie lekcje *w telefonie*', dict(detal=(720, 1230, 560))),
    ('B', 'blisko', 1.5, 'Zysk, marża, *próg rentowności*', {}),
    ('09', 'blisko', 1.0, 'Na start *za darmo.*', {}),
    ('KONIEC', None, 1.5, 'Zacznij za darmo', dict(pod='link w bio')),
 ],
}


def renderuj(nazwa, sceny):
    calosc = sum(s[2] for s in sceny)
    dest = os.path.join(OUT, f'{nazwa}.mp4')
    ff = subprocess.Popen(['ffmpeg', '-y', '-loglevel', 'error', '-f', 'rawvideo', '-pix_fmt', 'rgb24',
                           '-s', f'{W}x{H}', '-r', str(FPS), '-i', '-',
                           '-f', 'lavfi', '-i', 'anullsrc=channel_layout=stereo:sample_rate=44100', '-shortest',
                           '-c:v', 'libx264', '-preset', 'slow', '-crf', '17', '-pix_fmt', 'yuv420p',
                           '-c:a', 'aac', '-b:a', '128k', '-movflags', '+faststart', dest], stdin=subprocess.PIPE)
    start, okladka = 0.0, None
    for i, (k, plan, dl, tekst, opt) in enumerate(sceny):
        for n in range(int(round(dl * FPS))):
            t = n / FPS
            if k == 'KONIEC':
                ram = plansza(t, tekst, opt.get('pod', ''))
            else:
                ram = klatka(k, plan, t, dl, opt.get('detal'), kier=1 if i % 2 else -1)
                napis(ram, tekst, t, hook=opt.get('hook', False), maly=opt.get('maly'))
            # flesz przy cięciu na scenę z numerem (2 klatki) – akcent rytmu
            if opt.get('maly') and n < 2:
                ram = Image.blend(ram, Image.new('RGB', (W, H), 'white'), 0.35 if n == 0 else 0.15)
            # pasek postępu
            p = (start + t) / calosc
            ImageDraw.Draw(ram).rectangle((0, 0, int(W * p), 10), fill=KORAL)
            if okladka is None:
                okladka = ram.copy()
            ff.stdin.write(ram.tobytes())
        start += dl
    ff.stdin.close(); ff.wait()
    okladka.save(os.path.join(OUT, f'{nazwa}-okladka.jpg'), quality=92)
    print(nazwa, f'{calosc:.1f} s →', dest)


if __name__ == '__main__':
    wybrane = sys.argv[1:] or list(ROLKI)
    for n in wybrane:
        renderuj(n, ROLKI[n])
