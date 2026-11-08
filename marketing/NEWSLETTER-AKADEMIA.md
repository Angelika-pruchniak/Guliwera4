# Newsletter dla osób z Akademii Guliwera: plan na listopad 2026 – styczeń 2027

Źródło: `marketing/newsletter_akademia.py` (z niego powstaje ten plik i strona z planem). Stan: 8.11.2026.

**Teza:** Darmowy Pakiet startowy + newsletter zamieniają osobę, która zajrzała do Akademii, w kontakt na liście. Lista sprzedaje kolejne szczeble: 39 zł → Pakiet 297 zł → Akademia Pro 249 zł → kurs wideo.

## KPI

| KPI | Cel | Uwagi |
|---|---|---|
| Nowe zapisy na listę (z potwierdzeniem) | ≥ 110 / mies. od grudnia; 250 osób do 31.12.2026 | KPI nr 1 ze strategii |
| Potwierdzenie zapisu (double opt-in) | ≥ 70% zapisów | niżej = problem z mailem potwierdzającym albo spamem |
| Zgoda przy rejestracji w Akademii | ≥ 40% nowych kont | pole niezaznaczone domyślnie |
| Kliknięcia (CTR) w serii powitalnej | ≥ 8% | otwarcia zawyża Apple Mail, liczymy kliknięcia |
| Kliknięcia w newsletterze cyklicznym | ≥ 3% |  |
| Zakup Strategii na 1 stronie 39 zł | ≥ 3% nowych zapisów w 8 tygodni | kryterium wyjścia ze strategii |
| Zakup Pakietu 297 zł | ≥ 1% listy w 8 tygodni |  |
| Akademia Pro po ukończeniu działów 1–2 | ≥ 5% tych osób w 14 dni |  |
| Wypisy po wysyłce | < 0,5% | więcej = za często albo nietrafiony temat |
| Zgłoszenia spamu | < 0,1% | próg Gmaila 0,3% to czerwona linia |

## Pakiet startowy Kierunek Zysk (0 zł)

| Element | Co to jest | Stan |
|---|---|---|
| Kalkulator zysku | Arkusz (Excel / Arkusze Google): marża, koszty stałe, próg rentowności, ile sztuk lub godzin trzeba sprzedać. 10 minut na wypełnienie. | do zrobienia (regulamin wspomina „Kalkulator Zysku”, w sklepie go nie ma) |
| Strategia na 1 stronie: wypełniony przykład | 1 strona PDF z przykładową firmą. Pokazuje, jak wygląda gotowa strategia, i prowadzi do płatnego szablonu za 39 zł. | do zrobienia z płatnego szablonu (tylko podgląd, bez instrukcji) |
| Przegląd miesiąca w 15 minut | Checklista 7 pytań na koniec miesiąca (PDF, 1 strona): liczby, decyzje, co przestać robić. | do zrobienia |

- Pakiet dostaje się dopiero po potwierdzeniu zapisu (double opt-in), w pierwszym mailu serii.
- Pliki leżą w bibliotece mediów WordPressa pod trudnym do zgadnięcia adresem, nie w publicznym repozytorium.
- Akademia (działy 1–2) jest częścią pakietu: każdy mail serii ma lekcję na 5 minut.

## Wejścia na listę

| Wejście | Jak działa | Oznaczenie |
|---|---|---|
| Rejestracja w Akademii | Po założeniu konta aplikacja pokazuje ekran „Odbierz darmowy Pakiet startowy” z niezaznaczonym polem zgody. Zaznaczenie zapisuje zgodę (`gk_ak_zgoda`, data, źródło), a WordPress wysyła mail potwierdzający. Po kliknięciu kontakt trafia do Mailchimpa. | tag `akademia`, źródło `akademia_onboarding` |
| Strona /pakiet-startowy/ | Strona z formularzem: imię + e-mail + zgoda. Dla osób z reklam i LinkedIna, które nie chcą jeszcze zakładać konta. Po potwierdzeniu dostają pakiet i zaproszenie do Akademii. | tag `lead`, źródło `formularz_pakiet` |
| Profil w Akademii | Istniejący przełącznik zgody na maile w Profilu (dla osób, które ominęły ekran powitalny). | tag `akademia`, źródło `akademia_profil` |
| Zamówienie w sklepie | Opcjonalnie: niezaznaczone pole „Chcę dostawać newsletter i Pakiet startowy”. Do decyzji po pierwszych zakupach. | tag `klient`, źródło `zamowienie` |

