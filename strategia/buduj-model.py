# Model Guliwera 4 – produkty cyfrowe jako dźwignia (wersja 2). Uruchom: python3 buduj-model.py plik.xlsx
import sys, openpyxl
from openpyxl.styles import Font, PatternFill, Alignment
from openpyxl.workbook.defined_name import DefinedName
from openpyxl.utils import get_column_letter as L
OUT=sys.argv[1]
wb=openpyxl.Workbook()
F=Font(name="Arial",size=10); FB=Font(name="Arial",size=10,bold=True); FH=Font(name="Arial",size=12,bold=True,color="3A2340"); FIN=Font(name="Arial",size=10,color="0000FF")
INP=PatternFill("solid",fgColor="FFEFC7"); HEAD=PatternFill("solid",fgColor="FFD9E4"); KEY=PatternFill("solid",fgColor="EDE5FB")
ZL='#,##0" zł";[Red]-#,##0" zł"'; PCT='0.0%'; NUM='#,##0.0'; INT='#,##0'
def nm(n,ref): wb.defined_names[n]=DefinedName(n,attr_text=ref)
SC=["Ostrożny","Bazowy","Ambitny"]; SCOL=["C","D","E"]
# ---------------- Jak czytać
R=wb.active; R.title="Jak czytać"
for i,t in enumerate([
"Guliwera 4 – model 2027: produkty cyfrowe jako dźwignia zysku",
"Logika: ruch → lista mailowa (Akademia + newsletter) → produkt wejściowy 39 zł → szablony/PDF → Pakiet 297 zł → kurs wideo → Klub (abonament).",
"Twój czas jest stałym limitem: 2 soboty w miesiącu (warsztat online dla grupy + Q&A Klubu / nagrywanie) i 2–3 spotkania 1:1 po 1 h tygodniowo.",
"Żółte pola z niebieską czcionką = dane wejściowe. Kolumny C/D/E w Parametrach = scenariusz ostrożny / bazowy / ambitny.",
"Arkusze M-Ostrożny, M-Bazowy, M-Ambitny liczą każdy miesiąc (XII 2026 + 2027). Wynik i EBITDA porównuje scenariusze. KPI 2027 pokazuje scenariusz wybrany w Parametrach.",
"EBITDA w JDG = przychód − koszty działalności; ryczałt i składki właścicielki są pod EBITDA. 🧾 = potwierdzić z księgową. Domyślne liczby to ZAŁOŻENIA (badania rynku), nie dane firmy.",
],1): R.cell(i,1,t).font=FH if i==1 else F
R.column_dimensions["A"].width=160
# ---------------- Parametry
P=wb.create_sheet("Parametry")
P["A1"]="PARAMETRY (C = ostrożny, D = bazowy, E = ambitny; jeśli wypełniona tylko kolumna C – wartość wspólna)"; P["A1"].font=FH
for i,h in enumerate(["Parametr","Symbol",*SC,"Źródło / uwaga"],1):
    c=P.cell(3,i,h); c.font=FB; c.fill=HEAD
