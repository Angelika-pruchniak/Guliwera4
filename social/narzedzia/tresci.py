#!/usr/bin/env python3
"""Guliwera 4 · generator plików HTML do grafik Instagram (karuzele, nakładki rolek).
Uruchom z katalogu repozytorium:  python3 social/narzedzia/tresci.py
Potem PNG:                         node social/narzedzia/render.js
Treści zmieniaj TUTAJ, nie w wygenerowanych plikach HTML (social/instagram/html/)."""
import os, json, html

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))  # social/
OUT = os.path.join(ROOT, 'instagram', 'html')
os.makedirs(OUT, exist_ok=True)
FOTO = '../../../brand/zdjecia-sesja/'
Z = '../../zasoby/'

HEAD = f'''<!doctype html><html lang="pl"><head><meta charset="utf-8"><title>{{t}}</title>
<link rel="stylesheet" href="{Z}styl.css"><style>
html,body{{padding:0;margin:0;background:transparent}}
.marka img{{height:58px}}
.ramka{{margin-top:48px;background:var(--zolty-jasny);border-radius:28px;padding:34px 40px;font-size:32px;line-height:1.42}}
.ramka.lil{{background:var(--liliowy-jasny)}}.ramka.roz{{background:var(--roz-jasny)}}.ramka.brz{{background:var(--brzoskwinia)}}
.liczba{{font-family:var(--naglowek);font-weight:700;color:var(--koral-ciemny);line-height:1}}
.tab{{width:100%;border-collapse:collapse;font-size:38px;margin-top:40px}}
.tab td{{padding:22px 8px;border-bottom:2px solid var(--brzoskwinia)}}.tab td:last-child{{text-align:right;font-family:var(--naglowek);font-weight:700;color:var(--koral-ciemny)}}
.tab tr:first-child td{{font-size:24px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--atrament-60);font-family:var(--tekst)}}
</style></head><body>'''
FOOT = '</body></html>'


def stopka(nr, razem, jasna=False):
    znak = 'znak-g4-bialy.svg' if jasna else 'znak-g4.svg'
    return (f'<div class="stopka"><span class="marka"><img src="{Z}{znak}" alt="">'
            f'<span>@angelika.guliwera4</span></span><span>{nr} / {razem}</span></div>')


def okladka(k, razem):
    kolo = k.get('kolo', 'var(--roz-jasny)')
    return f'''<div class="plotno f-ig45 t-krem">
<div class="kolo" style="width:820px;height:820px;background:{kolo};right:-330px;top:-300px"></div>
<div class="portret" style="position:absolute;right:80px;top:130px;width:360px;height:480px;z-index:2;box-shadow:0 30px 60px rgba(58,35,64,.12)">
<img src="{FOTO}{k['foto']}" style="object-position:{k.get('pozycja','50% 18%')}"></div>
<div class="pole">
  <span class="pigulka p-granat">{k['pigulka']}</span>
  <p class="h1" style="margin-top:auto;max-width:540px;font-size:{k.get('rozmiar',100)}px">{k['tytul']}</p>
  <p class="lead" style="margin-top:28px;max-width:24ch">{k['lead']}</p>
  <div style="height:50px"></div>
  {stopka(1, razem)}
</div></div>'''


def slajd(s, nr, razem):
    tlo = s.get('tlo', 't-bialy')
    ramka = ''
    if s.get('ramka'):
        ramka = f'<div class="ramka {s.get("ramka_kolor","")}">{s["ramka"]}</div>'
    extra = s.get('extra', '')
    return f'''<div class="plotno f-ig45 {tlo}">
<div class="pole srodek">
  <span class="etykieta" style="color:var(--koral-ciemny)">{s['etykieta']}</span>
  <p class="h2" style="margin-top:24px">{s['tytul']}</p>
  <p class="tekst" style="margin-top:32px;max-width:27ch">{s['tekst']}</p>
  {extra}{ramka}
  {stopka(nr, razem)}
</div></div>'''


