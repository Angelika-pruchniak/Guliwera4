import openpyxl
from openpyxl.styles import Font, PatternFill, Alignment, Border, Side
from openpyxl.workbook.defined_name import DefinedName
from openpyxl.utils import get_column_letter as L
from openpyxl.comments import Comment
import sys
OUT=sys.argv[1]
wb=openpyxl.Workbook()
F=Font(name="Arial",size=10); FB=Font(name="Arial",size=10,bold=True); FH=Font(name="Arial",size=12,bold=True,color="3A2340")
FIN=Font(name="Arial",size=10,color="0000FF")
INP=PatternFill("solid",fgColor="FFEFC7"); HEAD=PatternFill("solid",fgColor="FFD9E4"); KEY=PatternFill("solid",fgColor="EDE5FB")
ZL='#,##0" zł";[Red]-#,##0" zł"'; PCT='0.0%'; NUM='#,##0.0'; INT='#,##0'
def name(n,ref): wb.defined_names[n]=DefinedName(n,attr_text=ref)
def style(ws):
    for row in ws.iter_rows():
        for c in row:
            if c.font==Font() or c.font.name!="Arial": c.font=F if not c.font.bold else FB
# ---------------- Parametry
P=wb.active; P.title="Parametry"
P["A1"]="Guliwera 4 – model finansowy 2026/2027: PARAMETRY"; P["A1"].font=FH
P["A2"]="Żółte komórki z niebieską czcionką = dane wejściowe (zmieniasz). Reszta liczy się wzorami. 🧾 = potwierdzić z księgową. Wartości domyślne to ZAŁOŻENIA, nie dane firmy."
params=[
 ("Wybór scenariusza",None,None),
 ("scen","Scenariusz (1 ostrożny, 2 bazowy, 3 ambitny, 4 rekomendowany B2B-first)",4,INT,"Steruje arkuszem KPI 2027"),
 ("Podatki i składki (🧾)",None,None),
 ("vat_dorad","Doradztwo wyłączone ze zwolnienia VAT (art. 113 ust. 13 ustawy o VAT)? 1 = tak, 0 = nie",1,INT,"🧾 Usługi doradcze nie korzystają ze zwolnienia podmiotowego. Jeśli tak: VAT od pierwszej złotówki dla CAŁEJ sprzedaży"),
 ("vat_limit","Limit zwolnienia podmiotowego VAT",200000,ZL,"od 2026 r. 240 000 zł? – sprawdzić aktualny limit 🧾"),
 ("vat_stawka","Stawka VAT",0.23,PCT,""),
 ("b2c_brutto","Ceny B2C (PDF, kurs, planer) zostają takie same brutto po wejściu w VAT? 1 = tak",1,INT,"Jeśli 1, przychód netto = cena/1,23"),
 ("zus_spol","ZUS społeczny miesięcznie",456,ZL,"0 przy etacie ≥ minimalna krajowa (zbieg tytułów); ok. 456 zł preferencyjny 2026; ok. 1 927 zł pełny z chorobowym – 🧾"),
 ("zdr_1","Składka zdrowotna (ryczałt) – przychód do 60 tys. zł",498,ZL,"szacunek 2026 🧾"),
 ("zdr_2","Składka zdrowotna (ryczałt) – przychód 60–300 tys. zł",831,ZL,"szacunek 2026 🧾"),
 ("zdr_3","Składka zdrowotna (ryczałt) – przychód powyżej 300 tys. zł",1495,ZL,"szacunek 2026 🧾"),
 ("Koszty stałe miesięcznie (wpisz swoje)",None,None),
 ("k_ksieg","Księgowa / biuro rachunkowe",300,ZL,""),
 ("k_wp","WordPress.com (plan) + domena + poczta",150,ZL,"roczny plan podzielony na 12"),
 ("k_narz","Canva, Mailchimp, Metricool, inne narzędzia",150,ZL,""),
 ("k_inne","Telefon, internet, sprzęt, szkolenia, inne",200,ZL,""),
 ("Reklamy (budżet miesięczny wg scenariusza)",None,None),
 ("ads_1","Reklamy – scenariusz ostrożny",500,ZL,""),
 ("ads_2","Reklamy – scenariusz bazowy",1000,ZL,"plan z czatu „Płatne reklamy”: 1000 zł/mies."),
 ("ads_3","Reklamy – scenariusz ambitny",2000,ZL,""),
 ("ads_4","Reklamy – scenariusz rekomendowany",400,ZL,"tylko retargeting + LinkedIn pod B2B i kurs"),
 ("Jednorazowe 2027",None,None),
 ("jedn","Koszty jednorazowe (np. GS1/EAN, sesja zdjęciowa, prawnik – regulaminy)",2000,ZL,""),
 ("Czas i koszt alternatywny",None,None),
 ("stawka_alt","Koszt alternatywny godziny Twojej pracy",250,ZL,"ile zarabiasz w alternatywie (etat/zlecenie) za 1 h"),
 ("h_mies_max","Godziny miesięcznie dostępne na firmę",60,INT,"przy etacie realnie 40–60 h"),
 ("h_admin","Godziny stałe miesięcznie (marketing, social, maile, księgowość)",20,INT,""),
 ("h_planer","Godziny jednorazowe: projekt i skład planera papierowego",80,INT,"220 stron – ok. 2 tygodnie"),
 ("Lejek (do KPI)",None,None),
 ("konw_pdf","Konwersja: odwiedziny → zamówienie PDF",0.012,PCT,"sklepy z e-bookami 0,5–2%"),
 ("konw_lead","Skuteczność: lead B2B → zlecenie",0.25,PCT,""),
 ("konw_kurs","Lista oczekujących → zakup kursu",0.15,PCT,""),
 ("gotowka0","Gotówka na start (1.12.2026)",5000,ZL,"wpisz prywatnie"),
]
r=4
for p in params:
    if p[1] is None:
        P.cell(r,1,p[0]).font=FB; P.cell(r,1).fill=HEAD; r+=1; continue
    n,lab,val,fmt,note=p
    P.cell(r,1,lab); c=P.cell(r,3,val); c.fill=INP; c.font=FIN; c.number_format=fmt; P.cell(r,4,note)
    name(n,f"Parametry!$C${r}"); r+=1