rows=[
 ("— Wybór —",),
 ("Scenariusz w arkuszu KPI (1/2/3)","scen",[2],INT,""),
 ("— Ruch i lista mailowa —",),
 ("Odwiedziny strony w XII 2026","ruch0",[300,500,800],INT,"dziś ok. 300/mies. (Jetpack: 144 odsłony / 2 tyg.)"),
 ("Wzrost ruchu miesiąc do miesiąca","ruch_g",[0.08,0.12,0.18],PCT,"SEO + LinkedIn + posty (agent social media)"),
 ("Zapis na listę (konto w Akademii / newsletter) z odwiedzin","optin",[0.03,0.04,0.06],PCT,"lead magnet: Akademia, darmowy kalkulator"),
 ("Lista na start (1.12.2026)","lista0",[100,150,200],INT,"konta Akademii + kontakty; wpisz prawdziwą liczbę"),
 ("Wypisy miesięcznie","wypis",[0.02],PCT,""),
 ("Reklamy miesięcznie – kupujemy zapisy, nie sprzedaż","ads",[300,600,1200],ZL,"Meta lead / zapis do Akademii"),
 ("Koszt pozyskania 1 zapisu z reklamy (CPL)","cpl",[10,7,5],ZL,"PL, lead magnet B2C: zwykle 4–12 zł"),
 ("— Konwersje (lista → zakup) —",),
 ("Produkt wejściowy 39 zł: % nowych zapisów kupujących","k_trip",[0.04,0.06,0.08],PCT,"tripwire po zapisie; rynek: kilka %"),
 ("Szablony / pojedyncze PDF: % listy kupujących w miesiącu","k_pdf",[0.004,0.007,0.01],PCT,""),
 ("Pakiet 297 zł: % listy kupujących w miesiącu","k_pak",[0.002,0.004,0.006],PCT,""),
 ("Kurs wideo: % listy w miesiącu premiery (okno sprzedaży)","k_kurs_l",[0.01,0.02,0.03],PCT,"rynek: 1–3% listy na premierę"),
 ("Kurs wideo: % listy poza premierą (sprzedaż ciągła)","k_kurs_e",[0.0005,0.001,0.0015],PCT,"rynek: zwykle 1–2% rocznie"),
 ("Klub: % listy dołączających miesięcznie","k_klub",[0.003,0.005,0.008],PCT,""),
 ("Klub: rezygnacje miesięcznie","churn",[0.10,0.08,0.06],PCT,"członkostwa: 5–10%/mies."),
 ("Warsztat sobotni: % listy zapisujących się","k_wars",[0.015,0.02,0.025],PCT,""),
 ("Sesje 1:1 – popyt miesięcznie","sesje",[2,4,8],INT,"limit czasu niżej"),
 ("Licencje B2B Akademii + kursu (rocznie)","lic",[1,3,6],INT,"dla zespołów HR/L&D, bez Twojego czasu"),
 ("— Ceny (B2C brutto, B2B netto) —",),
 ("Produkt wejściowy (Strategia na 1 stronie)","c_trip",[39],ZL,""),
 ("Średni koszyk szablonów / PDF","c_pdf",[69],ZL,"szablony Excel/Canva/Notion 49–99 zł"),
 ("Pakiet Kierunek Zysk","c_pak",[297],ZL,""),
 ("Kurs wideo (nagrany, dostęp na zawsze)","c_kurs",[697],ZL,"rynek: kursy nagrane ok. 100–150 USD w USA; w PL 497–997 zł"),
 ("Klub Kierunek Zysk – miesięcznie","c_klub",[49],ZL,"rynek: członkostwa ok. 61 USD średnio (Stan 2025)"),
 ("Warsztat sobotni online – za osobę","c_wars",[249],ZL,""),
 ("Sesja 1:1 60 min (netto)","c_ses",[1200],ZL,"cena ze strony"),
 ("Licencja B2B dla zespołu (netto, rok)","c_lic",[4900],ZL,"np. do 15 osób"),
 ("— Kalendarz produktów (numer miesiąca 2027) —",),
 ("Pierwsza premiera kursu wideo (miesiąc)","m_kurs",[4],INT,"I–III: nagrywanie w soboty"),
 ("Premiery kursu w roku: miesiące","m_kurs_lista",["4,6,9,11"],None,"miesiące okien sprzedaży"),
 ("Start Klubu (miesiąc)","m_klub",[5],INT,""),
 ("Start warsztatów sobotnich (miesiąc)","m_wars",[4],INT,"I–III soboty idą na nagrania"),
 ("— Twój czas (twarde limity) —",),
 ("Soboty na firmę w miesiącu","sob",[2],INT,"Twoja decyzja"),
 ("Godziny w jednej sobocie","h_sob",[6],INT,""),
 ("Max spotkań 1:1 miesięcznie (2–3/tydz.)","max_ses",[10],INT,""),
 ("Miejsca na warsztacie sobotnim","max_wars",[25],INT,"online, grupa"),
 ("Godziny warsztatu (z przygotowaniem)","h_wars",[6],INT,"zajmuje całą sobotę"),
 ("Godziny Q&A Klubu w miesiącu (w drugą sobotę)","h_klub",[2],INT,""),
 ("Godziny nagrania kursu łącznie","h_kurs",[36],INT,"3 soboty × 2 miesiące = 36 h"),
 ("Godziny marketingu tygodniowo POZA sobotami (posty, newsletter)","h_mark",[2],NUM,"⚠ tego czasu nie masz – agent social media + szablony maili"),
 ("— Koszty —",),
 ("Opłaty za płatność (karta/BLIK/P24)","oplata",[0.02],PCT,""),
 ("Księgowa","k_ksieg",[300],ZL,"wpisz swoje"),
 ("WordPress.com + domena + poczta (miesięcznie)","k_wp",[150],ZL,"wpisz swoje"),
 ("Narzędzia: Mailchimp, Canva, Metricool, wideo (miesięcznie)","k_narz",[250],ZL,"Mailchimp rośnie z listą"),
 ("Montaż wideo / asystentka (miesięcznie)","k_vva",[300,500,800],ZL,"kupujesz czas, którego nie masz"),
 ("Inne (telefon, sprzęt, mikrofon, światło)","k_inne",[150],ZL,""),
 ("Koszty jednorazowe 2027 (prawnik: regulaminy, sprzęt nagraniowy)","jedn",[2500],ZL,""),
 ("— Podatki i składki 🧾 —",),
 ("VAT aktywny? (1 = tak). Sesje 1:1 jako doradztwo mogą wyłączać zwolnienie","vat",[1],INT,"art. 113 ust. 13 ustawy o VAT 🧾"),
 ("Stawka VAT dla produktów cyfrowych","vat_st",[0.23],PCT,"e-book z ISBN może mieć 5% 🧾"),
 ("Limit zwolnienia podmiotowego","vat_lim",[200000],ZL,"sprawdzić aktualny limit 🧾"),
 ("Ryczałt – produkty cyfrowe, kurs, warsztat","r_dig",[0.085],PCT,"🧾 zależy od PKWiU"),
 ("Ryczałt – sesje 1:1 (doradztwo)","r_dor",[0.15],PCT,"🧾"),
 ("ZUS społeczny miesięcznie","zus",[0],ZL,"0 przy etacie ≥ minimalna krajowa (zbieg tytułów) 🧾"),
 ("Zdrowotna: przychód do 60 tys. / 60–300 tys. / >300 tys. (C/D/E)","zdr",[498,831,1495],ZL,"progi ryczałtu 2026, szacunek 🧾 – tu kolumny to progi, nie scenariusze"),
 ("Gotówka na start","got0",[3000],ZL,"wpisz prywatnie"),
 ("Koszt alternatywny Twojej godziny","stawka",[250],ZL,""),
]
r=4; REF={}
for row in rows:
    if len(row)==1:
        P.cell(r,1,row[0]).font=FB; P.cell(r,1).fill=HEAD; r+=1; continue
    lab,sym,vals,fmt,note=row
    P.cell(r,1,lab); P.cell(r,2,sym)
    for j,v in enumerate(vals):
        c=P.cell(r,3+j,v); c.fill=INP; c.font=FIN
        if fmt: c.number_format=fmt
    P.cell(r,6,note)
    REF[sym]=(r,len(vals)); r+=1
