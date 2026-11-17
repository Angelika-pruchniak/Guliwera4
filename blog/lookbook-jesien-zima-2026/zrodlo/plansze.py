"""Plansze stylizacji (1080x1350) z ilustracjami ubrań, strzałkami i opisami: rzecz, marka, skład, cena.
Użycie: python3 plansze.py <node_modules z @fontsource> <katalog_html_wyjściowy>
Ilustracje to własne rysunki wektorowe (SVG), bez zdjęć produktów ze sklepów.
"""
import sys, pathlib, html, json

FONTS = pathlib.Path(sys.argv[1]) / "@fontsource"
OUT = pathlib.Path(sys.argv[2])
INK = "#3A2340"

# ---------- ilustracje (viewBox 200x240) ----------
def _btn(x, y, r=4, c="#F4EFE6"):
    return f'<circle cx="{x}" cy="{y}" r="{r}" fill="{c}" stroke="{INK}" stroke-width="1.5"/>'

def coat(c, **k):
    return f'''<path d="M70 20 L100 30 L130 20 L165 38 L175 120 L162 125 L160 225 L40 225 L38 125 L25 120 L35 38 Z" fill="{c}" stroke="{INK}" stroke-width="2.5" stroke-linejoin="round"/>
<path d="M70 20 L100 95 L130 20" fill="none" stroke="{INK}" stroke-width="2"/><path d="M70 20 L82 70 L100 95 M130 20 L118 70 L100 95" fill="{c}" stroke="{INK}" stroke-width="2" opacity=".9"/>
<line x1="100" y1="95" x2="100" y2="225" stroke="{INK}" stroke-width="1.5"/>{_btn(90,120)}{_btn(110,120)}{_btn(90,155)}{_btn(110,155)}
<path d="M48 160 L70 160 M130 160 L152 160" stroke="{INK}" stroke-width="2"/>'''

def blazer(c, **k):
    return f'''<path d="M68 30 L100 40 L132 30 L165 45 L175 140 L160 143 L158 200 L42 200 L40 143 L25 140 L35 45 Z" fill="{c}" stroke="{INK}" stroke-width="2.5" stroke-linejoin="round"/>
<path d="M68 30 L84 85 L100 120 L116 85 L132 30" fill="none" stroke="{INK}" stroke-width="2"/><path d="M78 60 L92 72 M122 60 L108 72" stroke="{INK}" stroke-width="2"/>
<line x1="100" y1="120" x2="100" y2="200" stroke="{INK}" stroke-width="1.5"/>{_btn(100,140,5)}{_btn(100,165,5)}<path d="M50 165 L75 165 M125 165 L150 165" stroke="{INK}" stroke-width="2"/>'''

def boucle(c, **k):
    dots = "".join(f'<circle cx="{x}" cy="{y}" r="1.6" fill="{INK}" opacity=".25"/>' for x in range(48, 156, 9) for y in range(50, 190, 9))
    return f'''<path d="M72 32 Q100 50 128 32 L165 45 L172 140 L158 143 L156 195 L44 195 L42 143 L28 140 L35 45 Z" fill="{c}" stroke="{INK}" stroke-width="2.5" stroke-linejoin="round"/>{dots}
<path d="M72 32 Q100 50 128 32" fill="none" stroke="{INK}" stroke-width="3"/><path d="M100 45 L100 195" stroke="{INK}" stroke-width="1.5"/>
{_btn(92,80,5)}{_btn(92,110,5)}{_btn(92,140,5)}{_btn(92,170,5)}<path d="M52 150 L76 150 M124 150 L148 150" stroke="{INK}" stroke-width="2.5"/>'''

def shirt(c, **k):
    return f'''<path d="M75 30 L100 38 L125 30 L160 45 L172 150 L158 152 L155 210 L45 210 L42 152 L28 150 L40 45 Z" fill="{c}" stroke="{INK}" stroke-width="2.5" stroke-linejoin="round"/>
<path d="M75 30 L88 58 L100 40 L112 58 L125 30" fill="{c}" stroke="{INK}" stroke-width="2"/><line x1="100" y1="40" x2="100" y2="210" stroke="{INK}" stroke-width="1.5"/>
{_btn(106,75,3,"#fff")}{_btn(106,105,3,"#fff")}{_btn(106,135,3,"#fff")}{_btn(106,165,3,"#fff")}'''

