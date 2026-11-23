"""Wykresy do wpisów bloga (1200x720, kolory Guliwera 4). Użycie: python3 wykresy.py <fontsource node_modules> <katalog_html>"""
import sys, pathlib, html, math
F = pathlib.Path(sys.argv[1]) / "@fontsource"
OUT = pathlib.Path(sys.argv[2]); OUT.mkdir(parents=True, exist_ok=True)
FONTY = "".join(f'<link rel="stylesheet" href="{(F / f).as_uri()}">' for f in ["fraunces/600.css", "dm-sans/400.css", "dm-sans/600.css", "caveat/600.css"])
K = ["#FF6B3D", "#B9A5E3", "#F4799F", "#FFC84A", "#1E2A4A", "#3A2340"]

def bary(dane, maks, jedn="%", kolory=None):
    """dane: [(etykieta, wartosc, podpis_wartosci)] – poziome słupki"""
    w = ""
    for i, (et, v, pv) in enumerate(dane):
        k = (kolory or K)[i % len(kolory or K)]
        w += f'<div class="r"><div class="et">{et}</div><div class="tor"><div class="bar" style="width:{max(2, v / maks * 100):.1f}%;background:{k}"></div><span class="v">{pv}</span></div></div>'
    return f'<div class="bary">{w}</div>'

def donut(proc, kolor, srodek, opis):
    r, c = 150, 2 * math.pi * 150
    d = proc / 100 * c
    return f'''<div class="dn"><svg width="380" height="380" viewBox="0 0 380 380"><circle cx="190" cy="190" r="{r}" fill="none" stroke="#EADFD3" stroke-width="56"/>
<circle cx="190" cy="190" r="{r}" fill="none" stroke="{kolor}" stroke-width="56" stroke-dasharray="{d:.1f} {c:.1f}" transform="rotate(-90 190 190)"/>
<text x="190" y="205" text-anchor="middle" font-family="Fraunces" font-size="76" font-weight="600" fill="#3A2340">{srodek}</text></svg><p class="dop">{opis}</p></div>'''

def kolo(czesci):
    """wykres kołowy: [(etykieta, proc)]"""
    a, s = -90.0, ""
    leg = ""
    for i, (et, p) in enumerate(czesci):
        k = K[i % len(K)]
        a2 = a + p * 3.6
        x1, y1 = 190 + 170 * math.cos(math.radians(a)), 190 + 170 * math.sin(math.radians(a))
        x2, y2 = 190 + 170 * math.cos(math.radians(a2)), 190 + 170 * math.sin(math.radians(a2))
        s += f'<path d="M190 190 L{x1:.1f} {y1:.1f} A170 170 0 {1 if p > 50 else 0} 1 {x2:.1f} {y2:.1f} Z" fill="{k}" stroke="#FFF6EE" stroke-width="4"/>'
        leg += f'<li><span style="background:{k}"></span>{et} <b>{p}%</b></li>'
        a = a2
    return f'<div class="dn"><svg width="380" height="380" viewBox="0 0 380 380">{s}</svg><ul class="leg">{leg}</ul></div>'

def gantt(wiersze):
    """[(etykieta, od_tyg, do_tyg)] na 13 tygodni"""
    g = '<div class="gn"><div class="gh"><div></div>' + "".join(f"<span>{t}</span>" for t in range(1, 14)) + "</div>"
    for i, (et, a, b) in enumerate(wiersze):
        g += f'<div class="gr"><div class="et">{et}</div><div class="gt"><div class="gb" style="left:{(a - 1) / 13 * 100:.2f}%;width:{(b - a + 1) / 13 * 100:.2f}%;background:{K[i % len(K)]}"></div></div></div>'
    return g + "</div>"

def plansza(id_, kat, tytul, tresc, zrodlo):
    return f'<div class="w" id="{id_}"><div class="k">Guliwera 4 · {html.escape(kat)}</div><h1>{tytul}</h1><div class="body">{tresc}</div><div class="z">Źródło: {zrodlo}</div><div class="f">guliwera4.pl/blog</div></div>'

