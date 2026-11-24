"""Obrazki wyróżniające 1200x800 w kolorach Guliwera 4. Użycie: python3 obrazki.py <fontsource node_modules> <katalog_html>"""
import sys, pathlib, html
F = pathlib.Path(sys.argv[1]) / "@fontsource"
OUT = pathlib.Path(sys.argv[2]); OUT.mkdir(parents=True, exist_ok=True)
FONTY = "".join(f'<link rel="stylesheet" href="{(F / f).as_uri()}">' for f in ["fraunces/600.css", "dm-sans/400.css", "dm-sans/600.css", "caveat/600.css"])

WPISY = [
 ("black-friday-mala-firma", "Biznes i strategia", "Black Friday<br>w małej firmie", "jak zarobić, nie obniżając cen", ["marża", "wartość", "oferta"], "#FF6B3D", "#FFD8C2"),
 ("podsumowanie-roku-w-firmie", "Biznes i strategia", "Podsumowanie roku<br>w małej firmie", "5 liczb, które musisz znać", ["przychód", "marża", "klienci"], "#B9A5E3", "#EDE5FB"),
 ("strategia-na-1-stronie-2027", "Biznes i strategia", "Strategia<br>na 1 stronie", "plan na 2027 w 60 minut", ["cel", "klient", "priorytety"], "#F4799F", "#FFD9E4"),
 ("plan-90-dni", "Rozwój i kariera", "Plan 90 dni", "pierwszy kwartał 2027 krok po kroku", ["3 cele", "12 tygodni", "1 przegląd"], "#FFC84A", "#FFEFC7"),
 ("szafa-kapsulowa", "Styl i wizerunek", "Szafa kapsułowa:<br>22 elementy", "ile kosztuje i ile razy ją założysz", ["105 zestawów", "budżet", "koszt noszenia"], "#B9A5E3", "#FFD9E4"),
 ("tk-maxx-half-price-jak-kupowac", "Zakupy z głową", "TK Maxx<br>i Half Price", "jak szukać perełek bez chaosu", ["metka", "skład", "dostawy"], "#1E2A4A", "#EDE5FB"),
]

def strona(slug, kat, tytul, pod, tagi, akcent, jasny):
    t = "".join(f'<span class="tag">{html.escape(x)}</span>' for x in tagi)
    return f"""<div class="w" id="{slug}"><div class="pas" style="background:{jasny}"><div class="kolo" style="background:{akcent}"></div><div class="kolo2" style="border-color:{akcent}"></div>
<div class="znak">G4</div></div><div class="t"><div class="k">Guliwera 4 · {html.escape(kat)}</div><h1>{tytul}</h1><p class="c" style="color:#E8512A">{html.escape(pod)}</p><div class="tagi">{t}</div></div><div class="f">guliwera4.pl/blog</div></div>"""

CSS = """<style>*{margin:0;padding:0;box-sizing:border-box}body{background:#ccc}
.w{width:1200px;height:800px;background:#FFF6EE;position:relative;overflow:hidden;font-family:'DM Sans';color:#3A2340;display:flex}
.pas{width:380px;height:800px;position:relative}
.kolo{position:absolute;width:420px;height:420px;border-radius:50%;left:-60px;top:190px;opacity:.9}
.kolo2{position:absolute;width:260px;height:260px;border-radius:50%;left:170px;top:60px;border:10px solid;opacity:.6}
.znak{position:absolute;left:60px;bottom:60px;font-family:'Fraunces';font-weight:600;font-size:64px;color:#3A2340}
.t{flex:1;padding:90px 70px;display:flex;flex-direction:column;justify-content:center}
.k{letter-spacing:.18em;text-transform:uppercase;font-size:18px;font-weight:600;color:#7A6580}
h1{font-family:'Fraunces';font-weight:600;color:#3A2340;font-size:76px;line-height:1.03;margin:22px 0 18px}
.c{font-family:'Caveat';font-weight:600;font-size:44px}
.tagi{margin-top:30px;display:flex;gap:12px}.tag{background:#fff;border:2px solid #EADFD3;border-radius:99px;padding:8px 20px;font-size:22px;font-weight:600}
.f{position:absolute;right:70px;bottom:44px;font-size:18px;color:#7A6580}</style>"""

(OUT / "obrazki.html").write_text("<html><head><meta charset='utf-8'>" + FONTY + CSS + "</head><body>" + "".join(strona(*w) for w in WPISY) + "</body></html>", encoding="utf-8")
print("ok")