def blouse(c, **k):
    return f'''<path d="M78 30 L100 38 L122 30 L158 45 L170 150 L156 152 L152 205 L48 205 L44 152 L30 150 L42 45 Z" fill="{c}" stroke="{INK}" stroke-width="2.5" stroke-linejoin="round"/>
<path d="M100 48 Q82 40 74 56 Q86 66 100 52 Q114 66 126 56 Q118 40 100 48 Z" fill="{c}" stroke="{INK}" stroke-width="2"/><path d="M98 54 L88 95 M102 54 L112 92" stroke="{INK}" stroke-width="2"/>
<path d="M60 100 Q100 112 140 100" fill="none" stroke="{INK}" stroke-width="1" opacity=".4"/>'''

def sweater(c, neck="crew", **k):
    collar = ('<path d="M76 22 L124 22 L124 40 Q100 48 76 40 Z" fill="%s" stroke="%s" stroke-width="2"/>' % (c, INK)) if neck == "golf" else (f'<path d="M78 30 Q100 48 122 30" fill="none" stroke="{INK}" stroke-width="3"/>')
    return f'''<path d="M78 30 L122 30 L160 44 L178 160 L160 163 L156 205 L44 205 L40 163 L22 160 L40 44 Z" fill="{c}" stroke="{INK}" stroke-width="2.5" stroke-linejoin="round"/>{collar}
<path d="M44 192 L156 192 M24 150 L40 150 M160 150 L176 150" stroke="{INK}" stroke-width="2"/>{"".join(f'<line x1="{x}" y1="192" x2="{x}" y2="205" stroke="{INK}" stroke-width="1"/>' for x in range(50,156,8))}'''

def trousers(c, wide=True, **k):
    w = 28 if wide else 12
    return f'''<path d="M58 20 L142 20 L{150+w} 230 L{104} 230 L100 80 L96 230 L{50-w} 230 Z" fill="{c}" stroke="{INK}" stroke-width="2.5" stroke-linejoin="round"/>
<path d="M58 34 L142 34" stroke="{INK}" stroke-width="2"/><path d="M78 36 L{70-w//2} 228 M122 36 L{130+w//2} 228" stroke="{INK}" stroke-width="1" opacity=".5"/>'''

def jeans(c, **k):
    return trousers(c, wide=False) + f'<path d="M64 36 Q72 60 92 50 M136 36 Q128 60 108 50" fill="none" stroke="#E0B060" stroke-width="1.5"/>'

def skirt(c, **k):
    lines = "".join(f'<line x1="{60+i*8}" y1="40" x2="{30+i*14}" y2="225" stroke="{INK}" stroke-width="1" opacity=".5"/>' for i in range(11))
    return f'''<path d="M60 25 L140 25 L140 40 L172 225 L28 225 L60 40 Z" fill="{c}" stroke="{INK}" stroke-width="2.5" stroke-linejoin="round"/>{lines}<line x1="60" y1="40" x2="140" y2="40" stroke="{INK}" stroke-width="2"/>'''

def dress(c, **k):
    return f'''<path d="M78 18 L100 60 L122 18 L150 30 L168 95 L152 98 L140 80 L136 105 L166 232 L34 232 L64 105 L60 80 L48 98 L32 95 L50 30 Z" fill="{c}" stroke="{INK}" stroke-width="2.5" stroke-linejoin="round"/>
<path d="M78 18 L100 60 L122 18" fill="none" stroke="{INK}" stroke-width="2"/><path d="M64 105 Q100 115 136 105" fill="none" stroke="{INK}" stroke-width="2"/><path d="M100 60 L120 105 L150 232" fill="none" stroke="{INK}" stroke-width="1.5" opacity=".6"/>'''

def pumps(c, **k):
    return f'''<g transform="translate(0,60)"><path d="M20 110 Q30 70 70 72 Q110 74 140 60 L165 58 Q180 60 182 80 L180 120 L170 120 L168 92 Q150 96 130 104 Q90 120 30 120 Z" fill="{c}" stroke="{INK}" stroke-width="2.5" stroke-linejoin="round"/>
<path d="M168 92 L172 150 L182 150 L180 120" fill="{c}" stroke="{INK}" stroke-width="2.5"/><path d="M60 78 Q90 92 140 66" fill="none" stroke="{INK}" stroke-width="1.5" opacity=".5"/></g>'''

