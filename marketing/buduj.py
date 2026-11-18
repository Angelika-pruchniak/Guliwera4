# -*- coding: utf-8 -*-
import datetime as dt, re, unicodedata
from openpyxl import Workbook
from openpyxl.styles import Font, PatternFill, Alignment, Border, Side
from openpyxl.utils import get_column_letter
from openpyxl.worksheet.datavalidation import DataValidation
from openpyxl.formatting.rule import FormulaRule
from dane import W, L, B

ATR="3A2340"; KORAL="FF6B3D"; KREM="FFF6EE"; LIL="EDE5FB"; ZOL="FFEFC7"; ROZ="FFD9E4"; GRAN="1E2A4A"; BRZ="FFD8C2"
H=Font(name="DM Sans",bold=True,color="FFFFFF"); HF=PatternFill("solid",fgColor=GRAN)
T=Font(name="Fraunces",bold=True,size=16,color=ATR); N=Font(name="DM Sans",size=10,color=ATR)
thin=Side(style="thin",color="E5DCE8"); BOX=Border(left=thin,right=thin,top=thin,bottom=thin)
WR=Alignment(wrap_text=True,vertical="top")
DNI=["poniedziałek","wtorek","środa","czwartek","piątek","sobota","niedziela"]
MIES={11:"Listopad 2026",12:"Grudzień 2026",1:"Styczeń 2027"}
KOL={"LinkedIn – profil Angeliki":"DCE8F5","LinkedIn – strona Guliwera 4":"E8EFF8","LinkedIn – grupy":"F0F4FA","Facebook – strona Guliwera4":"E3ECFA","Facebook – grupy":"EEF3FC","Instagram":ROZ,"TikTok":"EDEDED","Blog guliwera4.pl":ZOL,"Newsletter":LIL,"Link building":BRZ}
def slug(s):
    s=unicodedata.normalize("NFKD",s).encode("ascii","ignore").decode().lower()
    return re.sub(r"[^a-z0-9]+","-",s).strip("-")[:40]
def utm(key,src,med,camp):
    return f"{B}{L[key][1].split('#')[0]}?utm_source={src}&utm_medium={med}&utm_campaign={camp}"+("#"+L[key][1].split('#')[1] if '#' in L[key][1] else "")
SRC={"LinkedIn – profil Angeliki":("linkedin","profil"),"LinkedIn – strona Guliwera 4":("linkedin","strona"),"LinkedIn – grupy":("linkedin","grupa"),
 "Facebook – strona Guliwera4":("facebook","strona"),"Facebook – grupy":("facebook","grupa"),"Instagram":("instagram","bio"),"TikTok":("tiktok","bio"),
 "Blog guliwera4.pl":("blog","wpis"),"Newsletter":("newsletter","email"),"Link building":("referral","link")}
FMT={"post":"LinkedIn: 1200×1500 px (4:5) lub tekst + zdjęcie Angeliki","karuzela":"PDF/karuzela 1080×1350 px, 6–10 slajdów, ostatni slajd = CTA","rolka":"1080×1920 px, 7–30 s, hook w 1. s, napisy, muzyka z biblioteki IG/TikTok",
 "zdjecie":"1080×1350 px (4:5), zdjęcie z prawdziwą twarzą Angeliki (09/07/portret-bialy)","blog":"Obrazek wyróżniający 1200×800 px, 1200–1800 słów","newsletter":"Mail 600 px, nagłówek + 3 sekcje + 1 CTA","grupa":"Tekst (bez linku w poście, link w odpowiedzi na prośbę)","lb":"—"}
KTO={"LinkedIn – profil Angeliki":"Claude in Chrome (komputer Angeliki) / Angelika","LinkedIn – strona Guliwera 4":"Claude in Chrome","LinkedIn – grupy":"Szkic: Chrome → publikuje Angelika",
 "Facebook – strona Guliwera4":"Claude in Chrome (Terminarz Meta)","Facebook – grupy":"Claude in Chrome (wg regulaminu grupy)","Instagram":"Agent social media (Metricool) / Chrome","TikTok":"Agent social media – po założeniu konta",
 "Blog guliwera4.pl":"AUTOMAT: zadanie wt 10:17 (czat Blog)","Newsletter":"Czat Mailing (Mailchimp) – automat po podpięciu listy","Link building":"Czat Dostęp do strony + Angelika (kontakty)"}
