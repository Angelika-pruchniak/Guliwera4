"""Generuje lookbook (A4, PDF) i karuzelę Instagram (1080x1350, PNG) w identyfikacji Guliwera 4.
Użycie: python3 generuj.py <katalog_fontsource_node_modules> <katalog_wyjściowy>
"""
import sys, pathlib, html

FONTS = pathlib.Path(sys.argv[1]) / "@fontsource"
OUT = pathlib.Path(sys.argv[2])
REPO = pathlib.Path(__file__).resolve().parents[3]
FOTO07 = (REPO / "brand/zdjecia-sesja/07-portret-boucle.jpg").as_uri()
FOTO09 = (REPO / "brand/zdjecia-sesja/09-kanapa-bialy-garnitur.jpg").as_uri()
LOGO = (REPO / "logo/mark.svg").as_uri()

KOL = {"granat": "#1E2A4A", "krem": "#F3E6D3", "ecru": "#EFE4D2", "camel": "#B98A5A", "błękit": "#A9C4E2",
       "burgund": "#7A2836", "biel": "#FFFFFF", "brąz": "#6B4630", "czerń": "#1C1A1F", "perły": "#F4EFE6",
       "beż": "#D9C3A5", "jeans": "#5B7393"}

STYL = [
    ("Poniedziałek w biurze", "biuro", [("granatowa marynarka", "granat"), ("biała koszula", "biel"), ("szerokie spodnie w kant", "beż"), ("skórzane mokasyny", "brąz"), ("kuferek camel", "camel")], "Spokojny granat i jedna mocna torebka."),
    ("Wystąpienie i konferencja", "scena", [("sukienka midi z kopertowym dekoltem", "granat"), ("płaszcz camel", "camel"), ("czółenka na niskim obcasie", "czerń"), ("perły", "perły")], "Jedna linia od szyi do kolan. Uwaga zostaje na twarzy i na treści."),
    ("Spotkanie z klientem", "klient", [("błękitny sweter", "błękit"), ("spódnica plisowana midi", "granat"), ("brązowe kozaki", "brąz"), ("kuferek", "camel")], "Miękko, ale z kształtem."),
    ("Kolacja biznesowa", "wieczór", [("bordowa wełniana marynarka", "burgund"), ("jedwabna bluzka", "ecru"), ("czarne spodnie", "czerń"), ("czółenka", "czerń")], "Burgund wieczorem, jedwab przy twarzy."),
    ("Dzień przy kamerze", "online", [("sweter merino ecru", "ecru"), ("perły", "perły"), ("granatowe spodnie", "granat"), ("mokasyny", "brąz")], "W kadrze widać tylko górę, więc to ona ma wyglądać najlepiej."),
    ("Szkolenie całodniowe", "szkolenie", [("kremowy żakiet bouclé", "krem"), ("granatowy golf", "granat"), ("proste spodnie", "granat"), ("płaskie czółenka", "brąz")], "Wygoda na 8 godzin stania, a nadal „ta, która prowadzi”."),
    ("Negocjacje i zarząd", "decyzje", [("jasny garnitur", "krem"), ("kremowa koszula", "ecru"), ("brązowe dodatki", "brąz")], "Jasny kolor dodaje pewności, garnitur mówi „przyszłam podejmować decyzje”."),
    ("Networking po pracy", "event", [("sukienka midi", "granat"), ("płaszcz camel", "camel"), ("kozaki", "brąz"), ("mała torebka", "burgund")], "Z biurka na event bez przebierania."),
    ("Podróż służbowa", "podróż", [("sweter wełniany", "camel"), ("spodnie z dzianiny", "granat"), ("płaszcz", "camel"), ("mokasyny", "brąz")], "Wszystko w bagażu podręcznym i nic się nie gniecie."),
    ("Weekend", "weekend", [("płaszcz camel", "camel"), ("sweter wełniany", "ecru"), ("proste jeansy", "jeans"), ("mokasyny", "brąz"), ("apaszka", "błękit")], "Ta sama kapsuła, tylko luźniej."),
]

KUP = [("Płaszcz camel", "Molton outlet", "499 zł"), ("Granatowa marynarka", "Mohito", "ok. 170 zł"),
       ("Bordowa marynarka", "TK Maxx – polowanie", "do 250 zł"), ("Sukienka midi, granat", "Ansin", "399 zł"),
       ("Sweter ecru (merino)", "Wólczanka", "99,99 zł"), ("Sweter wełniany", "Solar", "299 zł"),
       ("Spodnie w kant", "Reserved", "139,99 zł"), ("Spódnica plisowana", "TK Maxx / Half Price", "do 150 zł"),
       ("Czółenka", "Eva Minge", "ok. 189 zł"), ("Kozaki skórzane", "Half Price – polowanie", "do 350 zł"),
       ("Mokasyny skórzane", "Gino Rossi", "ok. 230 zł"), ("Kuferek", "Eva Minge", "ok. 256 zł"),
       ("Perły", "pracownia / TK Maxx", "do 100 zł")]