def flats(c, **k):
    return f'''<g transform="translate(0,70)"><path d="M18 110 Q24 78 66 76 Q110 74 150 70 Q182 70 184 96 L184 118 Q120 122 30 122 Z" fill="{c}" stroke="{INK}" stroke-width="2.5" stroke-linejoin="round"/>
<path d="M60 80 Q100 98 150 74" fill="none" stroke="{INK}" stroke-width="1.5" opacity=".5"/><path d="M18 118 L184 118" stroke="{INK}" stroke-width="3"/></g>'''

def loafers(c, **k):
    return f'''<g transform="translate(0,60)"><path d="M16 120 Q18 84 60 80 L80 66 L150 64 Q182 66 184 98 L184 126 Q110 130 26 130 Z" fill="{c}" stroke="{INK}" stroke-width="2.5" stroke-linejoin="round"/>
<path d="M80 66 Q100 92 150 64" fill="none" stroke="{INK}" stroke-width="2"/><rect x="98" y="74" width="34" height="8" rx="3" fill="#E7C27A" stroke="{INK}" stroke-width="1.5"/><path d="M16 126 L184 126" stroke="{INK}" stroke-width="4"/></g>'''

def boots(c, **k):
    return f'''<path d="M70 15 L130 15 L132 170 Q160 172 178 192 Q186 206 182 222 L60 222 L64 200 Q66 120 70 15 Z" fill="{c}" stroke="{INK}" stroke-width="2.5" stroke-linejoin="round"/>
<path d="M60 222 L60 236 L76 236 L78 222 M182 222 L182 228 L120 228" fill="{INK}" stroke="{INK}" stroke-width="2"/><path d="M118 20 L120 150" stroke="{INK}" stroke-width="1" stroke-dasharray="4 3"/>'''

def bag(c, small=False, **k):
    s = .75 if small else 1
    return f'''<g transform="translate({100-100*s},{120-110*s}) scale({s})"><path d="M60 70 Q60 30 100 30 Q140 30 140 70" fill="none" stroke="{INK}" stroke-width="7"/><path d="M60 70 Q60 30 100 30 Q140 30 140 70" fill="none" stroke="{c}" stroke-width="4"/>
<path d="M28 75 L172 75 L182 200 L18 200 Z" fill="{c}" stroke="{INK}" stroke-width="2.5" stroke-linejoin="round"/><path d="M28 75 Q100 120 172 75" fill="none" stroke="{INK}" stroke-width="2"/>
<rect x="90" y="96" width="20" height="16" rx="3" fill="#E7C27A" stroke="{INK}" stroke-width="1.5"/></g>'''

def pearls(c, **k):
    import math
    pts = "".join(f'<circle cx="{100+70*math.sin(t/30*math.pi*1.7-math.pi*.85):.1f}" cy="{70+90*math.cos(t/30*math.pi*1.7-math.pi*.85)*-1+80:.1f}" r="7" fill="#F7F2EA" stroke="{INK}" stroke-width="1.2"/>' for t in range(31))
    return f'<g transform="translate(0,-10)">{pts}</g>'

def scarf(c, **k):
    return f'''<path d="M30 60 L170 60 L100 200 Z" fill="{c}" stroke="{INK}" stroke-width="2.5" stroke-linejoin="round"/>
<path d="M45 72 L155 72 L100 182 Z" fill="none" stroke="#F7F2EA" stroke-width="3"/><circle cx="100" cy="100" r="10" fill="#F7F2EA" stroke="{INK}" stroke-width="1.5"/>'''

sys.path.insert(0, str(pathlib.Path(__file__).parent))
from rysunki import coat, blazer, boucle, shirt, blouse, sweater, golf, pumps, pearls  # poprawione rysunki

RYS = dict(coat=coat, blazer=blazer, boucle=boucle, shirt=shirt, blouse=blouse, sweater=sweater, golf=golf,
           wide=trousers, straight=lambda c, **k: trousers(c, wide=False), jeans=jeans, skirt=skirt, dress=dress, pumps=pumps,
           flats=flats, loafers=loafers, boots=boots, bag=bag, smallbag=lambda c, **k: bag(c, small=True), pearls=pearls, scarf=scarf)