rows=[]
def add(d,kamp,kanal,typ,temat,cel,prod,powiaz,fmt,camp):
    s,m=SRC[kanal]
    link=utm(prod,s,m,camp) if prod else ""
    rows.append(dict(d=d,kamp=kamp,kanal=kanal,typ=typ,temat=temat,cel=cel,prod=prod,link=link,powiaz=powiaz,fmt=FMT[fmt],kto=KTO[kanal]))
for wk in W:
    (start,kamp,prod,blog,news,lip,lis,fbs,ig,tt,grupy,lig,lb)=wk
    p=dt.date.fromisoformat(start); camp=slug(kamp)[:28]
    day=lambda i:p+dt.timedelta(days=i)
    bt=f"Wpis tygodnia: „{blog[0]}”"
    add(day(1),kamp,"Blog guliwera4.pl","Artykuł ("+blog[1]+")",blog[0],"SEO / ruch / lead",prod,"HUB tygodnia: linki wew. do produktu, Akademii i 2 starszych wpisów; z wpisu powstają posty LI/FB/IG/TT i newsletter","blog",camp)
    for i,(dzien,t) in enumerate(zip([0,2,4],lip)):
        add(day(dzien),kamp,"LinkedIn – profil Angeliki","Post osobisty (historia → lekcja → pytanie)",t,"Zasięg / obserwujący" if i!=1 else "Zaangażowanie",("akademia" if i==0 else prod if i==1 else "blog"),bt+" – link w 1. komentarzu","post",camp)
    for dzien,t in zip([1,3],lis):
        add(day(dzien),kamp,"LinkedIn – strona Guliwera 4","Karuzela / case",t,"Lead / sprzedaż",prod,bt+"; udostępnić na profilu Angeliki","karuzela",camp)
    add(day(2),kamp,"LinkedIn – grupy","Szkic dyskusji",lig,"Zasięg / relacje","",("Po dyskusji: link do wpisu tylko w odpowiedzi"),"grupa",camp)
    for i,(dzien,t) in enumerate(zip([0,2,4],fbs)):
        add(day(dzien),kamp,"Facebook – strona Guliwera4","Post (wartość / ankieta / kulisy)",t,["Zasięg","Zaangażowanie","Lead"][i],(prod if i!=1 else "akademia"),"MSG-02: „Napisz Guliwera4 w komentarzu – wyślę link” (prośba o PDF)" if i!=1 else bt,"post",camp)
    for dzien,t in zip([1,3],grupy):
        add(day(dzien),kamp,"Facebook – grupy","Post ekspercki (bez linku)",t,"Zasięg / zaufanie","akademia","Link (z UTM grupy) tylko na prośbę w komentarzu","grupa",camp)
    add(day(0),kamp,"Facebook – grupy","CODZIENNIE pn–pt: 5 wartościowych komentarzy","Odpowiadaj na pytania o strategię, ceny, plan – bez linków","Zaufanie / ruch","", "Na prośbę: link do wpisu tygodnia lub Akademii","grupa",camp)
    for (dzien,t,f) in zip([1,3,5,6],ig,["rolka","karuzela","rolka","zdjecie"]):
        add(day(dzien),kamp,"Instagram",{"rolka":"Rolka","karuzela":"Karuzela","zdjecie":"Post ze zdjęciem"}[f],t,"Zasięg / obserwujący" if f=="rolka" else "Zapisanie / lead",("akademia" if f!="karuzela" else prod),"CTA: „link w bio”; relacja z ankietą tego samego dnia; "+bt,f,camp)
    add(day(0),kamp,"Instagram","CODZIENNIE: 3–5 relacji","Kulisy, ankieta, pytanie, udostępnienie posta, link (1×/tydz.)","Relacja / lead","akademia","Wyróżnione: Start tutaj / Akademia / Materiały","rolka",camp)
    for (dzien,t) in zip([1,3,5],tt):
        add(day(dzien),kamp,"TikTok","Krótkie wideo (z rolki IG)",t,"Zasięg / obserwujący","akademia","Ta sama rolka co IG (bez znaku wodnego), CTA: link w bio","rolka",camp)
    add(day(3),kamp,"Newsletter","Newsletter tygodniowy",news,"Sprzedaż / relacja",prod,"Sekcje: 1) wpis tygodnia 2) ćwiczenie 3) produkt tygodnia; link do Akademii w stopce","newsletter",camp)
    for dzien,t in zip([0,2],lb):
        add(day(dzien),kamp,"Link building","Link zewnętrzny",t,"SEO / autorytet / ruch","blog","Anchor: nazwa wpisu lub „Guliwera 4”; link do wpisu tygodnia lub /materialy/","lb",camp)