P.column_dimensions["A"].width=78; P.column_dimensions["C"].width=14; P.column_dimensions["D"].width=90
# ---------------- Produkty
S=wb.create_sheet("Produkty")
S["A1"]="Ekonomika jednostkowa i wolumeny roczne 2027 (wolumeny = założenia scenariuszy)"; S["A1"].font=FH
hdr=["Produkt","Typ (B2C/B2B)","Cena katalogowa (strona)","Opłata płatności %","Opłata stała / szt.","Koszt bezpośredni / szt.","Ryczałt % 🧾","Godziny / szt. (z obsługą i sprzedażą)",
     "Cena netto dla mnie","Marża wkładu / szt.","Marża wkładu / godz.",
     "Wol. ostrożny","Wol. bazowy","Wol. ambitny","Wol. rekomend.","Wol. XII 2026","CAC realny / szt. (reklama)","Marża po CAC / szt.","Marża po CAC / godz."]
for i,h in enumerate(hdr,1):
    c=S.cell(3,i,h); c.font=FB; c.fill=HEAD; c.alignment=Alignment(wrap_text=True,vertical="top")
prods=[
 ("PDF Kierunek Zysk (średni koszyk)","B2C",95,0.02,1,0,0.085,0.15, 180,480,1200,240, 25, 60),
 ("Kurs „Strategia w 6 tygodni” (uczestnik)","B2C",1790,0.02,1,0,0.085,2.5, 8,24,54,24, 0, 250),
 ("Sesja strategiczna 60 min","B2B",1200,0.02,0,0,0.15,2.5, 8,20,40,20, 1, 0),
 ("Przegląd Decyzji (konsultacja)","B2B",3000,0.02,0,0,0.15,8, 4,10,20,8, 0, 0),
 ("Warsztat pół dnia","B2B",4900,0,0,0,0.15,14, 2,4,8,4, 0, 0),
 ("Warsztat cały dzień","B2B",8900,0,0,0,0.15,26, 1,3,6,3, 0, 0),
 ("Strateg na abonament (miesiąc)","B2B",3500,0,0,0,0.15,7, 0,9,27,12, 0, 0),
 ("Planer papierowy (szt.)","B2C",119,0.02,1,53.5,0.03,0.6, 50,150,300,0, 30, 30),
]
first=4
for k,p in enumerate(prods):
    rr=first+k
    vals=list(p[:8])
    for j,v in enumerate(vals,1):
        c=S.cell(rr,j,v)
        if j>=3: c.fill=INP; c.font=FIN
    S.cell(rr,3).number_format=ZL; S.cell(rr,4).number_format=PCT; S.cell(rr,5).number_format=ZL; S.cell(rr,6).number_format=ZL; S.cell(rr,7).number_format=PCT; S.cell(rr,8).number_format=NUM
    S.cell(rr,9,f'=IF(AND(B{rr}="B2C",vat_aktywny=1,b2c_brutto=1),C{rr}/(1+vat_stawka),C{rr})').number_format=ZL
    S.cell(rr,10,f"=I{rr}-C{rr}*D{rr}-E{rr}-F{rr}").number_format=ZL
    S.cell(rr,11,f"=IF(H{rr}>0,J{rr}/H{rr},0)").number_format=ZL
    for j,v in enumerate(p[8:13]):
        c=S.cell(rr,12+j,v); c.fill=INP; c.font=FIN; c.number_format=INT
    c=S.cell(rr,17,p[13]); c.fill=INP; c.font=FIN; c.number_format=ZL
    S.cell(rr,18,f"=J{rr}-Q{rr}").number_format=ZL
    S.cell(rr,19,f"=IF(H{rr}>0,R{rr}/H{rr},0)").number_format=ZL
