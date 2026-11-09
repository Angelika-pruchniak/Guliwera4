"""Generuje wektorowe logo (tekst zamieniony na krzywe) do brand/logo/svg/.
Wymaga: pip install fonttools; fonty TTF w katalogu podanym w FONTY (Google Fonts, licencja OFL).
Uruchom: FONTY=/sciezka/do/ttf python3 brand/narzedzia/generuj-logo.py"""
import os, pathlib
from fontTools.ttLib import TTFont
from fontTools.pens.svgPathPen import SVGPathPen
from fontTools.pens.transformPen import TransformPen
F = pathlib.Path(os.environ.get("FONTY", "."))
OUT = pathlib.Path(__file__).resolve().parent.parent / "logo" / "svg"; OUT.mkdir(parents=True, exist_ok=True)
C = dict(krem="#FFF6EE", koral="#FF6B3D", roz="#F4799F", zolty="#FFC84A", atrament="#3A2340", at60="#7A6580", granat="#1E2A4A")
_f = {}
def font(n):
    if n not in _f: _f[n] = TTFont(F / n)
    return _f[n]
def tekst(s, fn, size, x, y, ls=0.0, anchor="start"):
    """Zwraca (d, szerokość) ścieżki SVG dla tekstu s."""
    f = font(fn); gs = f.getGlyphSet(); cmap = f.getBestCmap(); upm = f["head"].unitsPerEm; sc = size / upm
    names = [cmap[ord(ch)] for ch in s]
    w = sum(gs[n].width * sc for n in names) + ls * (len(names) - 1)
    if anchor == "middle": x -= w / 2
    pen = SVGPathPen(gs); cx = x
    for n in names:
        gs[n].draw(TransformPen(pen, (sc, 0, 0, -sc, cx, y))); cx += gs[n].width * sc + ls
    return pen.getCommands(), w
ARCH = "M18 182 V100 A82 82 0 0 1 182 100 V182 Q182 196 168 196 H32 Q18 196 18 182 Z"
SMILE = "M50 170 Q100 184 150 168"
G4, _ = tekst("G4", "fraunces-9-700.ttf", 96, 100, 150, ls=-4, anchor="middle")
def znak(arch_fill, glyph_fill, smile=C["zolty"], defs="", knock=False, tx=0, ty=0, s=1.0, idp="g4"):
    t = f' transform="translate({tx} {ty}) scale({s})"' if (tx or ty or s != 1) else ""
    if knock:  # wersja jednokolorowa: litery i uśmiech wycięte (przezroczyste)
        return (f'<defs><mask id="{idp}m" maskUnits="userSpaceOnUse" x="0" y="0" width="200" height="200"><path d="{ARCH}" fill="#fff"/>'
                f'<path d="{G4}" fill="#000"/><path d="{SMILE}" stroke="#000" stroke-width="11" fill="none" stroke-linecap="round"/></mask></defs>'
                f'<g{t}><path d="{ARCH}" fill="{arch_fill}" mask="url(#{idp}m)"/></g>')
    return (f'{defs}<g{t}><path d="{ARCH}" fill="{arch_fill}"/><path d="{G4}" fill="{glyph_fill}"/>'
            f'<path d="{SMILE}" stroke="{smile}" stroke-width="11" fill="none" stroke-linecap="round"/></g>')
GRAD = (f'<defs><linearGradient id="g4g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="{C["koral"]}"/>'
        f'<stop offset="1" stop-color="{C["roz"]}"/></linearGradient></defs>')