SW={dt.date(2026,12,24),dt.date(2026,12,25),dt.date(2026,12,26)}
for r in rows:
    if r["d"] in SW and r["kanal"] in ("Newsletter","Link building","LinkedIn – strona Guliwera 4","Facebook – grupy","LinkedIn – grupy","TikTok"):
        r["d"]=dt.date(2026,12,23)
rows.sort(key=lambda r:(r["d"],list(KOL).index(r["kanal"])))
wb=Workbook()
def sheet(ws,title,sub,head,widths,start=3):
    ws["A1"]=title; ws["A1"].font=T; ws["A2"]=sub; ws["A2"].font=Font(name="DM Sans",italic=True,size=10,color="7A6580")
    for i,h in enumerate(head,1):
        c=ws.cell(row=start,column=i,value=h); c.font=H; c.fill=HF; c.alignment=Alignment(wrap_text=True,vertical="center"); c.border=BOX
    for i,w in enumerate(widths,1): ws.column_dimensions[get_column_letter(i)].width=w
    ws.row_dimensions[start].height=32
# 1 Kalendarz
ws=wb.active; ws.title="Kalendarz"
head=["Nr","Data","Dzień","Tydzień","Kampania tygodnia","Kanał","Typ / format","Temat / hook","Cel w lejku","Produkt / CTA","Link główny (UTM)","Powiązanie z innymi kanałami","Kto / automat","Status","Tekst gotowy (wkleja automat)","Brief grafiki / formatu","Wynik (zasięg / kliknięcia / zapisy)"]
sheet(ws,"Guliwera 4 – zintegrowany kalendarz treści (23.11.2026 – 31.01.2027)","Blog = centrum tygodnia. Każdy kanał prowadzi do jednego produktu tygodnia i do Akademii. Linki zawsze z UTM. Status uzupełniają automaty i Claude in Chrome.",head,[5,11,11,8,30,24,26,46,16,26,58,46,30,14,50,40,26])
dv=DataValidation(type="list",formula1='"do napisania,tekst gotowy,grafika gotowa,zaplanowane,opublikowane,pominięte"',allow_blank=True); ws.add_data_validation(dv)
for i,r in enumerate(rows,1):
    rr=i+3
    vals=[i,r["d"],DNI[r["d"].weekday()],f'=\"T\"&WEEKNUM(B{rr},21)',r["kamp"],r["kanal"],r["typ"],r["temat"],r["cel"],L[r["prod"]][0] if r["prod"] else "—",r["link"],r["powiaz"],r["kto"],"do napisania","",r["fmt"],""]
    for j,v in enumerate(vals,1):
        c=ws.cell(row=rr,column=j,value=v); c.font=N; c.alignment=WR; c.border=BOX
        c.fill=PatternFill("solid",fgColor=KOL[r["kanal"]])
    ws.cell(row=rr,column=2).number_format="DD.MM.YYYY"
    dv.add(ws.cell(row=rr,column=14))