last=first+len(prods)-1
S.cell(last+2,1,"CAC B2B = 0, bo koszt pozyskania to Twoje godziny sprzedaży wliczone w „Godziny / szt.”. CAC B2C to realny koszt reklamy na 1 sprzedaż (plan reklam: ≤60 zł na zakup PDF).")
S.cell(last+1,1,"Planer: koszt bezpośredni = druk 45 zł + opakowanie 2,5 zł + proof/transport 150 zł/50 szt. (3 zł) + wysyłka do klienta pokrywa klient. Kurs: 1 790 zł = średnia z ceny 1 590 (lista) i 1 990.")
name("vol_rows",f"Produkty!$A${first}:$A${last}")
# per-scenario calc block
S.cell(last+3,1,"Wyniki roczne wg produktu i scenariusza").font=FH
blk=last+4
heads=["Produkt"]
scen_names=["Ostrożny","Bazowy","Ambitny","Rekomend."]
cols=[]
for s in range(4):
    heads+= [f"Przychód {scen_names[s]}",f"Marża wkładu {scen_names[s]}",f"Godziny {scen_names[s]}",f"Ryczałt {scen_names[s]}"]
for i,h in enumerate(heads,1):
    c=S.cell(blk,i,h); c.font=FB; c.fill=HEAD; c.alignment=Alignment(wrap_text=True)
for k in range(len(prods)):
    rr=blk+1+k; src=first+k
    S.cell(rr,1,f"=A{src}")
    for s in range(4):
        vc=L(12+s); base=2+s*4
        S.cell(rr,base,f"={vc}{src}*I{src}").number_format=ZL
        S.cell(rr,base+1,f"={vc}{src}*J{src}").number_format=ZL
        S.cell(rr,base+2,f"={vc}{src}*H{src}").number_format=INT
        S.cell(rr,base+3,f"={vc}{src}*I{src}*G{src}").number_format=ZL