P.column_dimensions["A"].width=66; P.column_dimensions["B"].width=12
for c in "CDE": P.column_dimensions[c].width=12
P.column_dimensions["F"].width=70
def p(sym,s):  # absolute ref to parameter for scenario index s (0..2)
    row,n=REF[sym]; col=SCOL[s] if n==3 and sym!="zdr" else "C"
    return f"Parametry!${col}${row}"
def zdr(i): return f"Parametry!${SCOL[i]}${REF['zdr'][0]}"
# ---------------- monthly sheets
months=["XII 2026","I","II","III","IV","V","VI","VII","VIII","IX","X","XI","XII"]
LINES={}
def build_month(s):
    ws=wb.create_sheet(f"M-{SC[s]}")
    ws["A1"]=f"Miesiące – scenariusz {SC[s].lower()}"; ws["A1"].font=FH
    ws.cell(3,1,"Miesiąc").font=FB
    for i,m in enumerate(months):
        c=ws.cell(3,2+i,m); c.font=FB; c.fill=HEAD
        ws.cell(4,2+i,i)
    ws.cell(3,15,"Rok 2027").font=FB; ws.cell(4,1,"Nr miesiąca (0 = XII 2026)")
    spec=[
     ("odw","Odwiedziny strony", lambda c,pc,i: f"={p('ruch0',s)}*(1+{p('ruch_g',s)})^{c}4", INT, True),
     ("nowi_o","Nowe zapisy z ruchu", lambda c,pc,i: f"={c}{{odw}}*{p('optin',s)}", INT, True),
     ("nowi_a","Nowe zapisy z reklam", lambda c,pc,i: f"=IF({p('cpl',s)}>0,{p('ads',s)}/{p('cpl',s)},0)", INT, True),
     ("wyp","Wypisy", lambda c,pc,i: (f"={p('lista0',s)}*{p('wypis',s)}" if i==0 else f"={pc}{{lista}}*{p('wypis',s)}"), INT, True),
     ("lista","Lista mailowa (koniec miesiąca)", lambda c,pc,i: (f"={p('lista0',s)}+{c}{{nowi_o}}+{c}{{nowi_a}}-{c}{{wyp}}" if i==0 else f"={pc}{{lista}}+{c}{{nowi_o}}+{c}{{nowi_a}}-{c}{{wyp}}"), INT, False),
     ("q_trip","Sprzedaż: produkt wejściowy 39 zł (szt.)", lambda c,pc,i: f"=({c}{{nowi_o}}+{c}{{nowi_a}})*{p('k_trip',s)}", NUM, True),
     ("q_pdf","Sprzedaż: szablony / PDF (szt.)", lambda c,pc,i: f"={c}{{lista}}*{p('k_pdf',s)}", NUM, True),
     ("q_pak","Sprzedaż: Pakiet 297 zł (szt.)", lambda c,pc,i: f"={c}{{lista}}*{p('k_pak',s)}", NUM, True),
     ("q_kurs","Sprzedaż: kurs wideo (szt.)", lambda c,pc,i: f'=IF(AND({c}4>=1,{c}4>={p("m_kurs",s)}),{c}{{lista}}*IF(ISNUMBER(SEARCH(","&{c}4&",",","&{p("m_kurs_lista",s)}&",")),{p("k_kurs_l",s)},{p("k_kurs_e",s)}),0)', NUM, True),
     ("klub_n","Klub: nowi członkowie", lambda c,pc,i: f"=IF(AND({c}4>=1,{c}4>={p('m_klub',s)}),{c}{{lista}}*{p('k_klub',s)},0)", NUM, True),
     ("klub","Klub: aktywni członkowie (koniec miesiąca)", lambda c,pc,i: ("=0" if i==0 else f"={pc}{{klub}}*(1-{p('churn',s)})+{c}{{klub_n}}"), NUM, False),
     ("q_wars","Warsztat sobotni: uczestnicy", lambda c,pc,i: f"=IF(AND({c}4>=1,{c}4>={p('m_wars',s)}),MIN({p('max_wars',s)},{c}{{lista}}*{p('k_wars',s)}),0)", NUM, True),
     ("q_ses","Sesje 1:1 (szt.)", lambda c,pc,i: f"=MIN({p('sesje',s)},{p('max_ses',s)})", NUM, True),
     ("q_lic","Licencje B2B (szt.)", lambda c,pc,i: ("=0" if i==0 else f"=IF(OR({c}4=5,{c}4=9,{c}4=11),{p('lic',s)}/3,0)"), NUM, True),
     ("vatf","Współczynnik netto B2C (1 lub 1/(1+VAT))", lambda c,pc,i: f"=IF({p('vat',s)}=1,1/(1+{p('vat_st',s)}),1)", '0.000', False),
     ("r_trip","Przychód: produkt wejściowy", lambda c,pc,i: f"={c}{{q_trip}}*{p('c_trip',s)}*{c}{{vatf}}", ZL, True),
     ("r_pdf","Przychód: szablony / PDF", lambda c,pc,i: f"={c}{{q_pdf}}*{p('c_pdf',s)}*{c}{{vatf}}", ZL, True),
     ("r_pak","Przychód: Pakiet", lambda c,pc,i: f"={c}{{q_pak}}*{p('c_pak',s)}*{c}{{vatf}}", ZL, True),
     ("r_kurs","Przychód: kurs wideo", lambda c,pc,i: f"={c}{{q_kurs}}*{p('c_kurs',s)}*{c}{{vatf}}", ZL, True),
     ("r_klub","Przychód: Klub (MRR)", lambda c,pc,i: f"={c}{{klub}}*{p('c_klub',s)}*{c}{{vatf}}", ZL, True),
     ("r_wars","Przychód: warsztat sobotni", lambda c,pc,i: f"={c}{{q_wars}}*{p('c_wars',s)}*{c}{{vatf}}", ZL, True),
     ("r_ses","Przychód: sesje 1:1", lambda c,pc,i: f"={c}{{q_ses}}*{p('c_ses',s)}", ZL, True),
     ("r_lic","Przychód: licencje B2B", lambda c,pc,i: f"={c}{{q_lic}}*{p('c_lic',s)}", ZL, True),
     ("rev","PRZYCHÓD NETTO", lambda c,pc,i: f"=SUM({c}{{r_trip}}:{c}{{r_lic}})", ZL, True),
     ("rev_cyf","w tym produkty cyfrowe (bez Twojego czasu)", lambda c,pc,i: f"={c}{{r_trip}}+{c}{{r_pdf}}+{c}{{r_pak}}+{c}{{r_kurs}}+{c}{{r_klub}}+{c}{{r_lic}}", ZL, True),
     ("fee","Opłaty za płatność", lambda c,pc,i: f"=({c}{{rev}}-{c}{{r_lic}})*{p('oplata',s)}", ZL, True),
     ("adsr","Reklamy", lambda c,pc,i: f"={p('ads',s)}", ZL, True),
     ("stale","Koszty stałe (księgowa, WP, narzędzia, montaż/asystentka, inne)", lambda c,pc,i: f"={p('k_ksieg',s)}+{p('k_wp',s)}+{p('k_narz',s)}+{p('k_vva',s)}+{p('k_inne',s)}"+("" if i==0 else f"+{p('jedn',s)}/12"), ZL, True),
     ("ebitda","EBITDA", lambda c,pc,i: f"={c}{{rev}}-{c}{{fee}}-{c}{{adsr}}-{c}{{stale}}", ZL, True),
     ("zus","ZUS społeczny", lambda c,pc,i: f"={p('zus',s)}", ZL, True),
     ("zdrw","Składka zdrowotna (próg wg przychodu rocznego)", lambda c,pc,i: f"=IF($O{{rev}}<=60000,{zdr(0)},IF($O{{rev}}<=300000,{zdr(1)},{zdr(2)}))", ZL, True),
     ("rycz","Ryczałt", lambda c,pc,i: f"=({c}{{rev}}-{c}{{r_ses}})*{p('r_dig',s)}+{c}{{r_ses}}*{p('r_dor',s)}", ZL, True),
     ("zysk","ZYSK NA RĘKĘ", lambda c,pc,i: f"={c}{{ebitda}}-{c}{{zus}}-{c}{{zdrw}}-{c}{{rycz}}", ZL, True),
     ("cash","Gotówka narastająco", lambda c,pc,i: (f"={p('got0',s)}+{c}{{zysk}}" if i==0 else f"={pc}{{cash}}+{c}{{zysk}}"), ZL, False),
     ("vatsum","Sprzedaż narastająco w 2027 (do limitu VAT)", lambda c,pc,i: ("=0" if i==0 else (f"={c}{{rev}}" if i==1 else f"={pc}{{vatsum}}+{c}{{rev}}")), ZL, False),
     ("vatcost","Koszt VAT od sprzedaży B2C (gdy VAT aktywny)", lambda c,pc,i: f"=IF({p('vat',s)}=1,({c}{{r_trip}}+{c}{{r_pdf}}+{c}{{r_pak}}+{c}{{r_kurs}}+{c}{{r_klub}}+{c}{{r_wars}})*{p('vat_st',s)},0)", ZL, True),
     ("h_sob","Godziny sobotnie potrzebne", lambda c,pc,i: f"=IF({c}{{q_wars}}>0,{p('h_wars',s)},0)+IF({c}{{klub}}>0,{p('h_klub',s)},0)+IF(AND({c}4>=1,{c}4<{p('m_kurs',s)}),{p('h_kurs',s)}/MAX(1,{p('m_kurs',s)}-1),0)", NUM, True),
     ("h_sob_max","Godziny sobotnie dostępne", lambda c,pc,i: f"={p('sob',s)}*{p('h_sob',s)}", NUM, True),
     ("h_ok","Mieści się w sobotach?", lambda c,pc,i: f'=IF({c}{{h_sob}}<={c}{{h_sob_max}},"TAK","NIE")', None, False),
     ("h_mark","Godziny marketingu poza sobotami (luka czasu)", lambda c,pc,i: f"={p('h_mark',s)}*4.33", NUM, True),
     ("zysk_h","Zysk na rękę / godzinę Twojego czasu", lambda c,pc,i: f"=IF(({c}{{h_sob}}+{c}{{q_ses}}+{c}{{h_mark}})>0,{c}{{zysk}}/({c}{{h_sob}}+{c}{{q_ses}}+{c}{{h_mark}}),0)", ZL, False),
    ]
    rowof={k:5+j for j,(k,*_) in enumerate(spec)}
    for j,(k,lab,f,fmt,summ) in enumerate(spec):
        rr=rowof[k]; ws.cell(rr,1,lab)
        for i in range(13):
            c=L(2+i); pc=L(1+i)
            formula=f(c,pc,i)
            for kk,v in rowof.items(): formula=formula.replace("{"+kk+"}",str(v))
            cell=ws.cell(rr,2+i,formula)
            if fmt: cell.number_format=fmt
        if summ:
            cell=ws.cell(rr,15,f"=SUM(C{rr}:N{rr})"); cell.font=FB
            if fmt: cell.number_format=fmt
        elif k in ("lista","klub","cash","vatsum"):
            cell=ws.cell(rr,15,f"=N{rr}"); cell.font=FB; cell.number_format=fmt
        if k in ("rev","ebitda","zysk","cash","lista"):
            for jj in range(1,16): ws.cell(rr,jj).fill=KEY; ws.cell(rr,jj).font=FB
    ws.column_dimensions["A"].width=58
    for i in range(2,16): ws.column_dimensions[L(i)].width=11
    ws.freeze_panes="B5"
    LINES.update(rowof)