## Kto co wysyła (Mailchimp Free)

Mailchimp Free: 250 kontaktów, 500 wysyłek w miesiącu, najwyżej 250 dziennie, bez wieloetapowych automatyzacji. Dlatego dzielimy pracę: automaty wysyła WordPress, a Mailchimp tylko wydania i kampanie.

| Narzędzie | Co robi | Jak |
|---|---|---|
| WordPress (wtyczka guliwera-newsletter) | zapis i zgoda, mail potwierdzający (double opt-in), seria powitalna W1–W7, maile wyzwalane Z1–Z7, wypis jednym kliknięciem, preferencje | Maile wychodzą przez SMTP nazwa.pl z kontakt@guliwera4.pl, w tym samym szablonie co maile sklepu. Nie zużywają limitu Mailchimpa. WordPress ma dane o nauce i zakupach od ręki, więc niczego nie trzeba synchronizować. |
| Mailchimp Free | lista osób z potwierdzoną zgodą, wydania co 2 tygodnie i kampanie (Black Week, Plan na 2027), statystyki | WordPress dodaje do Mailchimpa tylko osoby po potwierdzeniu, z datą i źródłem zgody. Wypis w Mailchimpie wraca do WordPressa (webhook) i odwrotnie. |

- Jedno wydanie do całej listy zużywa tyle wysyłek, ile jest kontaktów. Przy 250 osobach mieszczą się 2 wysyłki w miesiącu.
- Listopad (lista ok. 30–80 osób): wydanie #1 + 3 maile Black Week mieszczą się z zapasem.
- Styczeń: przy ponad 160 osobach 3 wysyłki (5.01, 8.01, 19.01) przekroczą 500. Wtedy łączymy 5.01 i 8.01 w jeden mail, a 8.01 wysyłamy tylko do osób bez zakupu.
- Próg decyzji: 200 kontaktów albo 450 wysyłek w miesiącu. Wtedy albo płatny Mailchimp Essentials, albo wydania też z WordPressa (wymaga sprawdzenia limitów SMTP w nazwa.pl).
- Do sprawdzenia w panelu: plan Free dodaje stopkę Mailchimpa i może ograniczać własne szablony HTML. Wtedy wydanie składamy z bloków edytora w kolorach marki.

## Dane w Mailchimpie (pola i tagi)

| Pole / tag | Zawartość | Aktualizacja |
|---|---|---|
| `ZRODLO` | akademia_onboarding / akademia_profil / formularz_pakiet / zamowienie | przy zapisie |
| `FNAME` | imię (jeśli podane) | przy zapisie |
| `AK_LEKCJE` | liczba ukończonych lekcji | codziennie z WordPressa |
| `AK_SERIA` | aktualna seria dni | codziennie |
| `AK_OSTATNIO` | data ostatniej lekcji | codziennie |
| `AK_DARMOWE` | tak, gdy ukończono działy 1–2 | codziennie (wyzwala serię Pro) |
| `AK_PRO_DO` | data końca Akademii Pro (puste = brak) | po zakupie Pro |
| `KUPON_KOD / KUPON_DO` | aktywny kupon z Akademii i data ważności | po odebraniu kuponu |
| `tagi zakupów` | `kupil_strategia`, `kupil_planer`, `kupil_pakiet`, `kupil_pro`, `klient` | po opłaconym zamówieniu |

## Seria powitalna (14 dni, automatyczna)

### P0 · Potwierdź zapis i odbierz pakiet

- **Kiedy:** od razu po zapisie · **Segment:** każdy zapis
- **Temat A:** Potwierdź zapis i odbierz pakiet
- **Preheader:** Jedno kliknięcie i Pakiet startowy jest Twój.
- **Cel:** Potwierdzić adres (double opt-in). Wysyła WordPress, link ważny 7 dni.
- **CTA:** Potwierdzam i odbieram pakiet → link potwierdzenia (WordPress)

Treść:

> Cześć! Kliknij przycisk, żeby potwierdzić zapis. Zaraz potem wyślę Ci Pakiet startowy Kierunek Zysk: kalkulator zysku, przykład strategii na 1 stronie i przegląd miesiąca w 15 minut.
> Nie zapisujesz się? Zignoruj tę wiadomość, nic się nie stanie.

### W1 · Twój Pakiet startowy jest tutaj