def koniec(e, nr, razem):
    return f'''<div class="plotno f-ig45 t-granat">
<div class="kolo" style="width:640px;height:640px;background:rgba(185,165,227,.22);right:-240px;top:-220px"></div>
<div class="pole srodek">
  <span class="reka">{e['reka']}</span>
  <p class="h1" style="margin-top:20px;font-size:86px;max-width:13ch">{e['tytul']}</p>
  <p class="lead" style="margin-top:28px;max-width:24ch">{e['lead']}</p>
  <span class="przycisk" style="margin-top:46px">{e['przycisk']}</span>
  <p class="tekst" style="margin-top:22px;font-size:30px;color:rgba(255,246,238,.8)">{e.get('pod','link w bio · guliwera4.pl/aplikacja')}</p>
  <img src="{Z}lilek-macha.svg" alt="" style="position:absolute;right:-10px;bottom:120px;width:270px">
  {stopka(nr, razem, jasna=True)}
</div></div>'''


KONIEC_AKADEMIA = dict(
    reka='chcesz to przećwiczyć?',
    tytul='Zacznij za darmo w Akademii',
    lead='Krótkie lekcje strategii i liczb dla małych firm. W telefonie, bez zakładania firmy od nowa.',
    przycisk='Zacznij za darmo')

KARUZELE = {
 'k1-strategia-na-1-stronie': dict(
   okladka=dict(foto='10-notes-sukienka-blekit.jpg', pigulka='zapisz na poniedziałek', pozycja='50% 14%',
                tytul='Strategia firmy na <span class="zakres">1 stronie</span>',
                lead='5 pytań, które porządkują więcej niż 40 slajdów. Przesuń →'),
   slajdy=[
    dict(etykieta='pytanie 1', tytul='Dla kogo jesteśmy?',
         tekst='Jedno zdanie o kliencie, któremu pomagasz najlepiej. Nie „wszyscy”. Konkretna osoba z konkretnym problemem.',
         ramka='<b>Przykład:</b> właścicielki małych salonów, które mają pełny grafik, a na koniec miesiąca pusto na koncie.'),
    dict(etykieta='pytanie 2', tytul='Za co naprawdę nam płacą?', tlo='t-krem',
         tekst='Nie za to, co robisz. Za problem, który znika. To jest Twoja obietnica.',
         ramka='<b>Sprawdź:</b> jak klienci opisują Cię znajomym? Ich słowa są lepsze niż slogan.', ramka_kolor='lil'),
    dict(etykieta='pytanie 3', tytul='Na czym zarabiamy najwięcej?',
         tekst='Wypisz produkty i marżę każdego z nich. Zwykle mała część oferty robi większość zysku.',
         ramka='Produkt, który sprzedajesz najczęściej, i ten, który zostawia najwięcej w kieszeni, to często <b>nie ten sam</b> produkt.', ramka_kolor='roz'),
    dict(etykieta='pytanie 4', tytul='Czego NIE robimy?', tlo='t-krem',
         tekst='Strategia to wybór. Lista „nie robimy” chroni Twój czas lepiej niż najładniejszy kalendarz.',
         ramka='<b>Np.:</b> nie bierzemy zleceń poniżej progu opłacalności. Nie otwieramy nowego kanału przed końcem kwartału.'),
    dict(etykieta='pytanie 5', tytul='Jedna liczba na kwartał',
         tekst='Wybierz jeden wskaźnik, który mówi, czy idziesz w dobrą stronę. Sprawdzaj go co tydzień, zawsze tego samego dnia.',
         ramka='<b>Np.:</b> zysk na jednym kliencie, odsetek klientów, którzy wracają, marża brutto.', ramka_kolor='brz'),
   ],
   koniec=KONIEC_AKADEMIA),

 'k2-marza-a-narzut': dict(
   okladka=dict(foto='06-laptop-garnitur.jpg', pigulka='liczby bez stresu', pozycja='42% 40%', kolo='var(--zolty-jasny)',
                tytul='Narzut 50% to nie <span class="zakres">marża 50%</span>', rozmiar=92,
                lead='Najczęstszy błąd w cenniku małej firmy. Przesuń →'),
   slajdy=[
    dict(etykieta='narzut', tytul='Liczysz go od kosztu',
         tekst='Kupujesz albo wytwarzasz za 100 zł. Doliczasz 50% narzutu.',
         extra='<p style="margin-top:40px"><span class="liczba" style="font-size:150px">150 zł</span></p><p class="tekst" style="color:var(--atrament-60)">cena dla klienta</p>'),
    dict(etykieta='marża', tytul='Liczysz ją od ceny', tlo='t-krem',
         tekst='Ze 150 zł zostaje Ci 50 zł. Dzielisz przez cenę, nie przez koszt.',
         extra='<p style="margin-top:40px"><span class="liczba" style="font-size:150px">33%</span></p><p class="tekst" style="color:var(--atrament-60)">50 zł ÷ 150 zł. Nie 50%.</p>'),
    dict(etykieta='dlaczego to boli', tytul='Brakuje prawie 17 zł z każdej stówki',
         tekst='Planujesz koszty firmy przy 50% marży, a masz 33%. Różnica wychodzi dopiero na koncie.',
         ramka='Przy 20 000 zł sprzedaży miesięcznie to ok. <b>3 300 zł</b> mniej, niż zakładał plan.', ramka_kolor='roz'),
    dict(etykieta='ściąga', tytul='Jaki narzut dla jakiej marży?', tlo='t-krem', tekst='Zapisz i wróć przy następnym cenniku.',
         extra='<table class="tab"><tr><td>chcesz marży</td><td>ustaw narzut</td></tr><tr><td>30%</td><td>43%</td></tr><tr><td>40%</td><td>67%</td></tr><tr><td>50%</td><td>100%</td></tr></table>'),
   ],
   koniec=dict(KONIEC_AKADEMIA, lead='W Akademii są lekcje o kosztach, zysku i progu rentowności. 5 minut dziennie.')),

 'k3-trzy-liczby-w-poniedzialek': dict(
   okladka=dict(foto='02-kanapa-blekit.jpg', pigulka='rytuał na poniedziałek', kolo='var(--liliowy-jasny)',
                tytul='3 liczby, które sprawdzam <span class="zakres">co tydzień</span>', rozmiar=92,
                lead='10 minut przy kawie zamiast nerwów na koniec miesiąca. Przesuń →', pozycja='47% 30%'),
   slajdy=[
    dict(etykieta='liczba 1', tytul='Gotówka ÷ koszty stałe',
         tekst='Na ile miesięcy wystarczy pieniędzy na koncie, gdyby sprzedaż stanęła?',
         ramka='Poniżej 3 miesięcy zapala mi się lampka. Wtedy najpierw porządkuję koszty, dopiero potem planuję nowe projekty.'),
    dict(etykieta='liczba 2', tytul='Marża brutto z tygodnia', tlo='t-krem',
         tekst='Nie sam przychód. Ile zostaje po kosztach bezpośrednich: materiałach, prowizjach, podwykonawcach.',
         ramka='Przychód rośnie, a marża spada? To sygnał, że rabaty albo koszty zjadają wzrost.', ramka_kolor='lil'),
    dict(etykieta='liczba 3', tytul='Rozmowy w toku',
         tekst='Ile osób jest teraz „w drodze” do zakupu: zapytania, oferty, umówione rozmowy.',
         ramka='Dzisiejsze rozmowy to sprzedaż za miesiąc. Pusto teraz = trudny miesiąc później.', ramka_kolor='brz'),
    dict(etykieta='jak to robię', tytul='Jedna tabelka, zawsze ten sam dzień', tlo='t-krem',
         tekst='Trzy wiersze, kolumna na każdy tydzień. Patrzę na kierunek, nie na pojedynczy wynik.',
         ramka='Wolisz gotowy szablon? <b>Strategia na 1 stronie</b> (39 zł) ma miejsce na Twoje liczby i cel kwartału.'),
   ],
   koniec=KONIEC_AKADEMIA),

 'k4-nowy-produkt-6-pytan': dict(
   okladka=dict(foto='07-portret-boucle.jpg', pigulka='zanim wydasz budżet', kolo='var(--brzoskwinia)',
                tytul='Nowy produkt? <span class="zakres">6 pytań</span> przed premierą', rozmiar=90,
                lead='Wprowadzałam produkty na rynek. Te pytania oszczędzają najwięcej. Przesuń →', pozycja='50% 18%'),
   slajdy=[
    dict(etykieta='pytanie 1', tytul='Kto kupi jako pierwszy?', tekst='Wypisz 10 konkretnych osób albo firm. Nie potrafisz? To sygnał, że rynek jest jeszcze mglisty.'),
    dict(etykieta='pytanie 2', tytul='Jaki problem znika?', tlo='t-krem', tekst='Jedno zdanie językiem klienta, nie językiem funkcji.',
         ramka='„Wiem, na czym zarabiam” brzmi lepiej niż „moduł raportów finansowych”.', ramka_kolor='lil'),
    dict(etykieta='pytanie 3', tytul='Ile musi kosztować?', tekst='Policz próg rentowności: ile sztuk musisz sprzedać, żeby pokryć koszty wejścia.',
         ramka='Koszty stałe ÷ (cena − koszt jednostkowy) = liczba sztuk do zera.'),
    dict(etykieta='pytanie 4', tytul='Jak sprawdzę popyt, zanim zbuduję?', tlo='t-krem', tekst='Przedsprzedaż, lista oczekujących, rozmowy z klientami. Pieniądze i konkretne „tak” mówią więcej niż lajki.'),
    dict(etykieta='pytanie 5', tytul='Gdzie klient się o tym dowie?', tekst='Jeden główny kanał na start. Lepiej jeden dobrze niż pięć po trochu.', ramka_kolor='roz',
         ramka='Najtańszy kanał to zwykle ten, w którym już masz uwagę ludzi.'),
    dict(etykieta='pytanie 6', tytul='Po czym poznam, że działa?', tlo='t-krem', tekst='Ustal liczbę i datę przed premierą. Po 30 dniach decyzja: rozwijam, poprawiam albo zamykam.'),
   ],
   koniec=dict(KONIEC_AKADEMIA, reka='zapisz przed premierą',
               lead='Darmowa Akademia: próg rentowności, macierz BCG, priorytety. Krok po kroku.')),
}