last=len(rows)+3
ws.freeze_panes="D4"; ws.auto_filter.ref=f"A3:Q{last}"
ws.conditional_formatting.add(f"N4:N{last}",FormulaRule(formula=[f'$N4="opublikowane"'],fill=PatternFill("solid",fgColor="C9F2DF")))
# 2 Siatka tygodniowa
ws2=wb.create_sheet("Siatka tygodniowa")
sheet(ws2,"Siatka tygodniowa – co i gdzie każdego dnia","Skrót z zakładki Kalendarz (codzienne zadania: komentarze w grupach i relacje IG – pn–pt / codziennie).",["Tydzień od","Kampania"]+[d.capitalize() for d in DNI]+["Liczba pozycji"],[12,30]+[34]*7+[10])
for wi,wk in enumerate(W):
    p=dt.date.fromisoformat(wk[0]); rr=wi+4
    ws2.cell(row=rr,column=1,value=p).number_format="DD.MM.YYYY"; ws2.cell(row=rr,column=2,value=wk[1])
    for d in range(7):
        day=p+dt.timedelta(days=d)
        items=[r for r in rows if r["d"]==day and not r["typ"].startswith("CODZIENNIE")]
        short={"LinkedIn – profil Angeliki":"LI-A","LinkedIn – strona Guliwera 4":"LI-G","LinkedIn – grupy":"LI-gr","Facebook – strona Guliwera4":"FB","Facebook – grupy":"FB-gr","Instagram":"IG","TikTok":"TT","Blog guliwera4.pl":"BLOG","Newsletter":"NL","Link building":"LB"}
        ws2.cell(row=rr,column=3+d,value=day.strftime("%d.%m")+"\n"+"\n".join(f"{short[x['kanal']]}: {x['temat'][:48]}" for x in items))
    ws2.cell(row=rr,column=10,value=f'=COUNTIFS(Kalendarz!$B$4:$B${last},">="&A{rr},Kalendarz!$B$4:$B${last},"<"&A{rr}+7)')
    for c in range(1,11):
        cc=ws2.cell(row=rr,column=c); cc.font=N; cc.alignment=WR; cc.border=BOX
    ws2.row_dimensions[rr].height=150
ws2.freeze_panes="C4"
# 3 Architektura
ws3=wb.create_sheet("Architektura kanałów")
sheet(ws3,"Architektura kanałów – jak wszystko ze sobą gra","Model: HUB (blog + Akademia) → SPOKES (social) → LISTA (newsletter) → SPRZEDAŻ (PDF). Każdy kanał ma jedną rolę i jeden następny krok.",
 ["Kanał","Rola w lejku","Dla kogo","Częstotliwość","Formaty","CTA / następny krok","Kto / automat","KPI kanału","Powiązania (link building wewnętrzny)"],[24,26,28,22,30,34,30,28,52])
ARCH=[
("Blog guliwera4.pl","HUB treści, SEO, długoterminowy ruch z Google","Właścicielki małych firm, młode managerki","1 artykuł / tydzień (wt)","Poradnik, lookbook, case, lista","Akademia (darmowo) → produkt tygodnia","AUTOMAT wt 10:17 (czat Blog)","Wejścia z Google, czas na stronie, kliknięcia w produkt","Każdy wpis: 1 link do produktu, 1 do Akademii, 2 do starszych wpisów, 1 do /materialy/; z każdego wpisu: 3 posty LI, 2 FB, 2 IG, 2 TT, 1 newsletter"),
("Akademia Guliwera (/aplikacja/)","LEAD MAGNET – darmowy start, zbiera kontakty","Wszyscy nowi","stała","Aplikacja, wyzwanie 21 dni (styczeń)","Zapis → maile → Strategia na 1 stronie 39 zł","—","Zapisy / tydzień","Link z każdego posta i bio; stopka bloga; newsletter"),
("Newsletter (Mailchimp)","Relacja i sprzedaż (najwyższa konwersja)","Zapisani z Akademii i strony","1 / tydzień (czw)","3 sekcje: wpis, ćwiczenie, produkt","Produkt tygodnia","Czat Mailing – automat po podpięciu listy","Otwarcia ≥35%, kliknięcia ≥3%, sprzedaż","Linkuje wpis tygodnia, produkt, Akademię; wpis na blogu ma formularz zapisu"),
("LinkedIn – profil Angeliki","Główny silnik zasięgu i zaufania (osobisty > firmowy)","Managerki, właściciele firm B2B","pn / śr / pt","Historia → lekcja → pytanie; zdjęcie Angeliki","Link w 1. komentarzu (UTM) / obserwuj","Claude in Chrome na komputerze Angeliki","Obserwujący, zasięg, kliknięcia","Udostępnia posty strony G4; w sekcji Polecane: Akademia, blog, Strategia na 1 stronie"),
("LinkedIn – strona Guliwera 4","Karuzele i case’y, wiarygodność marki","Jw.","wt / czw","Karuzela PDF, case","Produkt tygodnia","Claude in Chrome","Obserwujący strony, kliknięcia","Udostępniana z profilu; link do wpisu tygodnia"),
("LinkedIn – grupy","Relacje, dyskusje, eksperckość","Grupy managerskie/przedsiębiorców","1 szkic / tydz.","Pytanie do dyskusji","Brak linku – link na prośbę","Szkic Chrome, publikuje Angelika","Komentarze, zaproszenia do sieci","—"),
("Facebook – strona Guliwera4","Społeczność, prośby o PDF przez Messenger","Mikroprzedsiębiorczynie, lokalnie Lublin","pn / śr / pt","Wartość, ankieta, kulisy","„Napisz Guliwera4 w komentarzu” → Messenger z linkiem","Claude in Chrome (Terminarz Meta)","Obserwujący, komentarze, prośby o PDF","Link do wpisu tygodnia; udostępnienia do grup"),
("Facebook – grupy","Największy darmowy zasięg do grupy docelowej","Grupy przedsiębiorczyń, marketingu, Lublin","codziennie 5 komentarzy + 2 posty / tydz.","Odpowiedź-ekspert, mini-poradnik","Link tylko na prośbę (UTM grupy)","Claude in Chrome","Polubienia strony, prośby o link","—"),
("Instagram","Marka osobista, twarz, styl (moda + biznes)","Kobiety 25–45 rozwijające się zawodowo","4 posty / tydz. + relacje codziennie","Rolki 2×, karuzela 1×, zdjęcie 1×","Link w bio (Akademia) / zapisz post","Agent social media (Metricool)","Obserwujący, zapisania, kliknięcia w bio","Lookbooki z bloga; rolki → TikTok; relacje udostępniają post LI/blog"),
("TikTok","Zasięg do nowych odbiorców (krótkie wideo)","Młode managerki, studentki, początkujące","3 / tydz. (wt / czw / sob)","Rolki z IG bez znaku wodnego","Link w bio","Agent social media – po założeniu konta","Obserwujący, wyświetlenia","Te same scenariusze co IG"),
("Pinterest (nowy)","Ruch do lookbooków i poradników z bloga","Moda biznesowa, planowanie","po każdym wpisie: 3 piny","Pin 1000×1500","Link do wpisu","Agent social media","Kliknięcia do bloga","Pin → wpis → Akademia"),
("Link building zewnętrzny","Autorytet domeny i ruch z poleceń","—","2 zadania / tydz.","Katalogi, wizytówka Google, gościnne wpisy, podcasty, media lokalne, partnerzy","Link do wpisu / materiałów","Czat Dostęp do strony + Angelika","Liczba linków, ruch z referral","Anchor naturalny; bez kupowania linków i farm"),
]
for i,a in enumerate(ARCH):
    for j,v in enumerate(a,1):
        c=ws3.cell(row=i+4,column=j,value=v); c.font=N; c.alignment=WR; c.border=BOX
    ws3.row_dimensions[i+4].height=78