- **Kiedy:** dzień 0, zaraz po potwierdzeniu · **Segment:** każdy potwierdzony
- **Temat A:** Twój Pakiet startowy jest tutaj · **Temat B:** 3 narzędzia na start (0 zł)
- **Preheader:** Kalkulator zysku, przykład strategii na 1 stronie i przegląd miesiąca w 15 minut.
- **Cel:** Dostarczyć obiecany pakiet i zrobić pierwszy mały krok: kalkulator albo pierwszą lekcję.
- **CTA:** Pobierz Pakiet startowy → /pakiet-startowy/pliki/ (adres plików)
- **Uwagi:** Drugi przycisk (tekstowy): „Otwórz Akademię” → /akademia/. Dla tagu `lead` (bez konta): „Załóż darmowe konto w Akademii”.

Treść:

> Cześć *|FNAME|*! Tu Angelika z Guliwera 4. Dziękuję za zapis. Obiecany pakiet jest pod przyciskiem, a w środku są 3 pliki:
> 1) Kalkulator zysku: wpisz cenę, koszty i zobacz, ile musisz sprzedać, żeby wyjść na plus. 2) Strategia na 1 stronie: wypełniony przykład, żeby zobaczyć, jak wygląda gotowa strategia. 3) Przegląd miesiąca w 15 minut: 7 pytań na ostatni piątek miesiąca.
> Zacznij od kalkulatora. To 10 minut i zwykle pierwsze „aha” przy marży.
> Wolisz zacząć od nauki? W Akademii Guliwera czeka lekcja na 5 minut (Lilek już się rozgrzewa).
> Co dalej: przez 2 tygodnie dostaniesz ode mnie kilka krótkich maili z jedną lekcją i jednym narzędziem w każdym, potem newsletter co 2 tygodnie we wtorek rano. Wypisać się możesz w każdej chwili, jednym kliknięciem.

### W2 · Jedna kartka zamiast 40 slajdów

- **Kiedy:** dzień 1, 7:30 · **Segment:** każdy, bez tagu `kupil_strategia`
- **Temat A:** Jedna kartka zamiast 40 slajdów · **Temat B:** Strategia, która mieści się na jednej stronie
- **Preheader:** 5 pól, które wystarczą, żeby wiedzieć, na co mówisz „nie”.
- **Cel:** Pierwsza oferta: Strategia na 1 stronie za 39 zł (pierwszy zakup na drabinie).
- **CTA:** Zobacz szablon za 39 zł → /zamowienie/?add-to-cart=69 (przez stronę produktu)
- **Uwagi:** W Akademii: lekcja o MECE jako P.S. („jak dzielić problem, żeby nic nie zgubić”).

Treść:

> Większość strategii, które widziałam w firmach, ma 40 slajdów i nikt ich nie czyta po tygodniu. Dobra strategia mieści się na jednej kartce i odpowiada na 5 pytań:
> Dokąd idziemy (cel na rok, liczbą). Gdzie gramy (klienci, produkty, kanały). Czym wygrywamy (dlaczego ktoś wybierze nas). Czego NIE robimy (to najtrudniejsze pole). Po czym poznamy postęp (3–5 liczb).
> Zajrzyj do przykładu z Pakietu startowego i spróbuj wypełnić tylko jedno pole: „czego nie robimy”. To ćwiczenie zajmuje 10 minut i porządkuje pół kalendarza.
> Jeśli chcesz zrobić to porządnie dla swojej firmy, mam szablon Strategia na 1 stronie: edytowalny plik, instrukcja krok po kroku i przykłady dla 3 typów firm. Kosztuje 39 zł.

### W3 · 5 minut dziennie. Policzyłam, ile to daje

- **Kiedy:** dzień 3, 7:30 · **Segment:** każdy
- **Temat A:** 5 minut dziennie. Policzyłam, ile to daje · **Temat B:** Twoja seria w Akademii: *|AK_SERIA|* dni
- **Preheader:** Mały nawyk, duży efekt w skali roku. Plus jak działa rabat za naukę.
- **Cel:** Zbudować nawyk nauki w Akademii. Uczciwie wyjaśnić rabat i Akademię Pro.
- **CTA:** Otwórz dzisiejszą lekcję → /akademia/

Treść:

> 5 minut dziennie to ok. 30 godzin nauki w roku. Tyle co dwa porządne szkolenia, tylko bez wyjazdu i bez slajdów.
> [Blok warunkowy] AK_LEKCJE = 0: „Twoja pierwsza lekcja nadal czeka. Zajmie mniej czasu niż kawa.” AK_LEKCJE ≥ 1: „Masz za sobą *|AK_LEKCJE|* lekcji, a Twoja seria to *|AK_SERIA|* dni. Lilek jest dumny.”
> Jak działa rabat: za regularną naukę Akademia daje kupon na materiały Kierunek Zysk. Im więcej lekcji i aktywnych dni w ostatnich 4 tygodniach, tym wyższy rabat. W darmowych działach 1–2 to do ok. 7%, z Akademią Pro do 30%.
> Wskazówka: ustaw lekcję na stałą porę, np. zaraz po pierwszej kawie. Aplikację możesz dodać do ekranu telefonu jak zwykłą apkę.

### W4 · Ile musisz sprzedać, żeby wyjść na zero?

- **Kiedy:** dzień 6, 7:30 · **Segment:** każdy
- **Temat A:** Ile musisz sprzedać, żeby wyjść na zero? · **Temat B:** Próg rentowności w 3 liczbach
- **Preheader:** Jedno równanie, które warto znać na pamięć. Kalkulator zrobi resztę.
- **Cel:** Dać wartość (liczby) i przygotować grunt pod Pakiet.
- **CTA:** Otwórz kalkulator zysku → plik z pakietu
- **Uwagi:** P.S. W Pakiecie Kierunek Zysk jest Przewodnik, który rozkłada to na czynniki pierwsze. Więcej za 2 dni.

Treść:

> Próg rentowności = koszty stałe ÷ (cena − koszt zmienny na sztukę).
> Przykład: koszty stałe 6 000 zł miesięcznie, usługa za 300 zł, koszt zmienny 100 zł. 6 000 ÷ 200 = 30 usług miesięcznie, żeby wyjść na zero. Każda kolejna to już zysk.
> Najczęstszy błąd: liczenie od przychodu, a nie od marży. Przy marży 20% obniżka ceny o 10% oznacza, że trzeba sprzedać dwa razy więcej, żeby zarobić tyle samo.
> Wpisz swoje liczby do kalkulatora z Pakietu startowego. Jeśli wynik Cię zaskoczy, odpisz: chętnie zobaczę, co wyszło (anonimowo, bez nazw).

### W5 · Co jest w Pakiecie Kierunek Zysk

- **Kiedy:** dzień 8, 7:30 · **Segment:** bez tagu `kupil_pakiet`
- **Temat A:** Co jest w Pakiecie Kierunek Zysk · **Temat B:** Cały Kierunek Zysk w jednym pakiecie
- **Preheader:** Przewodnik, Strategia na 1 stronie, Planer 90 dni, Skrypty, Audyt i Dziennik decyzji.
- **Cel:** Główna oferta: Pakiet 297 zł.
- **CTA:** Zobacz Pakiet → strona produktu 67
- **Uwagi:** Kupon Akademii nie łączy się z promocją Black Week (24–30.11).

Treść:

> Pakiet łączy wszystkie materiały Kierunek Zysk, które prowadzą od liczb do planu: Przewodnik (147 zł), Strategia na 1 stronie (39 zł), Planer 90 dni (59 zł), Skrypty i szablony sprzedażowe (79 zł), Audyt (59 zł), Dziennik decyzji (49 zł).
> Osobno to 432 zł, w Pakiecie 297 zł, czyli 135 zł mniej.
> Dla kogo: właściciele małych firm, osoby prowadzące jednoosobową działalność, młodzi managerowie, którzy chcą mieć plan na papierze, a nie w głowie.
> Dla kogo nie: jeśli w firmie działa już dział strategii i roczny proces planowania. Wtyczka do Excela tego nie zastąpi, Pakiet też nie.
> [Blok warunkowy] Jeśli KUPON_DO w przyszłości: „Masz aktywny kupon z Akademii (*|KUPON_KOD|*) do *|KUPON_DO|*. Działa także na Pakiet.”

### W6 · Jedno pytanie (odpisz jednym zdaniem)

- **Kiedy:** dzień 10, 7:30 · **Segment:** każdy
- **Temat A:** Jedno pytanie (odpisz jednym zdaniem) · **Temat B:** Z czym teraz walczysz w firmie?
- **Preheader:** Czytam każdą odpowiedź. Z nich powstają kolejne lekcje.
- **Cel:** Rozmowa zamiast sprzedaży: odpowiedzi poprawiają dostarczalność i dają tematy na lekcje i produkty.
- **CTA:** Odpowiedz na maila → brak przycisku: prośba o odpowiedź + 5 linków-tagów