# Nakładki tekstowe do rolek (przezroczyste PNG 1080×1920; strefy bezpieczne: góra 250 px, dół 340 px)
def nakladka(tekst, styl='dol', maly=''):
    if styl == 'hook':
        return f'''<div class="plotno f-story" style="background:transparent">
<div style="position:absolute;left:80px;right:80px;bottom:400px;z-index:3">
<div style="background:var(--krem);border-radius:36px;padding:44px 50px;box-shadow:0 20px 50px rgba(58,35,64,.18)">
<p class="h1" style="font-size:78px">{tekst}</p>{f'<p class="reka" style="margin:16px 0 0;font-size:56px">{maly}</p>' if maly else ''}</div></div></div>'''
    if styl == 'koniec':
        return f'''<div class="plotno f-story t-krem">
<div class="kolo" style="width:900px;height:900px;background:var(--roz-jasny);right:-380px;top:-300px"></div>
<div class="kolo" style="width:700px;height:700px;background:var(--liliowy-jasny);left:-300px;bottom:-260px"></div>
<div style="position:absolute;left:90px;right:90px;top:360px;z-index:3;display:flex;flex-direction:column">
<span class="reka" style="font-size:64px">{maly}</span>
<p class="h1" style="margin-top:20px;font-size:104px">{tekst}</p>
<p class="lead" style="margin-top:34px;font-size:44px">Akademia Guliwera · krótkie lekcje strategii i liczb</p>
<span class="przycisk" style="margin-top:56px;font-size:42px">link w bio</span>
<img src="{Z}logo-osobiste.svg" style="margin-top:120px;width:760px" alt="">
</div>
<img src="{Z}lilek-macha.svg" style="position:absolute;right:70px;bottom:380px;width:300px;z-index:3" alt=""></div>'''
    return f'''<div class="plotno f-story" style="background:transparent">
<div style="position:absolute;left:80px;right:80px;bottom:400px;z-index:3">
{f'<span class="pigulka p-zolta" style="margin-bottom:18px;font-size:30px">{maly}</span>' if maly else ''}
<div style="background:rgba(255,246,238,.96);border-radius:32px;padding:38px 46px;box-shadow:0 20px 50px rgba(58,35,64,.18)">
<p class="h2" style="font-size:66px">{tekst}</p></div></div></div>'''