def svg(kind, color):
    return f'<svg viewBox="0 0 200 240" xmlns="http://www.w3.org/2000/svg">{RYS[kind](color)}</svg>'

# ---------- katalog rzeczy ----------
# (rysunek, kolor, nazwa, marka/źródło, skład, cena, link)
K = {
 "plaszcz": ("coat", "#B98A5A", "Płaszcz camel", "Molton (outlet)", "wełniany – sprawdź % na metce", "499 zł", "https://molton.pl/pl/products/kamelowy-plaszcz-z-kolnierzykiem-9443.html"),
 "plaszcz_p": ("coat", "#B98A5A", "Płaszcz dyplomatka", "Molton · wersja premium", "100% wełna", "1 149 zł", "https://molton.pl/pl/products/camelowy-plaszcz-dyplomatka-100-welna-16394.html"),
 "marynarka": ("blazer", "#1E2A4A", "Granatowa marynarka", "Mohito, dwurzędowa", "sprawdź: wiskoza/wełna", "ok. 170 zł", "https://www.nuumi.pl/p/mohito-dwurzedowa-granatowa-marynarka-granatowy"),
 "bordo": ("blazer", "#7A2836", "Bordowa marynarka", "TK Maxx – polowanie", "szukaj: min. 50% wełny", "do 250 zł", ""),
 "boucle": ("boucle", "#EFE2CC", "Żakiet bouclé", "z szafy / TK Maxx", "szukaj: wełna lub bawełna", "do 300 zł", ""),
 "koszula": ("shirt", "#FFFFFF", "Biała koszula", "Wólczanka DO49WL0152", "bawełna", "249,99 zł", "https://wolczanka.pl/biala-koszula-damska-wolczanka-99"),
 "koszula_k": ("shirt", "#F3E6D3", "Kremowa koszula", "z szafy", "bawełna lub jedwab", "–", ""),
 "jedwab": ("blouse", "#EFE4D2", "Jedwabna bluzka", "Wólczanka (lub z szafy)", "100% jedwab", "499,99 zł", "https://wolczanka.pl/koszule-damskie"),
 "sw_ecru": ("sweater", "#EFE4D2", "Sweter ecru", "Wólczanka, merino", "wełna merino", "99,99 zł", "https://wolczanka.pl/bia%C5%82y-sweter-w%C3%B3lczanka-49674-1"),
 "sw_blekit": ("sweater", "#A9C4E2", "Błękitny sweter", "Wólczanka, z kaszmirem", "35% wiskoza, 30% poliamid, 30% merino, 5% kaszmir", "249,99 zł", "https://wolczanka.pl/niebieski-sweter-damski-w%C3%B3lczanka-37587-1"),
 "sw_welna": ("sweater", "#C9A27A", "Sweter wełniany", "Solar", "100% wełna owcza", "299 zł", "https://www.solar.com.pl/welniany-sweter-za035978.html"),
 "golf": ("golf", "#1E2A4A", "Granatowy golf", "z szafy", "najlepiej merino", "–", ""),
 "spodnie_kant": ("wide", "#D9C3A5", "Spodnie w kant", "Reserved, szerokie", "z dodatkiem wiskozy", "139,99 zł", "https://www.reserved.com/pl/pl/kobieta/spodnie/luzne"),
 "spodnie_gran": ("straight", "#1E2A4A", "Granatowe spodnie", "z szafy", "wełna lub wiskoza", "–", ""),
 "spodnie_czarne": ("straight", "#1C1A1F", "Czarne spodnie", "z szafy", "–", "–", ""),
 "spodnie_dzian": ("straight", "#2B3655", "Spodnie z dzianiny", "z szafy", "dzianina, nie gniecie się", "–", ""),
 "garnitur_m": ("blazer", "#F3E6D3", "Jasny garnitur", "z szafy (zdjęcie 09)", "–", "–", ""),
 "garnitur_s": ("wide", "#F3E6D3", "Spodnie od garnituru", "z szafy", "–", "–", ""),
 "jeans": ("jeans", "#5B7393", "Proste jeansy", "z szafy", "bawełna", "–", ""),
 "spodnica": ("skirt", "#1E2A4A", "Spódnica plisowana midi", "TK Maxx / Half Price", "szukaj: wełna lub gruba krepa", "do 150 zł", ""),
 "sukienka": ("dress", "#1E2A4A", "Sukienka midi kopertowa", "Ansin", "dzianina", "399 zł", "https://ansin.pl/sukienka-midi-z-kopertowym-dekoltem-granatowa"),
 "czolenka": ("pumps", "#1C1A1F", "Czółenka, niski obcas", "Eva Minge (Modivo)", "sprawdź: skóra", "ok. 189 zł", "https://modivo.pl/c/kobiety/obuwie/czolenka/marka:eva_minge"),
 "baleriny": ("flats", "#5A3A26", "Płaskie czółenka", "Kazar AURELO · premium", "zamsz", "549 zł", "https://kazar.com/nowosci/kobieta.html"),
 "mokasyny": ("loafers", "#6B4630", "Mokasyny", "Gino Rossi (eobuwie)", "skóra", "ok. 230 zł", "https://eobuwie.com.pl/c/damskie/mokasyny/marka:gino_rossi"),
 "kozaki": ("boots", "#6B4630", "Kozaki brązowe", "Half Price – polowanie", "szukaj: skóra naturalna", "do 350 zł", ""),
 "kuferek": ("bag", "#B98A5A", "Kuferek camel", "Eva Minge (Modivo)", "sprawdź: skóra czy ekoskóra", "ok. 256 zł", "https://modivo.pl/c/kobiety/torby-i-torebki/marka:eva_minge"),
 "torebka": ("smallbag", "#7A2836", "Mała torebka", "z szafy", "–", "–", ""),
 "perly": ("pearls", "#F4EFE6", "Perły", "pracownia / TK Maxx", "perły słodkowodne", "do 100 zł", ""),
 "apaszka": ("scarf", "#A9C4E2", "Apaszka", "z szafy / TK Maxx", "jedwab", "–", ""),
}