tr=blk+1+len(prods)
S.cell(tr,1,"RAZEM").font=FB
for s in range(4):
    for q in range(4):
        col=L(2+s*4+q)
        c=S.cell(tr,2+s*4+q,f"=SUM({col}{blk+1}:{col}{tr-1})"); c.font=FB; c.number_format=ZL if q!=2 else INT
# gross for VAT limit (no VAT adj): sum vol*price excluding B2B? Limit counts all sales value
gr=tr+1
S.cell(gr,1,"Sprzedaż do limitu VAT (ceny katalogowe)")
for s in range(4):
    vc=L(12+s)
    S.cell(gr,2+s*4,f"=SUMPRODUCT({vc}{first}:{vc}{last},C{first}:C{last})").number_format=ZL
name("vat_aktywny","Parametry!$C$40")
for i,w in enumerate([42,9,13,10,10,11,9,12,12,12,12,10,10,10,10,10],1): S.column_dimensions[L(i)].width=w
for i in range(2,18): S.column_dimensions[L(i)].width=max(S.column_dimensions[L(i)].width or 0,12)
S.freeze_panes="B4"
# vat_aktywny cell in Parametry (scenario-independent decision uses chosen scenario gross)
P["A40"]="VAT aktywny (wynik: doradztwo wyłączone LUB sprzedaż wybranego scenariusza > limit) – nie wpisuj"
P["C40"]=f"=IF(OR(vat_dorad=1,INDEX(Produkty!B{gr}:Q{gr},1,(scen-1)*4+1)>vat_limit),1,0)"; P["C40"].fill=KEY; P["C40"].font=FB
P["D40"]="Uwaga: wejście w VAT obniża przychód netto z PDF, kursu i planera o ok. 18,7%, jeśli ceny brutto zostają bez zmian."
# ---------------- Wynik (P&L / EBITDA)
W=wb.create_sheet("Wynik i EBITDA")
W["A1"]="Rachunek wyników 2027 wg scenariuszy, EBITDA i zysk „na rękę”"; W["A1"].font=FH
for i,h in enumerate(["Pozycja"]+scen_names+["Wzór / uwaga"],1):
    c=W.cell(3,i,h); c.font=FB; c.fill=HEAD