ws3.freeze_panes="B4"
# 4 Mapa linków / UTM
ws4=wb.create_sheet("Mapa linków (UTM)")
sheet(ws4,"Mapa linków i UTM","Zasada: utm_source = kanał, utm_medium = miejsce, utm_campaign = temat tygodnia (z kolumny E kalendarza). Wejścia sprawdzamy w statystykach Jetpack/GA4.",["Klucz","Cel","Adres","Typ","LinkedIn profil","Facebook strona","Facebook grupa","Instagram bio","TikTok bio","Newsletter","Użyte w kalendarzu"],[12,44,46,18,58,58,58,58,58,58,12])
for i,(k,(opis,path,typ)) in enumerate(L.items()):
    rr=i+4; vals=[k,opis,B+path,typ]+[utm(k,s,m,"KAMPANIA") for s,m in [("linkedin","profil"),("facebook","strona"),("facebook","grupa"),("instagram","bio"),("tiktok","bio"),("newsletter","email")]]
    for j,v in enumerate(vals,1):
        c=ws4.cell(row=rr,column=j,value=v); c.font=N; c.alignment=WR; c.border=BOX
    ws4.cell(row=rr,column=11,value=f'=COUNTIF(Kalendarz!$J$4:$J${last},B{rr})').font=N
# 5 Link building
ws5=wb.create_sheet("Link building")
sheet(ws5,"Link building – zewnętrzny i wewnętrzny","Wewnętrzny: zasady obowiązują każdy wpis. Zewnętrzny: lista stała + zadania tygodniowe z kalendarza.",["Rodzaj","Działanie","Cel linku","Anchor / sposób","Kto","Termin","Status","Link (po realizacji)"],[16,60,34,34,26,14,14,40])
LB=[("Wewnętrzny","Każdy wpis: link do produktu tygodnia (CTA w treści)","/produkt/…","nazwa produktu","Automat bloga","każdy wpis"),
("Wewnętrzny","Każdy wpis: link do Akademii (box pod wpisem – automatycznie)","/aplikacja/","„Zacznij za darmo”","Szablon strony","stale"),
("Wewnętrzny","Każdy wpis: 2 linki do starszych wpisów + aktualizacja 1 starszego wpisu linkiem do nowego","/blog/…","naturalny w zdaniu","Automat bloga","każdy wpis"),
("Wewnętrzny","Strony produktów: sekcja „Przeczytaj też” z 2 wpisami (po 4 wpisach)","/blog/…","tytuł wpisu","Dostęp do strony","po 4 wpisach"),
("Zewnętrzny","Wizytówka Google (Google Business Profile): Guliwera 4, Lublin, link do strony, posty co tydzień","/","Guliwera 4","Angelika (weryfikacja) + Dostęp do strony","do 30.11"),
("Zewnętrzny","Katalogi firm: Panorama Firm, Aleo, Oferteo, Biznes.gov (CEIDG link do www), katalogi Lublin","/ i /kontakt/","Guliwera 4","Dostęp do strony","do 6.12"),
("Zewnętrzny","Profile społecznościowe: link w bio IG/TikTok/LinkedIn/FB/Pinterest (UTM)","/aplikacja/","—","Agent social","do 30.11"),
("Zewnętrzny","Artykuły gościnne: 1 / 2 tygodnie (portale dla kobiet w biznesie, blogi partnerów)","wpis na blogu","tytuł/temat","Dostęp do strony (pitch) + Angelika","co 2 tyg."),
("Zewnętrzny","Podcasty i media lokalne (Lublin): 3 zgłoszenia / miesiąc","/ lub wpis","imię i nazwisko + Guliwera 4","Angelika (wystąpienia)","co miesiąc"),
("Zewnętrzny","Partnerstwa: wymiana poleceń z 3–5 twórcami/firmami (newslettery, wyzwanie styczniowe)","/aplikacja/","naturalny","Angelika + Dostęp do strony","grudzień–styczeń"),
("Zewnętrzny","Treść do cytowania: ankieta „Plany małych firm 2027” → raport na blogu → komunikat prasowy","wpis z raportem","„ankieta Guliwera 4”","Dostęp do strony","28.12–8.01"),
("Zasada","Bez kupowania linków, farm i wymiany na siłę; linki tematyczne i naturalne (zgodnie z wytycznymi Google)","—","—","Wszyscy","stale")]
r0=4
for i,x in enumerate(LB):
    for j,v in enumerate(list(x)+["do zrobienia",""],1):
        c=ws5.cell(row=r0+i,column=j,value=v); c.font=N; c.alignment=WR; c.border=BOX