W = [
 plansza("bf-wzrost", "Black Friday", "Ile więcej musisz sprzedać po rabacie<br><small>(przy marży 40%)</small>",
   bary([("Rabat 10%", 33, "+33%"), ("Rabat 20%", 100, "+100%"), ("Rabat 30%", 300, "+300%")], 300),
   "obliczenia Guliwera 4: wzrost = rabat ÷ (marża − rabat)"),
 plansza("bf-konsumenci", "Black Friday", "Jak Polacy podchodzą do Black Friday 2025",
   bary([("Deklaruje udział w promocjach", 62, "62%"), ("Planuje kupić ubrania*", 75, "75%"), ("Uważa promocje BF za opłacalne", 47.6, "47,6%"), ("Obawia się manipulacji cenami", 45, "45%"), ("Wyda ponad 750 zł*", 27, "27%")], 100),
   "PayPo/IQS 2025 (N=500; *wśród kupujących), UCE Research i Shopfully 2025, Santander Consumer Bank 2025"),
 plansza("pr-przyczyny", "Podsumowanie roku", "Dlaczego firmy upadają? Najczęstsze przyczyny",
   bary([("Brak gotówki / kapitału", 38, "38%"), ("Brak potrzeby rynkowej", 35, "35%"), ("Przegrana z konkurencją", 20, "20%")], 50),
   "CB Insights, analiza 111 post-mortemów startupów (2021)"),
 plansza("pr-msp", "Podsumowanie roku", "Mała firma w liczbach (Polska)",
   '<div class="trzy">' + donut(97.2, "#FF6B3D", "97,2%", "firm to mikroprzedsiębiorstwa") + donut(74.1, "#B9A5E3", "74,1%", "PKB wytwarza sektor MŚP") + donut(68.4, "#F4799F", "68,4%", "nowych firm przetrwało 1. rok") + "</div>",
   "PARP, Raport o stanie sektora MŚP 2025 i 2026"),
 plansza("st-95", "Strategia", "Strategia, której nikt nie zna",
   '<div class="dwa">' + donut(95, "#F4799F", "95%", "pracowników nie rozumie strategii swojej organizacji") + '<div class="cyt">„Strategia na jednej stronie” odpowiada na ten problem: plan, który da się przeczytać w 2 minuty, łatwiej zrozumieć i wykonać.</div></div>',
   "R. Kaplan, D. Norton, „The Office of Strategy Management”, Harvard Business Review, 10/2005"),
 plansza("st-lejek", "Strategia", "Od celu do kalendarza: przykład przeliczenia",
   bary([("Cel marży / mies.", 10000, "10 000 zł"), ("Marża z zamówienia", 500, "500 zł → 20 zamówień"), ("Skuteczność rozmów 30%", 3000, "≈ 67 rozmów / mies."), ("Rozmowy tygodniowo", 1600, "≈ 16 / tydz.")], 10000),
   "przykład ilustracyjny Guliwera 4"),
 plansza("p90-gantt", "Plan 90 dni", "Pierwszy kwartał 2027 w 13 tygodniach",
   gantt([("Cel 1: finanse", 1, 12), ("Cel 2: newsletter", 2, 11), ("Cel 3: porządki", 1, 6), ("Korekta planu", 6, 7), ("Przegląd kwartału", 13, 13)]),
   "schemat Guliwera 4; I kw. 2027: 1.01–31.03"),
 plansza("p90-cele", "Plan 90 dni", "Zapisane cele + rozliczanie się = więcej zrealizowanych",
   '<div class="dwa">' + donut(42, "#FFC84A", "+42%", "większa szansa realizacji celów zapisanych na piśmie") + '<div class="cyt">Najlepsze wyniki w badaniu osiągnęła grupa, która zapisała cele, rozpisała działania i co tydzień wysyłała raport postępów zaufanej osobie.</div></div>',
   "G. Matthews, Dominican University of California, badanie nad wyznaczaniem celów (2015)"),
 plansza("tk-noszenie", "Zakupy z głową", "Koszt jednego noszenia",
   bary([("Płaszcz wełniany 450 zł × 60", 7.5, "7,50 zł"), ("Botki skórzane 380 zł × 80", 4.75, "4,75 zł"), ("Sweter z kaszmirem 260 zł × 40", 6.5, "6,50 zł"), ("Modna bluzka 60 zł × 2", 30, "30,00 zł")], 30, kolory=["#1E2A4A", "#B9A5E3", "#F4799F", "#FF6B3D"]),
   "przykład ilustracyjny Guliwera 4: cena ÷ liczba założeń w roku"),
 plansza("tk-rynek", "Zakupy z głową", "Off-price w Polsce i noszenie ubrań na świecie",
   bary([("TK Maxx: do 60% taniej niż ceny regularne", 60, "do 60%"), ("HalfPrice: wzrost sprzedaży II kw. 2025 r/r", 22, "+22%"), ("Spadek liczby założeń ubrania w 15 lat", 36, "−36%")], 70),
   "TK Maxx/Retailnet; Grupa CCC (Bankier, II kw. 2025); Ellen MacArthur Foundation, A New Textiles Economy"),
]