def pc(s,q): return f"Produkty!{L(2+s*4+q)}{tr}"
rows=[
 ("Przychód netto",lambda s:f"={pc(s,0)}","suma: wolumen × cena netto",ZL),
 ("Koszty zmienne (opłaty płatności, druk)",lambda s:f"={L(2+s)}4-{L(2+s)}6" ,"przychód − marża wkładu",ZL),
 ("Marża wkładu",lambda s:f"={pc(s,1)}","",ZL),
 ("Marża wkładu %",lambda s:f"=IF({L(2+s)}4>0,{L(2+s)}6/{L(2+s)}4,0)","",PCT),
 ("Reklamy płatne",lambda s:f"=12*ads_{s+1}","12 × budżet miesięczny",ZL),
 ("Koszty stałe (księgowa, WordPress, narzędzia, inne)",lambda s:"=12*(k_ksieg+k_wp+k_narz+k_inne)","",ZL),
 ("Koszty jednorazowe",lambda s:"=jedn","",ZL),
 ("EBITDA",lambda s:f"={L(2+s)}6-{L(2+s)}8-{L(2+s)}9-{L(2+s)}10","marża wkładu − reklamy − stałe − jednorazowe (JDG: brak amortyzacji i odsetek)",ZL),
 ("Marża EBITDA %",lambda s:f"=IF({L(2+s)}4>0,{L(2+s)}11/{L(2+s)}4,0)","",PCT),
 ("ZUS społeczny",lambda s:"=12*zus_spol","koszt właścicielki – poniżej EBITDA",ZL),
 ("Składka zdrowotna (wg progu przychodu)",lambda s:f"=12*IF({L(2+s)}4<=60000,zdr_1,IF({L(2+s)}4<=300000,zdr_2,zdr_3))","🧾",ZL),
 ("Ryczałt od przychodu",lambda s:f"={pc(s,3)}","stawki wg produktu; bez odliczenia ZUS i 50% zdrowotnej – uproszczenie 🧾",ZL),
 ("ZYSK „NA RĘKĘ”",lambda s:f"={L(2+s)}11-{L(2+s)}13-{L(2+s)}14-{L(2+s)}15","EBITDA − ZUS − zdrowotna − ryczałt",ZL),
 ("Zysk na rękę miesięcznie",lambda s:f"={L(2+s)}16/12","",ZL),
 ("Godziny pracy w roku",lambda s:f"={pc(s,2)}+12*h_admin+IF(Produkty!{L(12+s)}{last}>0,h_planer,0)","godziny produktów + stałe + projekt planera",INT),
 ("Godziny miesięcznie",lambda s:f"={L(2+s)}18/12","porównaj z limitem w Parametrach",NUM),
 ("Czy mieści się w czasie?",lambda s:f'=IF({L(2+s)}19<=h_mies_max,"TAK","NIE – brak czasu")',"",None),
 ("Zysk na rękę / godzinę",lambda s:f"=IF({L(2+s)}18>0,{L(2+s)}16/{L(2+s)}18,0)","porównaj z kosztem alternatywnym",ZL),
 ("EBITDA po koszcie alternatywnym czasu",lambda s:f"={L(2+s)}11-{L(2+s)}18*stawka_alt","ekonomiczny zysk: czy firma płaci więcej niż alternatywa",ZL),
 ("Próg rentowności (przychód roczny)",lambda s:f"=IF({L(2+s)}7>0,({L(2+s)}8+{L(2+s)}9+{L(2+s)}10+{L(2+s)}13+{L(2+s)}14)/{L(2+s)}7,0)","koszty stałe z ZUS / marża wkładu % (ryczałt pominięty)",ZL),
 ("Bufor bezpieczeństwa",lambda s:f"=IF({L(2+s)}4>0,({L(2+s)}4-{L(2+s)}23)/{L(2+s)}4,0)","(przychód − próg) / przychód",PCT),
 ("Sprzedaż do limitu VAT",lambda s:f"=Produkty!{L(2+s*4)}{gr}","ceny katalogowe",ZL),
 ("Limit VAT przekroczony?",lambda s:f'=IF({L(2+s)}25>vat_limit,"TAK","nie")',"",None),
 ("Przychód B2B / całość",lambda s:f"=IF({L(2+s)}4>0,SUMPRODUCT((Produkty!$B${first}:$B${last}=\"B2B\")*Produkty!{L(12+s)}{first}:{L(12+s)}{last}*Produkty!$I${first}:$I${last})/{L(2+s)}4,0)","koncentracja przychodu",PCT),
 ("ROAS reklam (przychód B2C / reklamy)",lambda s:f"=IF({L(2+s)}8>0,SUMPRODUCT((Produkty!$B${first}:$B${last}=\"B2C\")*Produkty!{L(12+s)}{first}:{L(12+s)}{last}*Produkty!$I${first}:$I${last})/{L(2+s)}8,0)","zakłada, że cała sprzedaż B2C pochodzi z reklam – górna granica",NUM),
 ("CAC zamówienia B2C (reklamy / zamówienia B2C)",lambda s:f"=IF(SUMPRODUCT((Produkty!$B${first}:$B${last}=\"B2C\")*Produkty!{L(12+s)}{first}:{L(12+s)}{last})>0,{L(2+s)}8/SUMPRODUCT((Produkty!$B${first}:$B${last}=\"B2C\")*Produkty!{L(12+s)}{first}:{L(12+s)}{last}),0)","",ZL),
]
for k,(lab,f,note,fmt) in enumerate(rows):
    rr=4+k
    W.cell(rr,1,lab)
    for s in range(4):
        c=W.cell(rr,2+s,f(s))
        if fmt: c.number_format=fmt
    W.cell(rr,6,note)
    if lab in ("EBITDA","ZYSK „NA RĘKĘ”","EBITDA po koszcie alternatywnym czasu"):
        for j in range(1,6): W.cell(rr,j).font=FB; W.cell(rr,j).fill=KEY