ROLKI = {
 'r1-trzy-pytania': [
   ('04-telefon-kobalt.jpg', 'hook', 'Zanim powiesz „tak” nowemu projektowi…', 'zadaj sobie 3 pytania'),
   ('03-laptop-zakiet-tweed.jpg', 'dol', 'Czy to przybliża mnie do celu na ten kwartał?', 'pytanie 1'),
   ('06-laptop-garnitur.jpg', 'dol', 'Co przestanę robić, żeby to zmieścić?', 'pytanie 2'),
   ('05-szkolenia-sukienka.jpg', 'dol', 'Ile to zarobi i kiedy?', 'pytanie 3'),
   (None, 'koniec', 'Zacznij za darmo', 'zapisz na później'),
 ],
 'r2-etat-i-wlasna-marka': [
   ('08-rozowy-garnitur-paryskie-biuro.jpg', 'hook', 'Etat + własna marka. Co mi naprawdę pomaga:', 'bez ściemy'),
   ('10-notes-sukienka-blekit.jpg', 'dol', 'Jeden cel na kwartał. Nie dziesięć.', '1'),
   ('09-kanapa-bialy-garnitur.jpg', 'dol', 'Stałe bloki w kalendarzu na własną markę.', '2'),
   ('07-portret-boucle.jpg', 'dol', 'Produkty cyfrowe zamiast sprzedawania godzin.', '3'),
   (None, 'koniec', 'A co pomaga Tobie?', 'napisz w komentarzu'),
 ],
 'r3-zacznij-za-darmo': [
   ('05-szkolenia-sukienka.jpg', 'hook', 'Strategia i liczby dla małych firm. Po ludzku.', 'cześć, jestem Angelika'),
   ('07-portret-boucle.jpg', 'dol', 'Krótkie lekcje: kilka minut dziennie w telefonie.', 'Akademia Guliwera'),
   ('06-laptop-garnitur.jpg', 'dol', 'Zysk, próg rentowności, macierz BCG, priorytety.', 'czego się nauczysz'),
   ('09-kanapa-bialy-garnitur.jpg', 'dol', 'Bez żargonu. Z przykładami z małych firm.', 'jak'),
   (None, 'koniec', 'Zacznij za darmo', 'link w bio'),
 ],
}