for s in range(3): build_month(s)
# ---------------- Wynik i EBITDA
W=wb.create_sheet("Wynik i EBITDA",2)
W["A1"]="Rok 2027 – porównanie scenariuszy (sumy z arkuszy miesięcznych)"; W["A1"].font=FH
for i,h in enumerate(["Pozycja",*SC,"Uwaga"],1):
    c=W.cell(3,i,h); c.font=FB; c.fill=HEAD
def ms(s,k,col="O"): return f"'M-{SC[s]}'!{col}{LINES[k]}"
out=[
 ("Lista mailowa na koniec 2027",lambda s:f"={ms(s,'lista')}",INT,""),
 ("Członkowie Klubu na koniec 2027",lambda s:f"={ms(s,'klub')}",INT,""),
 ("MRR Klubu w XII 2027",lambda s:f"='M-{SC[s]}'!N{LINES['r_klub']}",ZL,"przychód powtarzalny miesięcznie"),
 ("Przychód netto",lambda s:f"={ms(s,'rev')}",ZL,""),
 ("  produkty cyfrowe i licencje (bez Twojego czasu)",lambda s:f"={ms(s,'rev_cyf')}",ZL,""),
 ("  warsztaty sobotnie",lambda s:f"={ms(s,'r_wars')}",ZL,""),
 ("  sesje 1:1",lambda s:f"={ms(s,'r_ses')}",ZL,""),
 ("Udział produktów cyfrowych w przychodzie",lambda s:f"=IF({L(2+s)}7>0,{L(2+s)}8/{L(2+s)}7,0)",PCT,"cel: ≥60%"),
 ("Opłaty za płatność",lambda s:f"={ms(s,'fee')}",ZL,""),
 ("Reklamy",lambda s:f"={ms(s,'adsr')}",ZL,""),
 ("Koszty stałe",lambda s:f"={ms(s,'stale')}",ZL,""),
 ("EBITDA",lambda s:f"={ms(s,'ebitda')}",ZL,"przychód − opłaty − reklamy − koszty stałe"),
 ("Marża EBITDA",lambda s:f"=IF({L(2+s)}7>0,{L(2+s)}15/{L(2+s)}7,0)",PCT,""),
 ("ZUS + zdrowotna",lambda s:f"={ms(s,'zus')}+{ms(s,'zdrw')}",ZL,"🧾"),
 ("Ryczałt",lambda s:f"={ms(s,'rycz')}",ZL,"🧾"),
 ("ZYSK NA RĘKĘ",lambda s:f"={ms(s,'zysk')}",ZL,""),
 ("Zysk na rękę miesięcznie (średnio)",lambda s:f"={L(2+s)}19/12",ZL,""),
 ("Gotówka na koniec 2027",lambda s:f"={ms(s,'cash')}",ZL,""),
 ("Godziny Twojej pracy w roku (soboty + 1:1 + marketing)",lambda s:f"={ms(s,'h_sob')}+{ms(s,'q_ses')}+{ms(s,'h_mark')}",INT,""),
 ("Zysk na rękę / godzinę",lambda s:f"=IF({L(2+s)}22>0,{L(2+s)}19/{L(2+s)}22,0)",ZL,"porównaj z kosztem alternatywnym"),
 ("EBITDA po koszcie alternatywnym czasu",lambda s:f"={L(2+s)}15-{L(2+s)}22*{p('stawka',s)}",ZL,""),
 ("Miesiące, w których soboty się nie mieszczą",lambda s:f"=COUNTIF('M-{SC[s]}'!C{LINES['h_ok']}:N{LINES['h_ok']},\"NIE\")",INT,"powinno być 0"),
 ("Próg rentowności (przychód roczny)",lambda s:f"=IF({L(2+s)}7>0,({L(2+s)}13+{L(2+s)}14+{L(2+s)}17)/(1-{L(2+s)}12/{L(2+s)}7),0)",ZL,"koszty stałe + reklamy + składki / (1 − opłaty%)"),
 ("Wartość 1 zapisu na listę (przychód / nowe zapisy)",lambda s:f"=IF(('M-{SC[s]}'!O{LINES['nowi_o']}+'M-{SC[s]}'!O{LINES['nowi_a']})>0,{L(2+s)}7/('M-{SC[s]}'!O{LINES['nowi_o']}+'M-{SC[s]}'!O{LINES['nowi_a']}),0)",ZL,"porównaj z CPL"),
 ("CPL (koszt zapisu z reklamy)",lambda s:f"={p('cpl',s)}",ZL,""),
 ("LTV członka Klubu (cena netto / churn)",lambda s:f"=IF({p('churn',s)}>0,{p('c_klub',s)}*'M-{SC[s]}'!C{LINES['vatf']}/{p('churn',s)},0)",ZL,""),
 ("ROI reklam (przychód z zapisów z reklam / reklamy − 1)",lambda s:f"=IF({L(2+s)}13>0,{L(2+s)}27*'M-{SC[s]}'!O{LINES['nowi_a']}/{L(2+s)}13-1,0)",PCT,"zakłada tę samą wartość zapisu z reklamy co z ruchu"),
 ("Koszt VAT od sprzedaży B2C",lambda s:f"={ms(s,'vatcost')}",ZL,"ile kosztuje utrata zwolnienia"),
 ("Przychód z sesji 1:1",lambda s:f"={L(2+s)}10",ZL,""),
 ("Czy sesje 1:1 pokrywają koszt VAT?",lambda s:f'=IF({p("vat",s)}=1,IF({L(2+s)}32>{L(2+s)}31,"TAK","NIE – rozważ inną formę sesji 🧾"),"VAT nieaktywny")',None,""),
 ("Sprzedaż w 2027 vs limit VAT",lambda s:f'=IF({ms(s,"vatsum")}>{p("vat_lim",s)},"powyżej limitu","w limicie")',None,""),
]
for k,(lab,f,fmt,note) in enumerate(out):
    rr=4+k; W.cell(rr,1,lab)
    for s in range(3):
        c=W.cell(rr,2+s,f(s))
        if fmt: c.number_format=fmt
    W.cell(rr,5,note)
    if lab in ("EBITDA","ZYSK NA RĘKĘ","Przychód netto"):
        for j in range(1,6): W.cell(rr,j).fill=KEY; W.cell(rr,j).font=FB