W.column_dimensions["A"].width=48
for c in "BCDE": W.column_dimensions[c].width=15
W.column_dimensions["F"].width=80
# ---------------- Macierz zysków
M=wb.create_sheet("Macierz zysków")
M["A1"]="Macierz zysków (w duchu BCG): zysk na godzinę × skalowalność – dla wybranego scenariusza"; M["A1"].font=FH
M["A2"]="Oś pionowa: marża wkładu na godzinę vs koszt alternatywny. Oś pozioma: skalowalność = czy przychód rośnie bez proporcjonalnego wzrostu Twoich godzin (≤ 1 h/szt.)."
mh=["Produkt","Marża po CAC / godz.","Godziny / szt.","Marża wkładu w scenariuszu","Udział w marży","Godziny w scenariuszu","Udział w godzinach","Ćwiartka","Decyzja"]
for i,h in enumerate(mh,1):
    c=M.cell(4,i,h); c.font=FB; c.fill=HEAD; c.alignment=Alignment(wrap_text=True)
for k in range(len(prods)):
    rr=5+k; src=first+k; br=blk+1+k
    M.cell(rr,1,f"=Produkty!A{src}")
    M.cell(rr,2,f"=Produkty!S{src}").number_format=ZL
    M.cell(rr,3,f"=Produkty!H{src}").number_format=NUM
    M.cell(rr,4,f"=INDEX(Produkty!B{br}:Q{br},1,(scen-1)*4+2)").number_format=ZL
    M.cell(rr,5,f"=IF(SUM($D$5:$D${4+len(prods)})<>0,D{rr}/SUM($D$5:$D${4+len(prods)}),0)").number_format=PCT
    M.cell(rr,6,f"=INDEX(Produkty!B{br}:Q{br},1,(scen-1)*4+3)").number_format=INT
    M.cell(rr,7,f"=IF(SUM($F$5:$F${4+len(prods)})<>0,F{rr}/SUM($F$5:$F${4+len(prods)}),0)").number_format=PCT
    M.cell(rr,8,f'=IF(B{rr}>=stawka_alt,IF(C{rr}<=1,"Gwiazda","Dojna krowa"),IF(C{rr}<=1,"Znak zapytania","Pies"))')
    M.cell(rr,9,f'=IF(H{rr}="Gwiazda","inwestuj i skaluj",IF(H{rr}="Dojna krowa","sprzedawaj, podnoś cenę, pilnuj czasu",IF(H{rr}="Znak zapytania","testuj tanio: rola lejka, nie zysku","ogranicz albo wycofaj")))')