MASZ = ["biała koszula", "bluzka satynowa", "granatowy golf", "sweter na weekend", "proste jeansy",
        "czarne spodnie", "trencz", "ciemna sukienka", "botki"]


def css(page_w, page_h):
    fonts = "".join(f'<link rel="stylesheet" href="{(FONTS / f).as_uri()}">' for f in
                    ["fraunces/400.css", "fraunces/600.css", "fraunces/400-italic.css", "dm-sans/400.css", "dm-sans/600.css", "caveat/600.css"])
    return fonts + f"""<style>
@page{{size:{page_w} {page_h};margin:0}}
*{{box-sizing:border-box;margin:0;padding:0}}
body{{font-family:'DM Sans';color:#3A2340;-webkit-print-color-adjust:exact;print-color-adjust:exact}}
.p{{width:{page_w};height:{page_h};position:relative;overflow:hidden;background:#FFF6EE;page-break-after:always;padding:var(--pad)}}
h1,h2,h3,.f{{font-family:'Fraunces';font-weight:600;color:#1E2A4A;line-height:1.08}}
.c{{font-family:'Caveat';font-weight:600;color:#FF6B3D}}
.kick{{text-transform:uppercase;letter-spacing:.18em;font-size:var(--small);color:#7A6580;font-weight:600}}
.stopka{{position:absolute;left:var(--pad);right:var(--pad);bottom:calc(var(--pad)*.6);display:flex;justify-content:space-between;font-size:var(--small);color:#7A6580}}
.sw{{display:flex;align-items:center;gap:.8em;margin:.45em 0}}
.dot{{width:var(--dot);height:var(--dot);border-radius:50%;flex:none;border:1px solid rgba(58,35,64,.15)}}
.tag{{display:inline-block;background:#EDE5FB;border-radius:99px;padding:.15em .8em;font-size:var(--small);font-weight:600}}
.karta{{background:#fff;border-radius:18px;padding:1.2em 1.4em}}
table{{width:100%;border-collapse:collapse}}
td{{padding:.42em .3em;border-bottom:1px solid #EADFD3;vertical-align:top}}
.r{{text-align:right;white-space:nowrap}}
</style>"""


def strip_kolorow(items):
    return '<div style="display:flex;height:100%">' + "".join(
        f'<div style="flex:1;background:{KOL[k]}"></div>' for _, k in items) + "</div>"


def stylizacja(i, s, big):
    tytul, tag, items, klucz = s
    sw = "".join(f'<div class="sw"><span class="dot" style="background:{KOL[k]}"></span>{html.escape(n)}</div>' for n, k in items)
    return f"""<div class="karta" style="display:flex;gap:1.2em;{'height:100%' if big else ''}">
<div style="width:{'28%' if not big else '30%'};border-radius:12px;overflow:hidden;min-height:{'auto' if big else '9em'}">{strip_kolorow(items)}</div>
<div style="flex:1"><div class="kick">Stylizacja {i} · <span class="tag">{tag}</span></div>
<h3 style="font-size:{'2.1em' if big else '1.5em'};margin:.25em 0 .4em">{html.escape(tytul)}</h3>{sw}
<p style="margin-top:.6em"><b>Klucz:</b> {html.escape(klucz)}</p></div></div>"""