STYL = [
 ("Poniedziałek w biurze", "biuro", ["marynarka", "koszula", "spodnie_kant", "kuferek", "mokasyny"], "Spokojny granat i jedna mocna torebka."),
 ("Wystąpienie i konferencja", "scena", ["plaszcz", "sukienka", "perly", "czolenka"], "Jedna linia od szyi do kolan. Uwaga zostaje na twarzy."),
 ("Spotkanie z klientem", "klient", ["sw_blekit", "spodnica", "kuferek", "kozaki"], "Miękko, ale z kształtem."),
 ("Kolacja biznesowa", "wieczór", ["bordo", "jedwab", "spodnie_czarne", "czolenka"], "Burgund wieczorem, jedwab przy twarzy."),
 ("Dzień przy kamerze", "online", ["sw_ecru", "perly", "spodnie_gran", "mokasyny"], "W kadrze widać górę – to ona ma błyszczeć."),
 ("Szkolenie całodniowe", "szkolenie", ["boucle", "golf", "spodnie_gran", "baleriny"], "8 godzin na nogach, a nadal „ta, która prowadzi”."),
 ("Negocjacje i zarząd", "decyzje", ["garnitur_m", "koszula_k", "garnitur_s", "kuferek", "mokasyny"], "Jasny garnitur = przyszłam podejmować decyzje."),
 ("Networking po pracy", "event", ["plaszcz", "sukienka", "torebka", "kozaki"], "Z biurka na event bez przebierania."),
 ("Podróż służbowa", "podróż", ["plaszcz", "sw_welna", "spodnie_dzian", "mokasyny"], "Wszystko w bagażu podręcznym, nic się nie gniecie."),
 ("Weekend", "weekend", ["plaszcz", "sw_welna", "jeans", "apaszka", "mokasyny"], "Ta sama kapsuła, tylko luźniej."),
]


def fonts():
    return "".join(f'<link rel="stylesheet" href="{(FONTS / f).as_uri()}">' for f in ["fraunces/600.css", "dm-sans/400.css", "dm-sans/600.css", "caveat/600.css"])