r1=r0+len(LB)+1
ws5.cell(row=r1,column=1,value="Zadania tygodniowe (z kalendarza)").font=Font(name="DM Sans",bold=True,color=ATR)
for i,r in enumerate([r for r in rows if r["kanal"]=="Link building"]):
    for j,v in enumerate(["Zewnętrzny",r["temat"],"wpis tygodnia / /materialy/","naturalny","Dostęp do strony + Angelika",r["d"],"do zrobienia",""],1):
        c=ws5.cell(row=r1+1+i,column=j,value=v); c.font=N; c.alignment=WR; c.border=BOX
    ws5.cell(row=r1+1+i,column=6).number_format="DD.MM.YYYY"
# 6 KPI
ws6=wb.create_sheet("KPI")
sheet(ws6,"KPI do 31.12.2026 – cele i tygodniowe rozliczenie","Cel główny: 100 płatnych pobrań PDF ze strony + 1000 obserwujących na każdym kanale. Uzupełniane co poniedziałek przez automat raportu.",
 ["Tydzień od","Wejścia na stronę","w tym z social (UTM)","Zapisy do Akademii","Sprzedane PDF (szt.)","Sprzedane PDF – narastająco","LinkedIn profil – obserwujący","LinkedIn strona","Facebook strona","Instagram","TikTok","Newsletter – subskrybenci","Uwagi / decyzje"],[12,12,12,12,12,14,14,12,12,12,12,14,46])