def svg(name, w, h, body, bg=None, title="Guliwera 4"):
    r = f'<rect width="{w}" height="{h}" fill="{bg}"/>' if bg else ""
    (OUT / name).write_text(f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {w} {h}" width="{w}" height="{h}" role="img" aria-label="{title}"><title>{title}</title>{r}{body}</svg>\n')
# --- znak G4 (viewBox 200x200, łuk zajmuje 18–182 x 18–196) ---
svg("znak-g4.svg", 200, 200, znak("url(#g4g)", C["krem"], defs=GRAD))
svg("znak-g4-atrament.svg", 200, 200, znak(C["atrament"], None, knock=True, idp="a"))
svg("znak-g4-granat.svg", 200, 200, znak(C["granat"], None, knock=True, idp="n"))
svg("znak-g4-bialy.svg", 200, 200, znak("#FFFFFF", None, knock=True, idp="w"))
svg("znak-g4-koral.svg", 200, 200, znak(C["koral"], C["krem"]))
# --- ikona / favicon: znak z mniejszym marginesem (łuk dociągnięty do krawędzi) ---
IS = 192 / 178  # łuk dociągnięty do 4 px od krawędzi: lepiej czytelny w 16–32 px
svg("ikona.svg", 200, 200, znak("url(#g4g)", C["krem"], defs=GRAD, tx=round(100 - 100 * IS, 3), ty=round(4 - 18 * IS, 3), s=round(IS, 4)))
# --- awatar: znak na kremowym kole/kwadracie, 60% wysokości ---
svg("awatar.svg", 400, 400, znak("url(#g4g)", C["krem"], defs=GRAD, tx=41.5, ty=30.4, s=1.585), bg=C["krem"])
svg("awatar-granat.svg", 400, 400, znak("url(#g4g)", C["krem"], defs=GRAD, tx=41.5, ty=30.4, s=1.585), bg=C["granat"])
# --- logo poziome: znak 178 px wysokości łuku, nazwa Fraunces 700, podpis DM Sans ---
def poziome(name, kol_nazwa, kol_podpis, podpis, nazwa="Guliwera", cyfra=" 4", bg=None, w=None):
    s = 0.95; mx = 0  # znak: łuk 18..196 → skala 0.95
    d1, w1 = tekst(nazwa, "fraunces-72-700.ttf", 74, 196, 108, ls=-0.8)
    d2, w2 = tekst(cyfra, "fraunces-144-600.ttf", 78, 196 + w1, 108) if cyfra else ("", 0)
    body = znak("url(#g4g)", C["krem"], defs=GRAD, tx=-17*s+4, ty=-18*s+4, s=s)
    body += f'<path d="{d1}" fill="{kol_nazwa}"/>' + (f'<path d="{d2}" fill="{C["koral"]}"/>' if cyfra else "")
    wp = 0
    if podpis:
        d3, wp = tekst(podpis, "dmsans-400.ttf", 25, 198, 152)
        body += f'<path d="{d3}" fill="{kol_podpis}"/>'
    W = w or int(max(196 + w1 + w2, 198 + wp) + 8)
    svg(name, W, 178, body, bg=bg)
    return W
PODPIS = "Angelika Pruchniak-Gąszczyk · strategia i produkty cyfrowe"
poziome("logo-poziome.svg", C["atrament"], C["at60"], PODPIS)
poziome("logo-poziome-ciemne-tlo.svg", C["krem"], "#D9CCDC", PODPIS)
poziome("logo-poziome-krotkie.svg", C["atrament"], None, None)
poziome("logo-poziome-krotkie-ciemne-tlo.svg", C["krem"], None, None)
# --- logo osobiste (jak w nagłówku strony): znak + imię i nazwisko + hasło ---
def osobiste(name, k1, k2):
    s = 0.95
    d1, w1 = tekst("Angelika Pruchniak-Gąszczyk", "fraunces-72-700.ttf", 52, 196, 96, ls=-0.4)
    d2, w2 = tekst("strategia, produkty cyfrowe i dobry humor", "dmsans-400.ttf", 27, 198, 142)
    body = znak("url(#g4g)", C["krem"], defs=GRAD, tx=-17*s+4, ty=-18*s+4, s=s) + f'<path d="{d1}" fill="{k1}"/><path d="{d2}" fill="{k2}"/>'
    svg(name, int(max(196 + w1, 198 + w2) + 8), 178, body, title="Angelika Pruchniak-Gąszczyk · Guliwera 4")
osobiste("logo-osobiste.svg", C["atrament"], C["at60"])
osobiste("logo-osobiste-ciemne-tlo.svg", C["krem"], "#D9CCDC")
print("OK", sorted(p.name for p in OUT.iterdir()))