W.column_dimensions["A"].width=58
for c in "BCD": W.column_dimensions[c].width=16
W.column_dimensions["E"].width=50
# ---------------- Macierz zysków
M=wb.create_sheet("Macierz zysków",3)
M["A1"]="Macierz zysków – scenariusz wybrany w Parametrach (scen): przychód × Twój czas"; M["A1"].font=FH
M["A2"]="Gwiazda: duży przychód, zero Twojego czasu na sprzedaż. Dojna krowa: stały przychód. Znak zapytania: mały, ale skalowalny. Pies: zjada czas."
hd=["Produkt","Przychód 2027","Udział","Twoje godziny 2027","Przychód / godz.","Skalowalny?","Ćwiartka","Rola w drabinie"]
for i,h in enumerate(hd,1):
    c=M.cell(4,i,h); c.font=FB; c.fill=HEAD
prods=[("Produkt wejściowy 39 zł","r_trip","0","TAK","wejście: zamienia zapis w klienta"),
       ("Szablony / PDF","r_pdf","0","TAK","drugi zakup, budowa koszyka"),
       ("Pakiet 297 zł","r_pak","0","TAK","produkt główny"),
       ("Kurs wideo","r_kurs",f"{p('h_kurs',0)}","TAK","produkt główny premium"),
       ("Klub (abonament)","r_klub",f"'M-X'!O{LINES['klub']}*0+COUNTIF('M-X'!C{LINES['klub']}:N{LINES['klub']},\">0\")*{p('h_klub',0)}","TAK","przychód powtarzalny"),
       ("Warsztat sobotni","r_wars",f"COUNTIF('M-X'!C{LINES['q_wars']}:N{LINES['q_wars']},\">0\")*{p('h_wars',0)}","NIE","premium, ograniczony sobotami"),
       ("Sesje 1:1","r_ses",f"'M-X'!O{LINES['q_ses']}","NIE","premium, ograniczony czasem"),
       ("Licencje B2B","r_lic","0","TAK","sprzedaż tego samego firmom")]