M.column_dimensions["A"].width=42
for c,w in zip("BCDEFGHI",[14,10,16,10,12,10,16,40]): M.column_dimensions[c].width=w
# ---------------- KPI 2027
K=wb.create_sheet("KPI 2027")
K["A1"]="KPI miesięczne: XII 2026 i 2027 dla wybranego scenariusza (Parametry → scen)"; K["A1"].font=FH
months=["XII 2026","I","II","III","IV","V","VI","VII","VIII","IX","X","XI","XII"]
K.cell(3,1,"Sezonowość (udział rocznego wolumenu w miesiącu)").font=FB
for i,m in enumerate(months): c=K.cell(3,2+i,m); c.font=FB; c.fill=HEAD
K.cell(3,15,"Suma").font=FB
season={
 0:[None,.11,.10,.09,.08,.07,.06,.05,.06,.09,.09,.10,.10],
 1:[None,0,.34,0,0,.33,0,0,0,0,.33,0,0],
 2:[None,.08,.09,.10,.09,.09,.08,.04,.04,.10,.10,.10,.09],
}
season[3]=season[2];season[4]=[None,.05,.10,.12,.10,.10,.08,0,0,.12,.13,.12,.08];season[5]=season[4];season[6]=[None,0,.06,.08,.09,.10,.10,.09,.09,.10,.10,.10,.09];season[7]=[None,.12,.06,.05,.05,.05,.04,.04,.10,.10,.09,.12,.18]
for k in range(len(prods)):
    rr=4+k
    K.cell(rr,1,f"=Produkty!A{first+k}")
    K.cell(rr,2,"XII 2026 z kol. P →")
    for i in range(12):
        c=K.cell(rr,3+i,season[k][1+i]); c.fill=INP; c.font=FIN; c.number_format='0%'
    K.cell(rr,15,f"=SUM(C{rr}:N{rr})").number_format='0%'
v0=4+len(prods)+2
K.cell(v0,1,"Wolumen (szt.)").font=FB
for i,m in enumerate(months): c=K.cell(v0,2+i,m); c.font=FB; c.fill=HEAD
for k in range(len(prods)):
    rr=v0+1+k; src=first+k
    K.cell(rr,1,f"=Produkty!A{src}")
    K.cell(rr,2,f"=Produkty!P{src}").number_format=NUM
    for i in range(12):
        K.cell(rr,3+i,f"=INDEX(Produkty!$L{src}:$O{src},1,scen)*{L(3+i)}{4+k}").number_format=NUM
r0=v0+len(prods)+2
K.cell(r0,1,"Wynik miesięczny").font=FB
for i,m in enumerate(months): c=K.cell(r0,2+i,m); c.font=FB; c.fill=HEAD
K.cell(r0,15,"Rok 2027").font=FB
labs=["Przychód netto","Marża wkładu","Reklamy","Koszty stałe","EBITDA","ZUS + zdrowotna","Ryczałt","Zysk na rękę","Gotówka narastająco","Sprzedaż narastająco do limitu VAT","Godziny pracy",
      "KPI: odwiedziny potrzebne dla PDF","KPI: leady B2B potrzebne (bez abonamentu)","KPI: lista oczekujących kursu potrzebna","KPI: CAC B2C (reklamy / zamówienia B2C)"]