def lookbook():
    P = []
    stop = lambda n: f'<div class="stopka"><span>Lookbook jesień–zima 2026 · guliwera4.pl</span><span>{n}</span></div>'
    P.append(f"""<div class="p" style="padding:0;background:#1E2A4A">
<img src="{FOTO07}" style="position:absolute;right:0;top:0;width:58%;height:100%;object-fit:cover">
<div style="position:absolute;left:0;top:0;width:46%;height:100%;padding:18mm 12mm;display:flex;flex-direction:column;justify-content:space-between;color:#FFF6EE">
<div class="kick" style="color:#B9A5E3">Guliwera 4 · Lookbook</div>
<div><h1 style="color:#FFF6EE;font-size:46pt">Jesień–<br>zima<br>2026</h1>
<p style="font-size:14pt;margin-top:6mm">10 stylizacji do pracy<br>i kapsuła za 3 000 zł</p>
<p class="c" style="font-size:22pt;margin-top:8mm">klasyka, która pracuje za Ciebie</p></div>
<div style="font-size:10pt">Angelika Pruchniak-Gąszczyk<br>guliwera4.pl</div></div></div>""")
    pal = "".join(f'<div style="text-align:center"><div style="width:26mm;height:26mm;border-radius:50%;background:{KOL[k]};margin:0 auto 3mm;border:1px solid #e6d9c8"></div>{n}</div>'
                  for n, k in [("granat", "granat"), ("krem", "krem"), ("camel", "camel"), ("błękit", "błękit"), ("burgund", "burgund"), ("perły", "perły")])
    P.append(f"""<div class="p"><div class="kick">Wstęp</div>
<h2 style="font-size:34pt;margin:6mm 0 8mm">Klasyka, która<br>pracuje za Ciebie</h2>
<p style="font-size:13pt;line-height:1.6;max-width:150mm">Inspiracja to elegancja w duchu „old money”: klasyczne kroje, dobre tkaniny, spokojne kolory i jeden kobiecy akcent – perły, apaszka, sukienka midi. Taki styl ma wielką zaletę biznesową: <b>nie odciąga uwagi od tego, co mówisz</b>. Pięć kolorów, które łączą się ze sobą, daje dziesiątki zestawów z kilkunastu rzeczy.</p>
<h3 style="font-size:18pt;margin:14mm 0 7mm">Paleta sezonu</h3>
<div style="display:flex;justify-content:space-between;font-size:11pt">{pal}</div>
<p class="c" style="font-size:26pt;margin-top:16mm">Najpierw krój i tkanina,<br>dopiero potem marka.</p>{stop(2)}</div>""")
    n = 3
    for a in range(0, 10, 2):
        if a == 6:  # stylizacja 7 ze zdjęciem
            P.append(f"""<div class="p" style="display:flex;flex-direction:column;gap:8mm">{stylizacja(7, STYL[6], False)}
<img src="{FOTO09}" style="width:100%;height:120mm;object-fit:cover;object-position:center 30%;border-radius:18px">
{stylizacja(8, STYL[7], False)}{stop(n)}</div>""")
        else:
            P.append(f"""<div class="p" style="display:flex;flex-direction:column;gap:10mm"><div class="kick">Stylizacje</div>
<div style="flex:1;max-height:118mm">{stylizacja(a+1, STYL[a], True)}</div>
<div style="flex:1;max-height:118mm">{stylizacja(a+2, STYL[a+1], True)}</div>{stop(n)}</div>""")
        n += 1
    rows = "".join(f"<tr><td>{e}</td><td style='color:#7A6580'>{m}</td><td class='r'>{c}</td></tr>" for e, m, c in KUP)
    P.append(f"""<div class="p"><div class="kick">Kapsuła</div><h2 style="font-size:30pt;margin:4mm 0 3mm">22 elementy za 3 000 zł</h2>
<p style="font-size:11.5pt;margin-bottom:5mm">13 rzeczy kupujesz, 9 pewnie już masz w szafie.</p>
<table style="font-size:10.5pt">{rows}
<tr><td>Rezerwa: krawcowa i szewc</td><td></td><td class='r'>167 zł</td></tr>
<tr><td style="border:0;padding-top:3mm"><b class="f" style="font-size:14pt">Razem</b></td><td style="border:0"></td><td class='r f' style="border:0;padding-top:3mm;font-size:14pt">3 000 zł</td></tr></table>
<div class="karta" style="margin-top:6mm;background:#EDE5FB"><b>Masz w szafie (9):</b> {", ".join(MASZ)}.</div>
<p style="font-size:8.5pt;color:#7A6580;margin-top:4mm">Ceny sprawdzone 16–17.11.2026, mogą się zmienić. Linki do produktów we wpisie na guliwera4.pl.</p>{stop(n)}</div>""")
    n += 1
    prem = [("Płaszcz ze 100% wełny", "Molton, dyplomatka", "1 149 zł"), ("Czółenka skórzane", "Kazar, SAVRIA", "549 zł"),
            ("Kuferek ze skóry", "Kazar, INEZ", "949 zł"), ("Kozaki skórzane", "Ryłko", "649,99 zł")]
    prem_h = "".join(f'<div class="karta" style="margin-bottom:5mm;display:flex;justify-content:space-between;align-items:center"><div><h3 style="font-size:16pt">{a}</h3><div style="color:#7A6580">{b}</div></div><div class="f" style="font-size:18pt">{c}</div></div>' for a, b, c in prem)
    P.append(f"""<div class="p"><div class="kick">Gdy trafi się okazja</div><h2 style="font-size:30pt;margin:4mm 0 10mm">Wersja premium</h2>{prem_h}
<p class="c" style="font-size:24pt;margin-top:10mm">Inwestuj w to, co nosisz najczęściej:<br>płaszcz, buty, torebka.</p>{stop(n)}</div>""")
    n += 1
    P.append(f"""<div class="p"><div class="kick">Zakupy z głową</div><h2 style="font-size:30pt;margin:4mm 0 8mm">Jak kupić taniej</h2>
<div class="karta" style="margin-bottom:5mm"><h3 style="font-size:17pt;margin-bottom:2mm">TK Maxx</h3><p>Nowości kilka razy w tygodniu, zwykle od wtorku do czwartku. Każdy sklep ma własny rytm, więc zapytaj obsługę o dni dostaw. W większych sklepach jest dział marek premium. Zwrot z paragonem do 28 dni.</p></div>
<div class="karta" style="margin-bottom:5mm"><h3 style="font-size:17pt;margin-bottom:2mm">Half Price</h3><p>Końcówki kolekcji i nadwyżki znanych marek (grupa CCC). W zakładce „Wyprzedaż” nawet do –80%. Zwrot często na kartę podarunkową, a termin zależy od kanału zakupu, więc sprawdź regulamin.</p></div>
<div class="karta" style="background:#FFEFC7"><h3 style="font-size:17pt;margin-bottom:2mm">Kalendarz</h3><p>Black Friday 27.11 · wyprzedaże od 27.12 · druga fala w styczniu · outlety marek cały rok.</p></div>{stop(n)}</div>""")
    n += 1
    chk = [("Skład", "płaszcz i sweter: wełna lub kaszmir min. 50%"), ("Szwy", "proste, gęste, bez wystających nitek"),
           ("Podszewka", "w płaszczu i marynarce"), ("Guziki", "przyszyte na nóżce, z zapasowym"),
           ("Ramiona", "szew kończy się tam, gdzie Twoje ramię"), ("Rękaw", "do kości nadgarstka"),
           ("Pielęgnacja", "„tylko pranie chemiczne” to dodatkowy koszt")]
    chk_h = "".join(f'<div style="display:flex;gap:5mm;padding:4mm 0;border-bottom:1px solid #EADFD3"><span style="width:8mm;height:8mm;border:2px solid #FF6B3D;border-radius:3px;flex:none"></span><div><b>{a}</b> – {b}</div></div>' for a, b in chk)
    P.append(f"""<div class="p"><div class="kick">Zakupy z głową</div><h2 style="font-size:30pt;margin:4mm 0 8mm">Checklista przy wieszaku</h2>
<div style="font-size:13pt">{chk_h}</div>
<div class="karta" style="margin-top:8mm;background:#EDE5FB">Sweter „z kaszmirem” bywa w 5% kaszmirem, a w 65% wiskozą i poliamidem. <b>Zawsze czytaj metkę</b>, nie nazwę.</div>{stop(n)}</div>""")
    n += 1
    P.append(f"""<div class="p"><div class="kick">Liczby</div><h2 style="font-size:30pt;margin:4mm 0 10mm">Koszt jednego noszenia</h2>
<div style="display:flex;gap:6mm">
<div class="karta" style="flex:1;text-align:center"><div class="kick">Płaszcz</div><div class="f" style="font-size:44pt;margin:4mm 0">6,24 zł</div><div>499 zł ÷ 80 wyjść<br>(4× w tygodniu, 20 tygodni)</div></div>
<div class="karta" style="flex:1;text-align:center"><div class="kick">Sukienka „na raz”</div><div class="f" style="font-size:44pt;margin:4mm 0;color:#FF6B3D">149,50 zł</div><div>299 zł ÷ 2 wyjścia</div></div></div>
<p style="font-size:13pt;margin-top:10mm;line-height:1.6">Ta sama logika co w firmie: liczy się <b>koszt jednostkowy</b>, nie cena na metce.</p>
<p class="c" style="font-size:26pt;margin-top:8mm">Droższe nie znaczy drogie,<br>jeśli nosisz to cały sezon.</p>{stop(n)}</div>""")
    n += 1
    P.append(f"""<div class="p" style="background:#1E2A4A;color:#FFF6EE;display:flex;flex-direction:column;justify-content:center">
<div class="kick" style="color:#B9A5E3">Liczysz w szafie? Policz też w firmie</div>
<h2 style="color:#FFF6EE;font-size:34pt;margin:6mm 0 8mm">Akademia Guliwera</h2>
<p style="font-size:14pt;line-height:1.6;max-width:150mm">Krótkie codzienne lekcje strategii i finansów dla właścicielek firm i młodych managerek.</p>
<p style="margin-top:8mm;font-size:14pt"><span style="background:#FF6B3D;color:#fff;padding:3mm 7mm;border-radius:99px;font-weight:600">guliwera4.pl/aplikacja</span></p>
<p style="margin-top:12mm;font-size:12pt">Narzędzia do planowania: <b>guliwera4.pl/materialy</b></p>
<p class="c" style="font-size:26pt;margin-top:16mm;color:#FFC84A">strategia, produkty cyfrowe i dobry humor</p>
<p style="position:absolute;bottom:12mm;font-size:8.5pt;color:#B9A5E3">Ceny sprawdzone 16–17.11.2026. Bez linków afiliacyjnych i płatnej współpracy. Stylizacje inspirowane klasyczną elegancją, nie odnoszą się do konkretnych osób.</p></div>""")
    return "<html><head><meta charset='utf-8'>" + css("210mm", "297mm") + "<style>:root{--pad:16mm;--small:8.5pt;--dot:6mm}body{font-size:11.5pt}</style></head><body>" + "".join(P) + "</body></html>"