for i,wk in enumerate(W):
    rr=i+4; ws6.cell(row=rr,column=1,value=dt.date.fromisoformat(wk[0])).number_format="DD.MM.YYYY"
    ws6.cell(row=rr,column=6,value=f"=SUM($E$4:E{rr})")
    for c in range(1,14):
        cc=ws6.cell(row=rr,column=c); cc.font=N; cc.border=BOX
rt=len(W)+5
ws6.cell(row=rt,column=1,value="CEL 31.12").font=Font(name="DM Sans",bold=True,color=KORAL)
for c,v in zip(range(5,13),[None,100,1000,1000,1000,1000,1000,None]):
    if v: ws6.cell(row=rt,column=c,value=v).font=Font(name="DM Sans",bold=True,color=KORAL)
ws6.cell(row=rt+2,column=1,value="Uczciwa ocena (stan 18.11): ok. 15 osób/tydz. na stronie, ~0 obserwujących. 100 sprzedaży przy konwersji 1–3% wymaga ok. 3 300–10 000 wejść w 6 tygodni; 1000 obserwujących na każdym kanale w 6 tygodni od zera wymaga płatnej promocji (kampanie na obserwujących/zasięg) i współprac. Plan organiczny + płatny: patrz zakładka Plan dojścia do KPI.").alignment=WR
ws6.merge_cells(start_row=rt+2,start_column=1,end_row=rt+2,end_column=13); ws6.row_dimensions[rt+2].height=60
# 7 Plan dojścia
ws7=wb.create_sheet("Plan dojścia do KPI")
sheet(ws7,"Plan dojścia do KPI (100 PDF + 1000 obserwujących / kanał)","Dźwignie w kolejności wpływu. Bez części płatnej cel 1000/kanał do 31.12 jest bardzo mało prawdopodobny – decyzja o budżecie: Angelika.",["Dźwignia","Co robimy","Efekt szacunkowy do 31.12","Koszt","Kto","Decyzja"],[26,70,34,18,28,16])
PL=[("1. Konwersja na stronie","Darmowa Akademia jako wejście; pop-up/sekcja zapisu na blogu; strona produktu 39 zł jako „pierwszy krok”; mail powitalny z ofertą 39 zł po zapisie","x2–3 sprzedaży z tego samego ruchu","0 zł","Dostęp do strony + Mailing","—"),
("2. Prośby o PDF w social (Messenger)","2 posty FB/tydz. z „Napisz Guliwera4” + automat Messenger z linkiem; 1 post LI z linkiem w 1. komentarzu","20–40 kliknięć do produktów / tydz.","0 zł","Claude in Chrome","działa"),
("3. Profil osobisty LinkedIn","3 posty/tydz. + 20 komentarzy dziennie u osób z grupy docelowej + 20 zaproszeń dziennie do sieci (limit LinkedIn)","+300–600 obserwujących","0 zł","Chrome / Angelika","—"),
("4. Grupy FB","5 komentarzy dziennie + 2 posty eksperckie/tydz.","ruch 50–150 wejść / tydz.","0 zł","Claude in Chrome","działa"),
("5. Meta Ads – obserwujący IG/FB","Kampania „aktywność profilu / obserwujący” na najlepsze rolki, PL, kobiety 25–45, zainteresowania biznes/przedsiębiorczość","1000 obserwujących IG ≈ 800–2000 zł; FB strona podobnie","ok. 30–60 zł/dzień","Czat Reklamy","Angelika: budżet?"),
("6. Meta/Google Ads – sprzedaż 39 zł i Akademia","Kampania konwersji na Strategia na 1 stronie (39 zł) i zapisy; remarketing odwiedzających","30–60 sprzedaży przy 1 500–3 000 zł budżetu (do weryfikacji po 7 dniach)","ok. 50 zł/dzień","Czat Reklamy","Angelika: budżet?"),
("7. TikTok + IG rolki","3 TT + 2 rolki IG/tydz. z tych samych nagrań; trendy dźwięki; serie","organicznie 100–500 / kanał","0 zł","Agent social","konto TT do założenia"),
("8. Partnerstwa i współprace","Wymiana poleceń z 3–5 twórcami, wspólne live, giveaway (zgodny z regulaminem Meta)","+200–500 obserwujących na kanał partnera","0–300 zł","Angelika + Dostęp do strony","—"),
("9. Grudniowa oferta czasowa","Np. Strategia na 1 stronie + Podsumowanie roku w zestawie (cena do decyzji), ograniczona do 31.12","x1,5–2 konwersji w grudniu","0 zł","Angelika (cena)","Angelika: zgoda?"),]
for i,x in enumerate(PL):
    for j,v in enumerate(x,1):
        c=ws7.cell(row=i+4,column=j,value=v); c.font=N; c.alignment=WR; c.border=BOX
    ws7.row_dimensions[i+4].height=58