for j,lab in enumerate(labs):
    rr=r0+1+j; K.cell(rr,1,lab)
    for i in range(13):
        col=L(2+i); vr=lambda k: f"{col}{v0+1+k}"
        if lab=="Przychód netto":
            f="="+"+".join(f"{vr(k)}*Produkty!$I${first+k}" for k in range(len(prods)))
        elif lab=="Marża wkładu":
            f="="+"+".join(f"{vr(k)}*Produkty!$J${first+k}" for k in range(len(prods)))
        elif lab=="Reklamy": f="=INDEX(Parametry!$C$21:$C$24,scen,1)"
        elif lab=="Koszty stałe": f="=k_ksieg+k_wp+k_narz+k_inne"+("+jedn/12" if i>0 else "")
        elif lab=="EBITDA": f=f"={col}{r0+2}-{col}{r0+3}-{col}{r0+4}"
        elif lab=="ZUS + zdrowotna": f="=zus_spol+INDEX(Parametry!$C$12:$C$14,MATCH(INDEX('Wynik i EBITDA'!$B$4:$E$4,1,scen),{0;60000.01;300000.01},1),1)"
        elif lab=="Ryczałt": f="="+"+".join(f"{vr(k)}*Produkty!$I${first+k}*Produkty!$G${first+k}" for k in range(len(prods)))
        elif lab=="Zysk na rękę": f=f"={col}{r0+5}-{col}{r0+6}-{col}{r0+7}"
        elif lab=="Gotówka narastająco": f=(f"=gotowka0+{col}{r0+8}" if i==0 else f"={L(1+i)}{rr}+{col}{r0+8}")
        elif lab.startswith("Sprzedaż narastająco"):
            g="+".join(f"{vr(k)}*Produkty!$C${first+k}" for k in range(len(prods)))
            f=(f"={g}" if i<=1 else f"={L(1+i)}{rr}+{g}")
        elif lab=="Godziny pracy": f="=h_admin+"+"+".join(f"{vr(k)}*Produkty!$H${first+k}" for k in range(len(prods)))
        elif lab.startswith("KPI: odwiedziny"): f=f"=IF(konw_pdf>0,{vr(0)}/konw_pdf,0)"
        elif lab.startswith("KPI: leady"): f="=IF(konw_lead>0,("+"+".join(vr(k) for k in range(2,6))+")/konw_lead,0)"
        elif lab.startswith("KPI: lista"): f=f"=IF(konw_kurs>0,{vr(1)}/konw_kurs,0)"
        elif lab.startswith("KPI: CAC"): f=f"=IF(({vr(0)}+{vr(1)}+{vr(7)})>0,{col}{r0+3}/({vr(0)}+{vr(1)}+{vr(7)}),0)"
        c=K.cell(rr,2+i,f); c.number_format=INT if ("Godziny" in lab or "KPI" in lab and "CAC" not in lab) else ZL
    if lab not in ("Gotówka narastająco","Sprzedaż narastająco do limitu VAT"):
        c=K.cell(rr,15,f"=SUM(C{rr}:N{rr})"); c.font=FB; c.number_format=K.cell(rr,3).number_format
    if lab in ("EBITDA","Zysk na rękę","Gotówka narastająco"):
        for jj in range(1,16): K.cell(rr,jj).fill=KEY
K.column_dimensions["A"].width=44
for i in range(2,16): K.column_dimensions[L(i)].width=11
K.freeze_panes="B4"
# legend sheet
R=wb.create_sheet("Jak czytać",0)
txt=["Jak korzystać z modelu",
"1. Arkusz Parametry: wpisz swoje koszty, formę ZUS, budżet reklam, godziny. Żółte pola z niebieską czcionką to dane wejściowe.",
"2. Arkusz Produkty: ceny (ze strony), opłaty, ryczałt, godziny na sztukę i wolumeny dla 4 scenariuszy. Wolumeny to hipotezy – podmieniaj co miesiąc na wykonanie.",
"3. Wynik i EBITDA: porównanie scenariuszy: EBITDA, zysk na rękę, godziny, zysk na godzinę, próg rentowności, limit VAT, ROAS, CAC.",
"4. Macierz zysków: każdy produkt w ćwiartce (Gwiazda / Dojna krowa / Znak zapytania / Pies) wg zysku na godzinę i skalowalności.",
"5. KPI 2027: miesięczne wolumeny, wynik, gotówka narastająco i KPI lejka dla scenariusza wybranego w Parametrach (komórka scen).",
"Definicja EBITDA w JDG: przychód − koszty działalności (bez podatku, amortyzacji, odsetek). Składki ZUS właścicielki i ryczałt są pod EBITDA.",
"Wszystkie kwestie podatkowe (VAT dla doradztwa, stawki ryczałtu, składki) – do potwierdzenia z księgową. Model nie zawiera prawdziwych danych firmy: wpisz je tylko w prywatnej kopii.",
]
for i,t in enumerate(txt,1): R.cell(i,1,t).font=FH if i==1 else F
R.column_dimensions["A"].width=150
for ws in wb.worksheets:
    for row in ws.iter_rows():
        for c in row:
            if c.font.name!="Arial":
                c.font=Font(name="Arial",size=c.font.size or 10,bold=c.font.bold,color=c.font.color)
wb.save(OUT)
print("ok",gr,tr,blk)