for k,(n,key,hrs,scal,role) in enumerate(prods):
    rr=5+k; M.cell(rr,1,n)
    M.cell(rr,2,"=CHOOSE("+p('scen',0)+","+",".join(f"'M-{SC[s]}'!O{LINES[key]}" for s in range(3))+")").number_format=ZL
    M.cell(rr,3,f"=IF(SUM($B$5:$B$12)>0,B{rr}/SUM($B$5:$B$12),0)").number_format=PCT
    hf="=CHOOSE("+p('scen',0)+","+",".join(hrs.replace("M-X",f"M-{SC[s]}") for s in range(3))+")"
    M.cell(rr,4,hf).number_format=INT
    M.cell(rr,5,f'=IF(D{rr}>0,B{rr}/D{rr},"bez limitu")').number_format=ZL
    c=M.cell(rr,6,scal); c.fill=INP; c.font=FIN
    M.cell(rr,7,f'=IF(F{rr}="TAK",IF(C{rr}>=0.15,"Gwiazda","Znak zapytania"),IF(C{rr}>=0.15,"Dojna krowa","Pies"))')
    M.cell(rr,8,role)
M.cell(14,1,"Rozkład: ruch → zapis (Akademia/newsletter) → 39 zł → szablony → Pakiet 297 zł → kurs wideo 697 zł → Klub 49 zł/mies. Warsztat i 1:1 są na górze drabiny, mają mało miejsc.")
M.column_dimensions["A"].width=30
for c,w in zip("BCDEFGH",[14,9,12,14,11,16,40]): M.column_dimensions[c].width=w
# ---------------- KPI 2027 (wybrany scenariusz)
K=wb.create_sheet("KPI 2027",4)
K["A1"]="KPI miesięczne – scenariusz wybrany w Parametrach"; K["A1"].font=FH
for i,m in enumerate(months): c=K.cell(3,2+i,m); c.font=FB; c.fill=HEAD
K.cell(3,15,"Rok").font=FB
kp=[("Odwiedziny","odw",INT),("Nowe zapisy (ruch + reklamy)",None,INT),("Lista mailowa","lista",INT),("Zakupy 39 zł","q_trip",NUM),("Zakupy szablonów/PDF","q_pdf",NUM),
    ("Zakupy Pakietu","q_pak",NUM),("Zakupy kursu wideo","q_kurs",NUM),("Członkowie Klubu","klub",NUM),("Uczestnicy warsztatu","q_wars",NUM),("Sesje 1:1","q_ses",NUM),
    ("Przychód netto","rev",ZL),("EBITDA","ebitda",ZL),("Zysk na rękę","zysk",ZL),("Gotówka narastająco","cash",ZL),("Godziny sobotnie (potrzebne)","h_sob",NUM)]
for j,(lab,key,fmt) in enumerate(kp):
    rr=4+j; K.cell(rr,1,lab)
    for i in range(14):
        col=L(2+i) if i<13 else "O"
        if key is None:
            f="=CHOOSE("+p('scen',0)+","+",".join(f"'M-{SC[s]}'!{col}{LINES['nowi_o']}+'M-{SC[s]}'!{col}{LINES['nowi_a']}" for s in range(3))+")"
        else:
            f="=CHOOSE("+p('scen',0)+","+",".join(f"'M-{SC[s]}'!{col}{LINES[key]}" for s in range(3))+")"
        c=K.cell(rr,2+i,f); c.number_format=fmt
    if lab in ("EBITDA","Zysk na rękę","Lista mailowa"):
        for jj in range(1,16): K.cell(rr,jj).fill=KEY
K.column_dimensions["A"].width=34
for i in range(2,16): K.column_dimensions[L(i)].width=11
for ws in wb.worksheets:
    for row in ws.iter_rows():
        for c in row:
            if c.font.name!="Arial": c.font=Font(name="Arial",size=c.font.size or 10,bold=c.font.bold,color=c.font.color)
wb.save(OUT); print("ok")