def plansza(nr, s):
    tytul, tag, items, klucz = s
    # sloty: lewa kolumna x=300, prawa x=560 (szer. 220), wiersze od y=330
    n = len(items)
    rows = (n + 1) // 2
    top, area = 300, 860
    rh = area / rows
    sz = min(250, rh - 20)
    parts, arrows = [], []
    for i, key in enumerate(items):
        kind, col, nazwa, marka, sklad, cena, link = K[key]
        left = i % 2 == 0
        r = i // 2
        if n % 2 == 1 and i == n - 1:  # ostatni sam: na środku
            cx = 540; left = (r % 2 == 0)
        else:
            cx = 420 if left else 660
        cy = top + rh * r + rh / 2
        parts.append(f'<div style="position:absolute;left:{cx-sz/2:.0f}px;top:{cy-sz*0.6:.0f}px;width:{sz:.0f}px;height:{sz*1.2:.0f}px">{svg(kind, col)}</div>')
        ly = cy - 52
        if left:
            lx, ax1, ax2 = 36, 262, cx - sz * 0.42
            align = "right"
        else:
            lx, ax1, ax2 = 818, 818, cx + sz * 0.42
            align = "left"
        parts.append(f'''<div class="lab" style="left:{lx}px;top:{ly:.0f}px;text-align:{align}">
<div class="ln">{html.escape(nazwa)}</div><div class="lm">{html.escape(marka)}</div><div class="ls">{html.escape(sklad)}</div><div class="lc">{html.escape(cena)}</div></div>''')
        arrows.append((ax1, cy, ax2, cy, left))
    svgar = '<svg width="1080" height="1350" style="position:absolute;left:0;top:0;pointer-events:none">' + "".join(
        f'<path d="M{a} {y} C {(a+b)/2} {y-40}, {(a+b)/2} {y+20}, {b} {y}" fill="none" stroke="#FF6B3D" stroke-width="2.5" stroke-dasharray="6 5"/><circle cx="{b}" cy="{y}" r="6" fill="#FF6B3D"/>'
        for a, y, b, _, left in arrows) + "</svg>"
    return f'''<div class="pl"><div class="hd"><div class="kick">Lookbook jesień–zima 2026 · stylizacja {nr}/10</div>
<h2>{html.escape(tytul)}</h2><span class="tag">{tag}</span><p class="kl">{html.escape(klucz)}</p></div>
{svgar}{"".join(parts)}<div class="ft"><span>guliwera4.pl</span><span>ceny z 16–17.11.2026</span></div></div>'''


CSS = """<style>*{margin:0;padding:0;box-sizing:border-box}body{background:#ddd}
.pl{width:1080px;height:1350px;position:relative;background:#FFF6EE;overflow:hidden;font-family:'DM Sans';color:#3A2340}
.hd{padding:60px 70px 0}.kick{font-size:20px;letter-spacing:.16em;text-transform:uppercase;color:#7A6580;font-weight:600}
h2{font-family:'Fraunces';font-weight:600;color:#1E2A4A;font-size:64px;line-height:1.05;margin:14px 0 12px}
.tag{display:inline-block;background:#EDE5FB;border-radius:99px;padding:4px 18px;font-size:20px;font-weight:600}
.kl{font-family:'Caveat';font-weight:600;color:#FF6B3D;font-size:38px;display:inline-block;margin-left:18px}
.lab{position:absolute;width:226px;font-size:19px;line-height:1.25}
.ln{font-family:'Fraunces';font-weight:600;color:#1E2A4A;font-size:23px}.lm{font-weight:600;margin-top:3px}.ls{color:#7A6580;font-size:16px;margin-top:2px}
.lc{color:#FF6B3D;font-weight:600;font-size:22px;margin-top:3px}
.ft{position:absolute;left:70px;right:70px;bottom:40px;display:flex;justify-content:space-between;color:#7A6580;font-size:20px}
svg{display:block;width:100%;height:100%}</style>"""

OUT.mkdir(parents=True, exist_ok=True)
(OUT / "plansze.html").write_text("<html><head><meta charset='utf-8'>" + fonts() + CSS + "</head><body>" + "".join(plansza(i + 1, s) for i, s in enumerate(STYL)) + "</body></html>", encoding="utf-8")
(OUT / "plansze.json").write_text(json.dumps([{"tytul": s[0], "items": [dict(zip(["rys", "kolor", "nazwa", "marka", "sklad", "cena", "link"], K[k])) for k in s[2]]} for s in STYL], ensure_ascii=False, indent=1), encoding="utf-8")
print("ok")