def karuzela():
    S = []
    stop = lambda n: f'<div class="stopka"><span>Guliwera 4 · guliwera4.pl</span><span>{n}/10</span></div>'
    S.append(f"""<div class="p" style="padding:0;background:#1E2A4A"><img src="{FOTO07}" style="width:100%;height:62%;object-fit:cover;object-position:center 20%">
<div style="padding:50px 70px;color:#FFF6EE"><div class="kick" style="color:#B9A5E3">Lookbook jesień–zima 2026</div>
<h1 style="color:#FFF6EE;font-size:78px;margin:18px 0">10 stylizacji do pracy</h1><p style="font-size:34px">+ kapsuła za 3 000 zł <span class="c" style="font-size:44px;color:#FFC84A">→ przesuń</span></p></div></div>""")
    for k, i in enumerate([0, 1, 2, 3, 5, 6]):
        s = STYL[i]
        S.append(f'<div class="p" style="display:flex;flex-direction:column;justify-content:center;font-size:40px;--dot:46px"><div style="height:1000px">{stylizacja(i+1, s, True)}</div>{stop(k+2)}</div>')
    rows = "".join(f"<tr><td>{e}</td><td class='r'>{c}</td></tr>" for e, m, c in KUP)
    S.append(f"""<div class="p"><div class="kick">Kapsuła</div><h2 style="font-size:58px;margin:10px 0 20px">13 zakupów = 3 000 zł</h2>
<table style="font-size:27px">{rows}<tr><td>rezerwa: krawcowa, szewc</td><td class='r'>167 zł</td></tr></table>{stop(8)}</div>""")
    S.append(f"""<div class="p"><div class="kick">Zakupy z głową</div><h2 style="font-size:62px;margin:10px 0 40px">Zanim kupisz, sprawdź:</h2>
<div style="font-size:38px;line-height:1.9">☐ skład: min. 50% wełny<br>☐ szwy i podszewka<br>☐ guziki na nóżce<br>☐ szew ramienia<br>☐ długość rękawa<br>☐ koszt jednego noszenia</div>{stop(9)}</div>""")
    S.append(f"""<div class="p" style="background:#1E2A4A;color:#FFF6EE;display:flex;flex-direction:column;justify-content:center">
<h2 style="color:#FFF6EE;font-size:70px">Pełny lookbook z linkami i cenami</h2><p style="font-size:38px;margin:30px 0">na blogu: <b>guliwera4.pl</b></p>
<p class="c" style="font-size:56px;color:#FFC84A">zapisz, żeby nie zgubić ♡</p>{stop(10)}</div>""")
    return "<html><head><meta charset='utf-8'>" + css("1080px", "1350px") + "<style>:root{--pad:70px;--small:22px;--dot:34px}body{font-size:30px}</style></head><body>" + "".join(S) + "</body></html>"


OUT.mkdir(parents=True, exist_ok=True)
(OUT / "lookbook.html").write_text(lookbook(), encoding="utf-8")
(OUT / "karuzela.html").write_text(karuzela(), encoding="utf-8")
print("ok")