Treść:

> Mam do Ciebie jedno pytanie: z czym teraz najbardziej walczysz w swojej firmie albo zespole?
> Odpisz na tego maila jednym zdaniem. Czytam każdą odpowiedź. Najczęstsze tematy zamieniam w lekcje w Akademii i w wydania newslettera.
> Jeśli wolisz kliknąć: ceny i marża / plan na rok / sprzedaż / zespół / czas i priorytety (każdy link dodaje tag z zainteresowaniem).

### W7 · Co dalej (i jak często będę pisać)

- **Kiedy:** dzień 14, 7:30 · **Segment:** każdy
- **Temat A:** Co dalej (i jak często będę pisać) · **Temat B:** Twoje 2 tygodnie z Guliwera 4
- **Preheader:** Newsletter co 2 tygodnie we wtorek. Możesz zmienić częstotliwość jednym kliknięciem.
- **Cel:** Przejście do newslettera cyklicznego, ustawienie preferencji, kolejny krok w Akademii.
- **CTA:** Ustaw preferencje → strona preferencji na guliwera4.pl
- **Uwagi:** Na koniec serii: tag `seria_powitalna_ok`, kontakt wpada do newslettera cyklicznego.

Treść:

> Dziękuję za te 2 tygodnie. Od teraz piszę co 2 tygodnie, we wtorek o 7:30: jeden temat ze strategii, jedno narzędzie, jedna lekcja z Akademii i jedna oferta, zawsze oznaczona.
> [Blok warunkowy] AK_DARMOWE = tak: „Masz za sobą darmowe działy Akademii. Kolejne działy (finanse, metryki, zespół) są w Akademii Pro.” Inaczej: „Do końca darmowych działów zostało Ci *|6 − AK_LEKCJE|* lekcji.”
> Chcesz rzadziej? Ustaw „raz w miesiącu” albo „tylko Akademia” w preferencjach.


## Maile wyzwalane zachowaniem (automatyczne)

### Z1 · Ukończone 2 działy Akademii. Co dalej?

- **Kiedy:** gdy AK_DARMOWE zmieni się na „tak” · **Segment:** ze zgodą, bez Pro
- **Temat A:** Ukończone 2 działy Akademii. Co dalej? · **Temat B:** Lilek ma dla Ciebie certyfikat (prawie)
- **Preheader:** Kolejne działy: finanse, metryki, zespół. Akademia Pro na 12 miesięcy.
- **Cel:** Sprzedaż Akademii Pro 249 zł w najlepszym momencie: po ukończeniu darmowej części.
- **CTA:** Odblokuj Akademię Pro → przycisk Pro w aplikacji / produkt akademia-pro
- **Uwagi:** Przypomnienie po 3 dniach tylko dla osób bez tagu `kupil_pro`, potem koniec serii (bez naciskania).

Treść:

> Gratulacje: darmowe działy Akademii masz za sobą. To *|AK_LEKCJE|* lekcji i seria *|AK_SERIA|* dni.
> Akademia Pro odblokowuje wszystkie kolejne działy i nowe lekcje dodawane w ciągu roku, zdejmuje limit serc i daje certyfikat ukończenia. Lekcje z Pro liczą się też do rabatu na materiały (do 30% zamiast ok. 7%).
> 249 zł za 12 miesięcy, czyli ok. 68 gr dziennie za 5 minut nauki.

### Z2 · 7 dni z rzędu. Szacun!

- **Kiedy:** seria 7 i 30 dni · **Segment:** ze zgodą
- **Temat A:** 7 dni z rzędu. Szacun!
- **Preheader:** Krótka gratulacja, bez oferty.
- **Cel:** Wzmocnić nawyk. Mail bez sprzedaży.
- **CTA:** Otwórz lekcję → /akademia/

Treść:

> 7 dni nauki z rzędu. Lilek zrobił z tej okazji taniec, którego nie pokażemy. Jutro dzień 8?

### Z3 · Wracasz na 5 minut?

- **Kiedy:** 7 dni bez lekcji (po co najmniej 1 lekcji) · **Segment:** ze zgodą
- **Temat A:** Wracasz na 5 minut? · **Temat B:** Lilek pyta, co słychać
- **Preheader:** Twoja seria czeka. Jedna lekcja i wracasz do gry.
- **Cel:** Reaktywacja nauki.
- **CTA:** Wracam do lekcji → /akademia/