# 8 Automatyzacja
ws8=wb.create_sheet("Automatyzacja")
sheet(ws8,"Co jest zautomatyzowane","Zadania zaplanowane (claude.ai → Scheduled) i stałe procesy.",["Kiedy","Zadanie","Co robi","Gdzie wynik"],[22,40,80,40])
AU=[("codziennie 10:30","Claude in Chrome (komputer Angeliki) – czat „Instrukcja z pliku PRZEKAZANIE”","Publikuje posty FB/LinkedIn z kalendarza, komentuje w grupach wg regulaminów, wysyła linki PDF w Messengerze","Kalendarz Meta / LinkedIn"),
("codziennie 12:17","Kontrola PDF Guliwera 4","Sprawdza prośby o PDF, czy każda osoba dostała link w 24 h; powiadomienie push","Powiadomienie"),
("poniedziałek 7:41","Miniraport tygodniowy","Raport tygodnia, news na czwartek, uzupełnianie tekstów postów","Kalendarz (artefakt) + wiadomość"),
("poniedziałek 8:23","NOWE: Marketing manager – KPI i kalendarz","Zbiera liczby (strona, UTM, sprzedaż PDF, obserwujący), uzupełnia zakładkę KPI, rozdziela zadania tygodnia do czatów, zgłasza odchylenia","KPI + wiadomość do Angeliki"),
("wtorek 10:17","Blog – cotygodniowy artykuł","Pisze i publikuje artykuł tygodnia (temat z tego kalendarza), wysyła link agentowi social","guliwera4.pl/blog/"),
("niedziela 18:41","Posty FB i LinkedIn – plan na tydzień","Przygotowuje teksty postów na kolejny tydzień z tego kalendarza","Kalendarz / pliki social")]
for i,x in enumerate(AU):
    for j,v in enumerate(x,1):
        c=ws8.cell(row=i+4,column=j,value=v); c.font=N; c.alignment=WR; c.border=BOX
    ws8.row_dimensions[i+4].height=46
# 9 Zasady
ws9=wb.create_sheet("Zasady")
ws9["A1"]="Zasady planu"; ws9["A1"].font=T; ws9.column_dimensions["A"].width=140
Z=["• Okres: 23.11.2026 – 31.01.2027 (10 tygodni). Kampania tygodnia = 1 temat, 1 produkt, 1 wpis na blogu jako centrum.",
"• Każda treść ma jeden cel w lejku i jeden następny krok (CTA). Darmowa Akademia jest zawsze drugim wyborem dla osób niegotowych na zakup.",
"• Wszystkie linki z UTM (zakładka Mapa linków). Bez UTM nie wiemy, co działa.",
"• Na zdjęciach tylko prawdziwa twarz Angeliki (brand/zdjecia-sesja 09 i 07, logo/angelika-portret-bialy.jpg). Zdjęcia generowane – oznaczać w reklamach zgodnie z zasadami Meta.",
"• Grupy: najpierw wartość, link tylko na prośbę i zgodnie z regulaminem grupy. LinkedIn grupy (profil osobisty): publikuje Angelika.",
"• Bez obietnic dochodu. Fakty i ceny sprawdzane w dniu publikacji, ze źródłem.",
"• Identyfikacja: kolory krem/koral/róż/liliowy/żółty/atrament/granat, fonty Fraunces/DM Sans/Caveat (brandbook: repo Guliwera4, gałąź claude/brandbook).",
"• Nie wymieniamy nazw firm, z którymi pracowała Angelika. Nie korzystamy z danych Grupy Azoty ani repozytorium gaia.",
"• Materiały przygotowywane z pomocą AI – informacja w regulaminie, w PDF i w stopce wpisów."]
for i,z in enumerate(Z): ws9.cell(row=i+3,column=1,value=z).font=N
wb.save("Guliwera4_Kalendarz_tresci_XI2026-I2027.xlsx")
print(len(rows),"pozycji")
from collections import Counter; print(Counter(r["kanal"] for r in rows))