def zapisz(nazwa, body, tytul):
    with open(os.path.join(OUT, nazwa + '.html'), 'w') as f:
        f.write(HEAD.replace('{t}', html.escape(tytul)) + body + FOOT)


lista = []
for kid, k in KARUZELE.items():
    razem = len(k['slajdy']) + 2
    zapisz(f'{kid}-01', okladka(k['okladka'], razem), kid); lista.append(dict(n=f'{kid}-01', w=1080, h=1350))
    for i, s in enumerate(k['slajdy'], start=2):
        zapisz(f'{kid}-{i:02d}', slajd(s, i, razem), kid); lista.append(dict(n=f'{kid}-{i:02d}', w=1080, h=1350))
    zapisz(f'{kid}-{razem:02d}', koniec(k['koniec'], razem, razem), kid); lista.append(dict(n=f'{kid}-{razem:02d}', w=1080, h=1350))

for rid, sceny in ROLKI.items():
    for i, (foto, styl, tekst, maly) in enumerate(sceny, start=1):
        n = f'{rid}-tekst-{i}'
        zapisz(n, nakladka(tekst, styl, maly), rid)
        lista.append(dict(n=n, w=1080, h=1920, przezroczyste=(styl != 'koniec')))

with open(os.path.join(OUT, 'lista.json'), 'w') as f:
    json.dump(dict(pliki=lista, rolki={r: [s[0] for s in v] for r, v in ROLKI.items()}), f, ensure_ascii=False, indent=1)
print(len(lista), 'plików HTML w', OUT)
