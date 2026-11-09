#!/usr/bin/env python3
"""Guliwera 4 · rolki ze zdjęć (1080×1920, 30 kl./s, MP4 H.264, cicha ścieżka audio – muzykę dodaj w Instagramie).
Najpierw: python3 social/narzedzia/tresci.py && node social/narzedzia/render.js
Potem:    python3 social/narzedzia/rolki.py
Zdjęcia: tylko kadr i powolny ruch kamery (zoom/przesunięcie). Bez zmian twarzy, bez filtrów."""
import json, os, subprocess, sys
from PIL import Image

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
REPO = os.path.dirname(ROOT)
W, H, FPS = 1080, 1920, 30
SCENA, HOOK, KONIEC, PRZEJSCIE = 3.0, 3.2, 3.0, 0.35
lista = json.load(open(os.path.join(ROOT, 'instagram', 'html', 'lista.json')))
out_dir = os.path.join(ROOT, 'instagram', 'rolki')
os.makedirs(out_dir, exist_ok=True)


def klatka_zdjecia(img, t, dlugosc, kierunek):
    """Powolny zoom 1.00→1.07 i lekkie przesunięcie w pionie (subpikselowo, bez drgań)."""
    sw, sh = img.size
    base = max(W / sw, H / sh)
    p = t / dlugosc
    zoom = 1.0 + 0.07 * (p if kierunek % 2 == 0 else 1 - p)
    s = base * zoom
    cx = sw / 2
    cy = sh / 2 + (sh * 0.03) * (p - 0.5) * (1 if kierunek % 2 == 0 else -1)
    # nie wychodź poza zdjęcie
    cx = min(max(cx, W / 2 / s), sw - W / 2 / s)
    cy = min(max(cy, H / 2 / s), sh - H / 2 / s)
    return img.transform((W, H), Image.AFFINE, (1 / s, 0, cx - W / 2 / s, 0, 1 / s, cy - H / 2 / s), Image.BICUBIC)


def nakladka(png, t):
    """Tekst wjeżdża: 0.35 s przenikania + 24 px w górę."""
    a = min(1.0, max(0.0, (t - 0.25) / 0.35))
    if a <= 0:
        return None
    a = 1 - (1 - a) ** 3
    warstwa = Image.new('RGBA', (W, H), (0, 0, 0, 0))
    warstwa.alpha_composite(png, (0, int(24 * (1 - a))))
    if a < 1:
        warstwa.putalpha(warstwa.getchannel('A').point(lambda v: int(v * a)))
    return warstwa


for rid, zdjecia in lista['rolki'].items():
    if len(sys.argv) > 1 and rid not in sys.argv[1:]:
        continue  # np. python3 social/narzedzia/rolki.py r3-zacznij-za-darmo
    sceny = []
    for i, foto in enumerate(zdjecia, start=1):
        txt = Image.open(os.path.join(ROOT, 'instagram', 'png', 'rolki', f'{rid}-tekst-{i}.png')).convert('RGBA')
        img = Image.open(os.path.join(REPO, 'brand', 'zdjecia-sesja', foto)).convert('RGB') if foto else None
        dl = HOOK if i == 1 else (KONIEC if foto is None else SCENA)
        sceny.append((img, txt, dl, i))

    def render_sceny(sc, t):
        img, txt, dl, i = sc
        if img is None:
            ram = txt.copy()  # plansza końcowa (pełne tło)
            a = min(1.0, t / 0.4)
            return ram.convert('RGB') if a >= 1 else Image.blend(Image.new('RGB', (W, H), '#FFF6EE'), ram.convert('RGB'), a)
        ram = klatka_zdjecia(img, t, dl, i).convert('RGBA')
        n = nakladka(txt, t)
        if n is not None:
            ram.alpha_composite(n)
        return ram.convert('RGB')

    calosc = sum(s[2] for s in sceny)
    dest = os.path.join(out_dir, f'{rid}.mp4')
    ff = subprocess.Popen(['ffmpeg', '-y', '-loglevel', 'error',
                           '-f', 'rawvideo', '-pix_fmt', 'rgb24', '-s', f'{W}x{H}', '-r', str(FPS), '-i', '-',
                           '-f', 'lavfi', '-i', 'anullsrc=channel_layout=stereo:sample_rate=44100',
                           '-shortest', '-c:v', 'libx264', '-preset', 'slow', '-crf', '18', '-pix_fmt', 'yuv420p',
                           '-profile:v', 'high', '-c:a', 'aac', '-b:a', '128k', '-movflags', '+faststart', dest],
                          stdin=subprocess.PIPE)
    start = 0.0
    granice = []
    for s in sceny:
        granice.append(start); start += s[2]
    for k in range(int(calosc * FPS)):
        t = k / FPS
        idx = max(j for j, g in enumerate(granice) if g <= t)
        lok = t - granice[idx]
        ram = render_sceny(sceny[idx], lok)
        # przenikanie do następnej sceny
        if idx + 1 < len(sceny) and lok > sceny[idx][2] - PRZEJSCIE:
            a = (lok - (sceny[idx][2] - PRZEJSCIE)) / PRZEJSCIE
            nast = sceny[idx + 1]
            nr = klatka_zdjecia(nast[0], 0, nast[2], nast[3]) if nast[0] is not None else Image.new('RGB', (W, H), '#FFF6EE')
            ram = Image.blend(ram, nr, a)
        ff.stdin.write(ram.tobytes())
    ff.stdin.close(); ff.wait()
    # okładka rolki = klatka z hookiem (do wgrania jako „cover” w Instagramie)
    okl = render_sceny(sceny[0], 1.5)
    okl.save(os.path.join(out_dir, f'{rid}-okladka.jpg'), quality=92)
    print(rid, f'{calosc:.1f} s →', dest)