Treść:

> Tydzień przerwy to nic złego. Najtrudniejsza lekcja to ta pierwsza po przerwie, więc wybrałam krótką: *|następna lekcja|*.
> Uwaga: rabat za naukę liczy aktywne dni z ostatnich 4 tygodni, więc po przerwie trochę spada. Wraca, gdy wracasz Ty.

### Z4 · Zostawić Ci miejsce w Akademii?

- **Kiedy:** 21 dni bez lekcji · **Segment:** ze zgodą
- **Temat A:** Zostawić Ci miejsce w Akademii?
- **Preheader:** Bez presji. Możesz też zmienić, jak często piszę.
- **Cel:** Druga, ostatnia próba reaktywacji + zmiana preferencji.
- **CTA:** Wracam do lekcji → /akademia/
- **Uwagi:** Dwa linki tekstowe do preferencji.

Treść:

> Nie było Cię w Akademii od 3 tygodni. Rozumiem, kalendarz bywa bezlitosny.
> Masz 3 opcje: wracasz na lekcję, zostajesz tylko przy newsletterze co 2 tygodnie albo wybierasz „raz w miesiącu”.

### Z5 · Twój kupon −*|PROCENT|*% wygasa pojutrze

- **Kiedy:** kupon z Akademii wygasa za 2 dni · **Segment:** ze zgodą, kupon niewykorzystany
- **Temat A:** Twój kupon −*|PROCENT|*% wygasa pojutrze
- **Preheader:** Kod *|KUPON_KOD|* działa do *|KUPON_DO|*.
- **Cel:** Przypomnienie (marketing, dlatego tylko za zgodą).
- **CTA:** Użyj kuponu → /koszyk/?gk_kupon=*|KUPON_KOD|*

Treść:

> Twój kupon z Akademii (*|KUPON_KOD|*) działa jeszcze 2 dni, do *|KUPON_DO|*. Przycisk od razu dodaje go do koszyka.

### Z6 · Jak wypełnić Strategię w 45 minut

- **Kiedy:** po zakupie Strategii na 1 stronie: dzień 1, 7, 14 · **Segment:** tag `kupil_strategia`, ze zgodą
- **Temat A:** Jak wypełnić Strategię w 45 minut · **Temat B:** Następny krok: Planer 90 dni
- **Preheader:** D1: kolejność pól i najczęstsze błędy. D7: Planer 90 dni (59 zł) zamienia strategię w plan kwartału. D14: prośba o opinię.
- **Cel:** Drugi zakup na drabinie i opinia.
- **CTA:** D7: Zobacz Planer 90 dni → produkt 70

Treść:

> D1: „Zacznij od pola »czego nie robimy«, potem cel, na końcu liczby.” D7: „Strategia mówi dokąd, Planer 90 dni mówi, co robisz w tym kwartale.” D14: „Jak Ci poszło? Jedno zdanie opinii bardzo mi pomoże.”

### Z7 · Od czego zacząć w Pakiecie

- **Kiedy:** po zakupie Pakietu: dzień 2, 10, 21 · **Segment:** tag `kupil_pakiet`, ze zgodą
- **Temat A:** Od czego zacząć w Pakiecie · **Temat B:** Plan na 4 tygodnie z Pakietem
- **Preheader:** D2: kolejność materiałów na 4 tygodnie. D10: opinia. D21: lista oczekujących kursu wideo.
- **Cel:** Wykorzystanie produktu (mniej zwrotów), opinia, kolejny szczebel: kurs.
- **CTA:** D21: Zapisz się na listę oczekujących → strona kursu

Treść:

> D2: „Tydzień 1: Przewodnik + Audyt. Tydzień 2: Strategia na 1 stronie. Tydzień 3: Planer 90 dni. Tydzień 4: Skrypty i Dziennik decyzji.” D21: „Nagrywam kurs wideo Strategia w 6 tygodni. Osoby z listy oczekujących dostaną cenę przedsprzedaży.”

## Newsletter cykliczny i kampanie (ręczne, każda wysyłka po akceptacji)