CSS = """<style>*{margin:0;padding:0;box-sizing:border-box}body{background:#ccc}
.w{width:1200px;height:720px;background:#FFF6EE;position:relative;overflow:hidden;font-family:'DM Sans';color:#3A2340;padding:56px 70px}
.k{letter-spacing:.18em;text-transform:uppercase;font-size:17px;font-weight:600;color:#E8512A}
h1{font-family:'Fraunces';font-weight:600;font-size:46px;line-height:1.1;margin:14px 0 34px}h1 small{font-size:26px;color:#7A6580}
.bary .r{display:flex;align-items:center;margin:0 0 26px}.et{width:380px;font-size:23px;font-weight:600;padding-right:20px}
.tor{flex:1;display:flex;align-items:center;gap:16px}.bar{height:46px;border-radius:0 14px 14px 0}.v{font-family:'Fraunces';font-size:30px;font-weight:600;white-space:nowrap}
.trzy,.dwa{display:flex;gap:40px;align-items:center;justify-content:center}.dn{text-align:center}.trzy .dn svg{width:300px;height:300px}
.dop{font-size:22px;font-weight:600;max-width:320px;margin:8px auto 0}.cyt{font-size:28px;line-height:1.45;max-width:520px;font-family:'Fraunces';color:#3A2340}
.leg{list-style:none;font-size:24px}.leg li{margin:10px 0}.leg span{display:inline-block;width:20px;height:20px;border-radius:5px;margin-right:10px;vertical-align:-2px}
.gn .gh,.gn .gr{display:flex;align-items:center}.gn .gh div{width:260px}.gn .gh span{flex:1;text-align:center;font-size:18px;color:#7A6580;font-weight:600}
.gn .et{width:260px;font-size:22px}.gt{flex:1;height:48px;position:relative;background:repeating-linear-gradient(90deg,#fff 0,#fff calc(100%/13 - 3px),#FFF6EE calc(100%/13 - 3px),#FFF6EE calc(100%/13));margin:9px 0;border-radius:10px}
.gb{position:absolute;top:6px;bottom:6px;border-radius:10px}
.z{position:absolute;left:70px;bottom:34px;font-size:16px;color:#7A6580;max-width:820px}.f{position:absolute;right:70px;bottom:34px;font-size:16px;color:#7A6580}</style>"""

(OUT / "wykresy.html").write_text("<html><head><meta charset='utf-8'>" + FONTY + CSS + "</head><body>" + "".join(W) + "</body></html>", encoding="utf-8")
print("ok", len(W))