Rytm: **co 2 tygodnie, wtorek 7:30**. Managerowie i właściciele firm czytają pocztę przed pracą; wtorek omija poniedziałkowy zalew maili. Kampanie promocyjne: dodatkowo piątek 12:00 (przerwa) i ostatni dzień promocji 18:00. Po 6 tygodniach test A/B: wtorek 7:30 vs czwartek 12:00.

| Data | Typ | Temat | Zawartość | Segment |
|---|---|---|---|---|
| 17.11 (wt) 7:30 | Wydanie #1 | Strategia na 1 stronie: 5 pól, które wystarczą | narzędzie: kalkulator zysku · lekcja: MECE · oferta: Strategia 39 zł | cała lista ze zgodą |
| 24.11 (wt) 7:30 | Black Week 1/3 | Pakiet Kierunek Zysk 30% taniej do 30.11 | 207,90 zł zamiast 297 zł; „Najniższa cena od początku sprzedaży (4.11.2026): 297 zł” | bez tagu `kupil_pakiet` |
| 27.11 (pt) 12:00 | Black Week 2/3 | Black Friday: 3 dni do końca −30% | krótki, dla tych, którzy nie kliknęli 24.11 | bez zakupu Pakietu, bez kliknięcia w BW 1/3 |
| 30.11 (pn) 18:00 | Black Week 3/3 | Ostatni dzień −30% na Pakiet | „do północy”, jedno zdanie + przycisk | bez zakupu Pakietu |
| 8.12 (wt) 7:30 | Wydanie #2 | Ceny i marża: kiedy podnieść cenę | narzędzie: kalkulator · lekcja: macierz BCG · oferta: Przewodnik 147 zł | cała lista |
| 22.12 (wt) 7:30 | Wydanie #3 (świąteczne, krótkie) | Przegląd roku w 15 minut | narzędzie: checklista z Pakietu startowego · bez oferty · życzenia | cała lista |
| 5.01 (wt) 7:30 | Kampania „Plan na 2027” 1/2 | Twój plan na 2027 na jednej kartce | Strategia 39 zł + Planer 90 dni 59 zł · lekcja: OKR | cała lista |
| 8.01 (pt) 12:00 | Kampania „Plan na 2027” 2/2 | Pierwszy kwartał 2027: 90 dni, 3 priorytety | Planer 90 dni · tylko osoby bez zakupu | bez `kupil_planer` |
| 19.01 (wt) 7:30 | Wydanie #4 | Piramida Minto: jak pisać maile, które ktoś przeczyta | narzędzie: szablon maila do zarządu · lekcja: Minto · oferta: Akademia Pro | cała lista |
| 2.02 (wt) 7:30 | Wydanie #5 | Kurs wideo Strategia w 6 tygodni: zapisy na listę oczekujących | lekcja: 7S · oferta: lista oczekujących kursu z ceną przedsprzedaży | cała lista |

### Szablon wydania

| Część | Zasada |
|---|---|
| Temat (≤ 45 znaków) + preheader (≤ 90) | konkret albo pytanie; bez CAPS LOCK i wykrzykników w temacie |
| 1. Temat ze strategii (150–200 słów) | jedna myśl, własnymi słowami Angeliki, przykład z małej firmy |
| 2. Narzędzie (50–80 słów) | plik z Pakietu startowego, szablon albo wzór; zawsze do użycia od razu |
| 3. Lekcja z Akademii (40–60 słów) | zapowiedź jednej lekcji + przycisk „Otwórz lekcję” (link z UTM) |
| 4. Oferta (40–60 słów, oznaczona „Oferta”) | jeden produkt z drabiny; przy promocji najniższa cena z 30 dni |
| P.S. | pytanie do odpowiedzi albo zapowiedź kolejnego wydania |
| Stopka | dane firmy, powód otrzymania („zapis z Akademii / ze strony”), preferencje, wypis jednym kliknięciem |

## Prawo i dostarczalność

- Zgoda na maile marketingowe osobna od regulaminu i rejestracji, pole niezaznaczone domyślnie (art. 398 Prawa komunikacji elektronicznej, RODO).
- Double opt-in w Mailchimpie: zapis liczy się dopiero po kliknięciu w mail potwierdzający. Mailchimp zapisuje datę, IP i źródło zgody.
- W każdym mailu: wypis jednym kliknięciem (nagłówek List-Unsubscribe; wymóg Gmaila i Yahoo), link do preferencji, dane firmy, powód otrzymania.
- Promocje: najniższa cena z 30 dni przed obniżką (dyrektywa Omnibus), bez fałszywej presji czasu; „do 30.11” musi być prawdą.
- Bez obietnic wyniku finansowego („zarobisz”, „podwoisz zysk”); rabat za naukę opisany uczciwie (zależy od regularności).
- Dostarczalność: uwierzytelniona domena guliwera4.pl w Mailchimpie (DKIM i SPF w DNS nazwa.pl), DMARC co najmniej p=none, nadawca kontakt@guliwera4.pl.
- Higiena listy: kontakty bez otwarć i kliknięć przez 90 dni dostają mail „zostajesz?”, a bez reakcji trafiają do archiwum. Na planie Free to też oszczędność limitu.
- Polityka prywatności: dopisać Mailchimp (Intuit) jako odbiorcę danych i transfer do USA (Data Privacy Framework).

## Wdrożenie

| Kto | Co | Termin | Stan |
|---|---|---|---|
| Ty | Mailchimp Free: klucz API (Konto → Extras → API keys) wpisany w panelu WordPressa, nigdy w czacie ani repo. Uwierzytelnienie domeny: rekordy DNS w nazwa.pl według instrukcji, którą przygotuję. | do 15.11 | 👤 |
| ja | Mailchimp: lista „Guliwera 4”, pola i tagi z tabeli, stopka z danymi firmy, webhook wypisu do WordPressa. | do 15.11 | 🔧 |
| ja | Pakiet startowy: kalkulator zysku (XLSX + kopia w Arkuszach Google), przykład strategii na 1 stronie, checklista przeglądu miesiąca. Pliki w bibliotece mediów. | do 14.11 | 🔧 |
| ja | Wtyczka `guliwera-newsletter`: zapis i zgoda (Akademia + formularz), double opt-in, seria W1–W7 i maile Z1–Z7 wysyłane przez WordPress według harmonogramu, wypis i preferencje, przekazanie potwierdzonych osób do Mailchimpa, zdarzenie `generate_lead` w GTM. | do 16.11 | 🔧 |
| ja | Strona /pakiet-startowy/ z formularzem i strona „Sprawdź skrzynkę” (z ofertą 39 zł po potwierdzeniu). | do 16.11 | 🔧 |
| ja | Podgląd wszystkich maili serii i Z1–Z7 (zrzuty komputer + telefon) do akceptacji; szablon wydania #1 jako szkic w Mailchimpie. Nic nie startuje bez Twojej akceptacji. | do 16.11 | 🔧 |
| Ty | Akceptacja treści i włączenie serii powitalnej. Test: zapis na własny adres, przejście całej ścieżki. | 16.11 | 👤 |
| razem | Start reklam K1 Akademia (czat o reklamach) kierujących do Akademii i /pakiet-startowy/. | 17.11 | 👤 |
| ja | Co 2 tygodnie: szkic wydania w czwartek przed wysyłką, Ty dopisujesz 1 akapit własnymi słowami i akceptujesz (ok. 45 min). | od 12.11 | 🔁 |
| ja | Raport po 2 i 6 tygodniach: KPI vs cel, testy A/B tematów, co zmienić. | 1.12 i 29.12 | 📊 |

## Decyzje Angeliki

| Decyzja | Rekomendacja | Dlaczego |
|---|---|---|
| Zawartość Pakietu startowego | ✅ Kalkulator zysku + przykład strategii na 1 stronie + przegląd miesiąca (decyzja 8.11) | Najmocniej prowadzi do 39 zł i Pakietu, a kalkulator jest w strategii jako magnes. |
| Plan Mailchimpa | ✅ Free (decyzja 8.11) + automaty z WordPressa | Free nie ma wieloetapowych automatyzacji i ma 500 wysyłek w miesiącu, więc serie wysyła WordPress. Próg zmiany planu: 200 kontaktów albo 450 wysyłek w miesiącu. |
| Głos maili marketingowych | ✅ Pierwsza osoba: „Angelika z Guliwera 4” (decyzja 8.11) | Newsletter sprzedaje markę osobistą. Maile sklepu zostają w formie „my”. |
| Rabat Akademii 30% | Zostawić w komunikacji, ale mocniej sprzedawać Pro | Strategia krytykuje rabat 30% za koszt marży; do 30% działa teraz tylko z Pro, więc wspiera sprzedaż Pro. |
| Black Week | Pakiet −30% 24–30.11, 3 maile do listy | Zgodne z planem reklam (K2); kupony Akademii się nie łączą. |
