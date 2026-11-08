# -*- coding: utf-8 -*-
"""
Plan newslettera dla osób z Akademii Guliwera: jedno źródło treści,
z którego powstają marketing/NEWSLETTER-AKADEMIA.md (repo) i strona z planem (Artifact).
Użycie: python3 marketing/newsletter_akademia.py [KATALOG_HTML]
"""
import html, json, os, sys

CENY = {"Strategia na 1 stronie": "39 zł", "Planer 90 dni": "59 zł", "Pakiet Kierunek Zysk": "297 zł", "Akademia Pro": "249 zł / 12 mies."}

CEL = {
    "teza": "Darmowy Pakiet startowy + newsletter zamieniają osobę, która zajrzała do Akademii, w kontakt na liście. Lista sprzedaje kolejne szczeble: 39 zł → Pakiet 297 zł → Akademia Pro 249 zł → kurs wideo.",
    "kpi": [
        ("Nowe zapisy na listę (z potwierdzeniem)", "≥ 110 / mies. od grudnia; 250 osób do 31.12.2026", "KPI nr 1 ze strategii"),
        ("Potwierdzenie zapisu (double opt-in)", "≥ 70% zapisów", "niżej = problem z mailem potwierdzającym albo spamem"),
        ("Zgoda przy rejestracji w Akademii", "≥ 40% nowych kont", "pole niezaznaczone domyślnie"),
        ("Kliknięcia (CTR) w serii powitalnej", "≥ 8%", "otwarcia zawyża Apple Mail, liczymy kliknięcia"),
        ("Kliknięcia w newsletterze cyklicznym", "≥ 3%", ""),
        ("Zakup Strategii na 1 stronie 39 zł", "≥ 3% nowych zapisów w 8 tygodni", "kryterium wyjścia ze strategii"),
        ("Zakup Pakietu 297 zł", "≥ 1% listy w 8 tygodni", ""),
        ("Akademia Pro po ukończeniu działów 1–2", "≥ 5% tych osób w 14 dni", ""),
        ("Wypisy po wysyłce", "< 0,5%", "więcej = za często albo nietrafiony temat"),
        ("Zgłoszenia spamu", "< 0,1%", "próg Gmaila 0,3% to czerwona linia"),
    ],
}

PAKIET = {
    "nazwa": "Pakiet startowy Kierunek Zysk (0 zł)",
    "elementy": [
        ("Kalkulator zysku", "Arkusz (Excel / Arkusze Google): marża, koszty stałe, próg rentowności, ile sztuk lub godzin trzeba sprzedać. 10 minut na wypełnienie.", "do zrobienia (regulamin wspomina „Kalkulator Zysku”, w sklepie go nie ma)"),
        ("Strategia na 1 stronie: wypełniony przykład", "1 strona PDF z przykładową firmą. Pokazuje, jak wygląda gotowa strategia, i prowadzi do płatnego szablonu za 39 zł.", "do zrobienia z płatnego szablonu (tylko podgląd, bez instrukcji)"),
        ("Przegląd miesiąca w 15 minut", "Checklista 7 pytań na koniec miesiąca (PDF, 1 strona): liczby, decyzje, co przestać robić.", "do zrobienia"),
    ],
    "zasady": [
        "Pakiet dostaje się dopiero po potwierdzeniu zapisu (double opt-in), w pierwszym mailu serii.",
        "Pliki leżą w bibliotece mediów WordPressa pod trudnym do zgadnięcia adresem, nie w publicznym repozytorium.",
        "Akademia (działy 1–2) jest częścią pakietu: każdy mail serii ma lekcję na 5 minut.",
    ],
}

WEJSCIA = [
    ("Rejestracja w Akademii", "Po założeniu konta aplikacja pokazuje ekran „Odbierz darmowy Pakiet startowy” z niezaznaczonym polem zgody. Zaznaczenie zapisuje zgodę (`gk_ak_zgoda`, data, źródło), a WordPress wysyła mail potwierdzający. Po kliknięciu kontakt trafia do Mailchimpa.", "tag `akademia`, źródło `akademia_onboarding`"),
    ("Strona /pakiet-startowy/", "Strona z formularzem: imię + e-mail + zgoda. Dla osób z reklam i LinkedIna, które nie chcą jeszcze zakładać konta. Po potwierdzeniu dostają pakiet i zaproszenie do Akademii.", "tag `lead`, źródło `formularz_pakiet`"),
    ("Profil w Akademii", "Istniejący przełącznik zgody na maile w Profilu (dla osób, które ominęły ekran powitalny).", "tag `akademia`, źródło `akademia_profil`"),
    ("Zamówienie w sklepie", "Opcjonalnie: niezaznaczone pole „Chcę dostawać newsletter i Pakiet startowy”. Do decyzji po pierwszych zakupach.", "tag `klient`, źródło `zamowienie`"),
]

ARCHITEKTURA = {
    "zalozenie": "Mailchimp Free: 250 kontaktów, 500 wysyłek w miesiącu, najwyżej 250 dziennie, bez wieloetapowych automatyzacji. Dlatego dzielimy pracę: automaty wysyła WordPress, a Mailchimp tylko wydania i kampanie.",
    "podzial": [
        ("WordPress (wtyczka guliwera-newsletter)", "zapis i zgoda, mail potwierdzający (double opt-in), seria powitalna W1–W7, maile wyzwalane Z1–Z7, wypis jednym kliknięciem, preferencje", "Maile wychodzą przez SMTP nazwa.pl z kontakt@guliwera4.pl, w tym samym szablonie co maile sklepu. Nie zużywają limitu Mailchimpa. WordPress ma dane o nauce i zakupach od ręki, więc niczego nie trzeba synchronizować."),
        ("Mailchimp Free", "lista osób z potwierdzoną zgodą, wydania co 2 tygodnie i kampanie (Black Week, Plan na 2027), statystyki", "WordPress dodaje do Mailchimpa tylko osoby po potwierdzeniu, z datą i źródłem zgody. Wypis w Mailchimpie wraca do WordPressa (webhook) i odwrotnie."),
    ],
    "progi": [
        "Jedno wydanie do całej listy zużywa tyle wysyłek, ile jest kontaktów. Przy 250 osobach mieszczą się 2 wysyłki w miesiącu.",
        "Listopad (lista ok. 30–80 osób): wydanie #1 + 3 maile Black Week mieszczą się z zapasem.",
        "Styczeń: przy ponad 160 osobach 3 wysyłki (5.01, 8.01, 19.01) przekroczą 500. Wtedy łączymy 5.01 i 8.01 w jeden mail, a 8.01 wysyłamy tylko do osób bez zakupu.",
        "Próg decyzji: 200 kontaktów albo 450 wysyłek w miesiącu. Wtedy albo płatny Mailchimp Essentials, albo wydania też z WordPressa (wymaga sprawdzenia limitów SMTP w nazwa.pl).",
        "Do sprawdzenia w panelu: plan Free dodaje stopkę Mailchimpa i może ograniczać własne szablony HTML. Wtedy wydanie składamy z bloków edytora w kolorach marki.",
    ],
}

DANE = [  # pola w Mailchimpie: tylko do segmentacji wydań; automaty czytają dane wprost z WordPressa
    ("ZRODLO", "akademia_onboarding / akademia_profil / formularz_pakiet / zamowienie", "przy zapisie"),
    ("FNAME", "imię (jeśli podane)", "przy zapisie"),
    ("AK_LEKCJE", "liczba ukończonych lekcji", "codziennie z WordPressa"),
    ("AK_SERIA", "aktualna seria dni", "codziennie"),
    ("AK_OSTATNIO", "data ostatniej lekcji", "codziennie"),
    ("AK_DARMOWE", "tak, gdy ukończono działy 1–2", "codziennie (wyzwala serię Pro)"),
    ("AK_PRO_DO", "data końca Akademii Pro (puste = brak)", "po zakupie Pro"),
    ("KUPON_KOD / KUPON_DO", "aktywny kupon z Akademii i data ważności", "po odebraniu kuponu"),
    ("tagi zakupów", "`kupil_strategia`, `kupil_planer`, `kupil_pakiet`, `kupil_pro`, `klient`", "po opłaconym zamówieniu"),
]

def m(kod, kiedy, segment, temat_a, temat_b, preheader, cel, tresc, cta, warunek=""):
    return dict(kod=kod, kiedy=kiedy, segment=segment, temat_a=temat_a, temat_b=temat_b, preheader=preheader, cel=cel, tresc=tresc, cta=cta, warunek=warunek)

SERIA = [
    m("P0", "od razu po zapisie", "każdy zapis", "Potwierdź zapis i odbierz pakiet", "", "Jedno kliknięcie i Pakiet startowy jest Twój.",
      "Potwierdzić adres (double opt-in). Wysyła WordPress, link ważny 7 dni.",
      ["Cześć! Kliknij przycisk, żeby potwierdzić zapis. Zaraz potem wyślę Ci Pakiet startowy Kierunek Zysk: kalkulator zysku, przykład strategii na 1 stronie i przegląd miesiąca w 15 minut.",
       "Nie zapisujesz się? Zignoruj tę wiadomość, nic się nie stanie."],
      ("Potwierdzam i odbieram pakiet", "link potwierdzenia (WordPress)")),
    m("W1", "dzień 0, zaraz po potwierdzeniu", "każdy potwierdzony", "Twój Pakiet startowy jest tutaj", "3 narzędzia na start (0 zł)",
      "Kalkulator zysku, przykład strategii na 1 stronie i przegląd miesiąca w 15 minut.",
      "Dostarczyć obiecany pakiet i zrobić pierwszy mały krok: kalkulator albo pierwszą lekcję.",
      ["Cześć *|FNAME|*! Tu Angelika z Guliwera 4. Dziękuję za zapis. Obiecany pakiet jest pod przyciskiem, a w środku są 3 pliki:",
       "1) Kalkulator zysku: wpisz cenę, koszty i zobacz, ile musisz sprzedać, żeby wyjść na plus. 2) Strategia na 1 stronie: wypełniony przykład, żeby zobaczyć, jak wygląda gotowa strategia. 3) Przegląd miesiąca w 15 minut: 7 pytań na ostatni piątek miesiąca.",
       "Zacznij od kalkulatora. To 10 minut i zwykle pierwsze „aha” przy marży.",
       "Wolisz zacząć od nauki? W Akademii Guliwera czeka lekcja na 5 minut (Lilek już się rozgrzewa).",
       "Co dalej: przez 2 tygodnie dostaniesz ode mnie kilka krótkich maili z jedną lekcją i jednym narzędziem w każdym, potem newsletter co 2 tygodnie we wtorek rano. Wypisać się możesz w każdej chwili, jednym kliknięciem."],
      ("Pobierz Pakiet startowy", "/pakiet-startowy/pliki/ (adres plików)"),
      "Drugi przycisk (tekstowy): „Otwórz Akademię” → /akademia/. Dla tagu `lead` (bez konta): „Załóż darmowe konto w Akademii”."),
    m("W2", "dzień 1, 7:30", "każdy, bez tagu `kupil_strategia`", "Jedna kartka zamiast 40 slajdów", "Strategia, która mieści się na jednej stronie",
      "5 pól, które wystarczą, żeby wiedzieć, na co mówisz „nie”.",
      "Pierwsza oferta: Strategia na 1 stronie za 39 zł (pierwszy zakup na drabinie).",
      ["Większość strategii, które widziałam w firmach, ma 40 slajdów i nikt ich nie czyta po tygodniu. Dobra strategia mieści się na jednej kartce i odpowiada na 5 pytań:",
       "Dokąd idziemy (cel na rok, liczbą). Gdzie gramy (klienci, produkty, kanały). Czym wygrywamy (dlaczego ktoś wybierze nas). Czego NIE robimy (to najtrudniejsze pole). Po czym poznamy postęp (3–5 liczb).",
       "Zajrzyj do przykładu z Pakietu startowego i spróbuj wypełnić tylko jedno pole: „czego nie robimy”. To ćwiczenie zajmuje 10 minut i porządkuje pół kalendarza.",
       "Jeśli chcesz zrobić to porządnie dla swojej firmy, mam szablon Strategia na 1 stronie: edytowalny plik, instrukcja krok po kroku i przykłady dla 3 typów firm. Kosztuje 39 zł."],
      ("Zobacz szablon za 39 zł", "/zamowienie/?add-to-cart=69 (przez stronę produktu)"),
      "W Akademii: lekcja o MECE jako P.S. („jak dzielić problem, żeby nic nie zgubić”)."),
    m("W3", "dzień 3, 7:30", "każdy", "5 minut dziennie. Policzyłam, ile to daje", "Twoja seria w Akademii: *|AK_SERIA|* dni",
      "Mały nawyk, duży efekt w skali roku. Plus jak działa rabat za naukę.",
      "Zbudować nawyk nauki w Akademii. Uczciwie wyjaśnić rabat i Akademię Pro.",
      ["5 minut dziennie to ok. 30 godzin nauki w roku. Tyle co dwa porządne szkolenia, tylko bez wyjazdu i bez slajdów.",
       "[Blok warunkowy] AK_LEKCJE = 0: „Twoja pierwsza lekcja nadal czeka. Zajmie mniej czasu niż kawa.” AK_LEKCJE ≥ 1: „Masz za sobą *|AK_LEKCJE|* lekcji, a Twoja seria to *|AK_SERIA|* dni. Lilek jest dumny.”",
       "Jak działa rabat: za regularną naukę Akademia daje kupon na materiały Kierunek Zysk. Im więcej lekcji i aktywnych dni w ostatnich 4 tygodniach, tym wyższy rabat. W darmowych działach 1–2 to do ok. 7%, z Akademią Pro do 30%.",
       "Wskazówka: ustaw lekcję na stałą porę, np. zaraz po pierwszej kawie. Aplikację możesz dodać do ekranu telefonu jak zwykłą apkę."],
      ("Otwórz dzisiejszą lekcję", "/akademia/")),
    m("W4", "dzień 6, 7:30", "każdy", "Ile musisz sprzedać, żeby wyjść na zero?", "Próg rentowności w 3 liczbach",
      "Jedno równanie, które warto znać na pamięć. Kalkulator zrobi resztę.",
      "Dać wartość (liczby) i przygotować grunt pod Pakiet.",
      ["Próg rentowności = koszty stałe ÷ (cena − koszt zmienny na sztukę).",
       "Przykład: koszty stałe 6 000 zł miesięcznie, usługa za 300 zł, koszt zmienny 100 zł. 6 000 ÷ 200 = 30 usług miesięcznie, żeby wyjść na zero. Każda kolejna to już zysk.",
       "Najczęstszy błąd: liczenie od przychodu, a nie od marży. Przy marży 20% obniżka ceny o 10% oznacza, że trzeba sprzedać dwa razy więcej, żeby zarobić tyle samo.",
       "Wpisz swoje liczby do kalkulatora z Pakietu startowego. Jeśli wynik Cię zaskoczy, odpisz: chętnie zobaczę, co wyszło (anonimowo, bez nazw)."],
      ("Otwórz kalkulator zysku", "plik z pakietu"),
      "P.S. W Pakiecie Kierunek Zysk jest Przewodnik, który rozkłada to na czynniki pierwsze. Więcej za 2 dni."),
    m("W5", "dzień 8, 7:30", "bez tagu `kupil_pakiet`", "Co jest w Pakiecie Kierunek Zysk", "Cały Kierunek Zysk w jednym pakiecie",
      "Przewodnik, Strategia na 1 stronie, Planer 90 dni, Skrypty, Audyt i Dziennik decyzji.",
      "Główna oferta: Pakiet 297 zł.",
      ["Pakiet łączy wszystkie materiały Kierunek Zysk, które prowadzą od liczb do planu: Przewodnik (147 zł), Strategia na 1 stronie (39 zł), Planer 90 dni (59 zł), Skrypty i szablony sprzedażowe (79 zł), Audyt (59 zł), Dziennik decyzji (49 zł).",
       "Osobno to 432 zł, w Pakiecie 297 zł, czyli 135 zł mniej.",
       "Dla kogo: właściciele małych firm, osoby prowadzące jednoosobową działalność, młodzi managerowie, którzy chcą mieć plan na papierze, a nie w głowie.",
       "Dla kogo nie: jeśli w firmie działa już dział strategii i roczny proces planowania. Wtyczka do Excela tego nie zastąpi, Pakiet też nie.",
       "[Blok warunkowy] Jeśli KUPON_DO w przyszłości: „Masz aktywny kupon z Akademii (*|KUPON_KOD|*) do *|KUPON_DO|*. Działa także na Pakiet.”"],
      ("Zobacz Pakiet", "strona produktu 67"),
      "Kupon Akademii nie łączy się z promocją Black Week (24–30.11)."),
    m("W6", "dzień 10, 7:30", "każdy", "Jedno pytanie (odpisz jednym zdaniem)", "Z czym teraz walczysz w firmie?",
      "Czytam każdą odpowiedź. Z nich powstają kolejne lekcje.",
      "Rozmowa zamiast sprzedaży: odpowiedzi poprawiają dostarczalność i dają tematy na lekcje i produkty.",
      ["Mam do Ciebie jedno pytanie: z czym teraz najbardziej walczysz w swojej firmie albo zespole?",
       "Odpisz na tego maila jednym zdaniem. Czytam każdą odpowiedź. Najczęstsze tematy zamieniam w lekcje w Akademii i w wydania newslettera.",
       "Jeśli wolisz kliknąć: ceny i marża / plan na rok / sprzedaż / zespół / czas i priorytety (każdy link dodaje tag z zainteresowaniem)."],
      ("Odpowiedz na maila", "brak przycisku: prośba o odpowiedź + 5 linków-tagów")),
    m("W7", "dzień 14, 7:30", "każdy", "Co dalej (i jak często będę pisać)", "Twoje 2 tygodnie z Guliwera 4",
      "Newsletter co 2 tygodnie we wtorek. Możesz zmienić częstotliwość jednym kliknięciem.",
      "Przejście do newslettera cyklicznego, ustawienie preferencji, kolejny krok w Akademii.",
      ["Dziękuję za te 2 tygodnie. Od teraz piszę co 2 tygodnie, we wtorek o 7:30: jeden temat ze strategii, jedno narzędzie, jedna lekcja z Akademii i jedna oferta, zawsze oznaczona.",
       "[Blok warunkowy] AK_DARMOWE = tak: „Masz za sobą darmowe działy Akademii. Kolejne działy (finanse, metryki, zespół) są w Akademii Pro.” Inaczej: „Do końca darmowych działów zostało Ci *|6 − AK_LEKCJE|* lekcji.”",
       "Chcesz rzadziej? Ustaw „raz w miesiącu” albo „tylko Akademia” w preferencjach."],
      ("Ustaw preferencje", "strona preferencji na guliwera4.pl"),
      "Na koniec serii: tag `seria_powitalna_ok`, kontakt wpada do newslettera cyklicznego."),
]

ZACHOWANIA = [
    m("Z1", "gdy AK_DARMOWE zmieni się na „tak”", "ze zgodą, bez Pro", "Ukończone 2 działy Akademii. Co dalej?", "Lilek ma dla Ciebie certyfikat (prawie)",
      "Kolejne działy: finanse, metryki, zespół. Akademia Pro na 12 miesięcy.",
      "Sprzedaż Akademii Pro 249 zł w najlepszym momencie: po ukończeniu darmowej części.",
      ["Gratulacje: darmowe działy Akademii masz za sobą. To *|AK_LEKCJE|* lekcji i seria *|AK_SERIA|* dni.",
       "Akademia Pro odblokowuje wszystkie kolejne działy i nowe lekcje dodawane w ciągu roku, zdejmuje limit serc i daje certyfikat ukończenia. Lekcje z Pro liczą się też do rabatu na materiały (do 30% zamiast ok. 7%).",
       "249 zł za 12 miesięcy, czyli ok. 68 gr dziennie za 5 minut nauki."],
      ("Odblokuj Akademię Pro", "przycisk Pro w aplikacji / produkt akademia-pro"),
      "Przypomnienie po 3 dniach tylko dla osób bez tagu `kupil_pro`, potem koniec serii (bez naciskania)."),
    m("Z2", "seria 7 i 30 dni", "ze zgodą", "7 dni z rzędu. Szacun!", "",
      "Krótka gratulacja, bez oferty.", "Wzmocnić nawyk. Mail bez sprzedaży.",
      ["7 dni nauki z rzędu. Lilek zrobił z tej okazji taniec, którego nie pokażemy. Jutro dzień 8?"],
      ("Otwórz lekcję", "/akademia/")),
    m("Z3", "7 dni bez lekcji (po co najmniej 1 lekcji)", "ze zgodą", "Wracasz na 5 minut?", "Lilek pyta, co słychać",
      "Twoja seria czeka. Jedna lekcja i wracasz do gry.", "Reaktywacja nauki.",
      ["Tydzień przerwy to nic złego. Najtrudniejsza lekcja to ta pierwsza po przerwie, więc wybrałam krótką: *|następna lekcja|*.",
       "Uwaga: rabat za naukę liczy aktywne dni z ostatnich 4 tygodni, więc po przerwie trochę spada. Wraca, gdy wracasz Ty."],
      ("Wracam do lekcji", "/akademia/")),
    m("Z4", "21 dni bez lekcji", "ze zgodą", "Zostawić Ci miejsce w Akademii?", "",
      "Bez presji. Możesz też zmienić, jak często piszę.", "Druga, ostatnia próba reaktywacji + zmiana preferencji.",
      ["Nie było Cię w Akademii od 3 tygodni. Rozumiem, kalendarz bywa bezlitosny.",
       "Masz 3 opcje: wracasz na lekcję, zostajesz tylko przy newsletterze co 2 tygodnie albo wybierasz „raz w miesiącu”."],
      ("Wracam do lekcji", "/akademia/"), "Dwa linki tekstowe do preferencji."),
    m("Z5", "kupon z Akademii wygasa za 2 dni", "ze zgodą, kupon niewykorzystany", "Twój kupon −*|PROCENT|*% wygasa pojutrze", "",
      "Kod *|KUPON_KOD|* działa do *|KUPON_DO|*.", "Przypomnienie (marketing, dlatego tylko za zgodą).",
      ["Twój kupon z Akademii (*|KUPON_KOD|*) działa jeszcze 2 dni, do *|KUPON_DO|*. Przycisk od razu dodaje go do koszyka."],
      ("Użyj kuponu", "/koszyk/?gk_kupon=*|KUPON_KOD|*")),
    m("Z6", "po zakupie Strategii na 1 stronie: dzień 1, 7, 14", "tag `kupil_strategia`, ze zgodą", "Jak wypełnić Strategię w 45 minut", "Następny krok: Planer 90 dni",
      "D1: kolejność pól i najczęstsze błędy. D7: Planer 90 dni (59 zł) zamienia strategię w plan kwartału. D14: prośba o opinię.",
      "Drugi zakup na drabinie i opinia.",
      ["D1: „Zacznij od pola »czego nie robimy«, potem cel, na końcu liczby.” D7: „Strategia mówi dokąd, Planer 90 dni mówi, co robisz w tym kwartale.” D14: „Jak Ci poszło? Jedno zdanie opinii bardzo mi pomoże.”"],
      ("D7: Zobacz Planer 90 dni", "produkt 70")),
    m("Z7", "po zakupie Pakietu: dzień 2, 10, 21", "tag `kupil_pakiet`, ze zgodą", "Od czego zacząć w Pakiecie", "Plan na 4 tygodnie z Pakietem",
      "D2: kolejność materiałów na 4 tygodnie. D10: opinia. D21: lista oczekujących kursu wideo.",
      "Wykorzystanie produktu (mniej zwrotów), opinia, kolejny szczebel: kurs.",
      ["D2: „Tydzień 1: Przewodnik + Audyt. Tydzień 2: Strategia na 1 stronie. Tydzień 3: Planer 90 dni. Tydzień 4: Skrypty i Dziennik decyzji.” D21: „Nagrywam kurs wideo Strategia w 6 tygodni. Osoby z listy oczekujących dostaną cenę przedsprzedaży.”"],
      ("D21: Zapisz się na listę oczekujących", "strona kursu")),
]

KAMPANIE = [
    ("17.11 (wt) 7:30", "Wydanie #1", "Strategia na 1 stronie: 5 pól, które wystarczą", "narzędzie: kalkulator zysku · lekcja: MECE · oferta: Strategia 39 zł", "cała lista ze zgodą"),
    ("24.11 (wt) 7:30", "Black Week 1/3", "Pakiet Kierunek Zysk 30% taniej do 30.11", "207,90 zł zamiast 297 zł; „Najniższa cena od początku sprzedaży (4.11.2026): 297 zł”", "bez tagu `kupil_pakiet`"),
    ("27.11 (pt) 12:00", "Black Week 2/3", "Black Friday: 3 dni do końca −30%", "krótki, dla tych, którzy nie kliknęli 24.11", "bez zakupu Pakietu, bez kliknięcia w BW 1/3"),
    ("30.11 (pn) 18:00", "Black Week 3/3", "Ostatni dzień −30% na Pakiet", "„do północy”, jedno zdanie + przycisk", "bez zakupu Pakietu"),
    ("8.12 (wt) 7:30", "Wydanie #2", "Ceny i marża: kiedy podnieść cenę", "narzędzie: kalkulator · lekcja: macierz BCG · oferta: Przewodnik 147 zł", "cała lista"),
    ("22.12 (wt) 7:30", "Wydanie #3 (świąteczne, krótkie)", "Przegląd roku w 15 minut", "narzędzie: checklista z Pakietu startowego · bez oferty · życzenia", "cała lista"),
    ("5.01 (wt) 7:30", "Kampania „Plan na 2027” 1/2", "Twój plan na 2027 na jednej kartce", "Strategia 39 zł + Planer 90 dni 59 zł · lekcja: OKR", "cała lista"),
    ("8.01 (pt) 12:00", "Kampania „Plan na 2027” 2/2", "Pierwszy kwartał 2027: 90 dni, 3 priorytety", "Planer 90 dni · tylko osoby bez zakupu", "bez `kupil_planer`"),
    ("19.01 (wt) 7:30", "Wydanie #4", "Piramida Minto: jak pisać maile, które ktoś przeczyta", "narzędzie: szablon maila do zarządu · lekcja: Minto · oferta: Akademia Pro", "cała lista"),
    ("2.02 (wt) 7:30", "Wydanie #5", "Kurs wideo Strategia w 6 tygodni: zapisy na listę oczekujących", "lekcja: 7S · oferta: lista oczekujących kursu z ceną przedsprzedaży", "cała lista"),
]

SZABLON = [
    ("Temat (≤ 45 znaków) + preheader (≤ 90)", "konkret albo pytanie; bez CAPS LOCK i wykrzykników w temacie"),
    ("1. Temat ze strategii (150–200 słów)", "jedna myśl, własnymi słowami Angeliki, przykład z małej firmy"),
    ("2. Narzędzie (50–80 słów)", "plik z Pakietu startowego, szablon albo wzór; zawsze do użycia od razu"),
    ("3. Lekcja z Akademii (40–60 słów)", "zapowiedź jednej lekcji + przycisk „Otwórz lekcję” (link z UTM)"),
    ("4. Oferta (40–60 słów, oznaczona „Oferta”)", "jeden produkt z drabiny; przy promocji najniższa cena z 30 dni"),
    ("P.S.", "pytanie do odpowiedzi albo zapowiedź kolejnego wydania"),
    ("Stopka", "dane firmy, powód otrzymania („zapis z Akademii / ze strony”), preferencje, wypis jednym kliknięciem"),
]

PRAWO = [
    "Zgoda na maile marketingowe osobna od regulaminu i rejestracji, pole niezaznaczone domyślnie (art. 398 Prawa komunikacji elektronicznej, RODO).",
    "Double opt-in w Mailchimpie: zapis liczy się dopiero po kliknięciu w mail potwierdzający. Mailchimp zapisuje datę, IP i źródło zgody.",
    "W każdym mailu: wypis jednym kliknięciem (nagłówek List-Unsubscribe; wymóg Gmaila i Yahoo), link do preferencji, dane firmy, powód otrzymania.",
    "Promocje: najniższa cena z 30 dni przed obniżką (dyrektywa Omnibus), bez fałszywej presji czasu; „do 30.11” musi być prawdą.",
    "Bez obietnic wyniku finansowego („zarobisz”, „podwoisz zysk”); rabat za naukę opisany uczciwie (zależy od regularności).",
    "Dostarczalność: uwierzytelniona domena guliwera4.pl w Mailchimpie (DKIM i SPF w DNS nazwa.pl), DMARC co najmniej p=none, nadawca kontakt@guliwera4.pl.",
    "Higiena listy: kontakty bez otwarć i kliknięć przez 90 dni dostają mail „zostajesz?”, a bez reakcji trafiają do archiwum. Na planie Free to też oszczędność limitu.",
    "Polityka prywatności: dopisać Mailchimp (Intuit) jako odbiorcę danych i transfer do USA (Data Privacy Framework).",
]

WDROZENIE = [
    ("Ty", "Mailchimp Free: klucz API (Konto → Extras → API keys) wpisany w panelu WordPressa, nigdy w czacie ani repo. Uwierzytelnienie domeny: rekordy DNS w nazwa.pl według instrukcji, którą przygotuję.", "do 15.11", "👤"),
    ("ja", "Mailchimp: lista „Guliwera 4”, pola i tagi z tabeli, stopka z danymi firmy, webhook wypisu do WordPressa.", "do 15.11", "🔧"),
    ("ja", "Pakiet startowy: kalkulator zysku (XLSX + kopia w Arkuszach Google), przykład strategii na 1 stronie, checklista przeglądu miesiąca. Pliki w bibliotece mediów.", "do 14.11", "🔧"),
    ("ja", "Wtyczka `guliwera-newsletter`: zapis i zgoda (Akademia + formularz), double opt-in, seria W1–W7 i maile Z1–Z7 wysyłane przez WordPress według harmonogramu, wypis i preferencje, przekazanie potwierdzonych osób do Mailchimpa, zdarzenie `generate_lead` w GTM.", "do 16.11", "🔧"),
    ("ja", "Strona /pakiet-startowy/ z formularzem i strona „Sprawdź skrzynkę” (z ofertą 39 zł po potwierdzeniu).", "do 16.11", "🔧"),
    ("ja", "Podgląd wszystkich maili serii i Z1–Z7 (zrzuty komputer + telefon) do akceptacji; szablon wydania #1 jako szkic w Mailchimpie. Nic nie startuje bez Twojej akceptacji.", "do 16.11", "🔧"),
    ("Ty", "Akceptacja treści i włączenie serii powitalnej. Test: zapis na własny adres, przejście całej ścieżki.", "16.11", "👤"),
    ("razem", "Start reklam K1 Akademia (czat o reklamach) kierujących do Akademii i /pakiet-startowy/.", "17.11", "👤"),
    ("ja", "Co 2 tygodnie: szkic wydania w czwartek przed wysyłką, Ty dopisujesz 1 akapit własnymi słowami i akceptujesz (ok. 45 min).", "od 12.11", "🔁"),
    ("ja", "Raport po 2 i 6 tygodniach: KPI vs cel, testy A/B tematów, co zmienić.", "1.12 i 29.12", "📊"),
]

DECYZJE = [
    ("Zawartość Pakietu startowego", "✅ Kalkulator zysku + przykład strategii na 1 stronie + przegląd miesiąca (decyzja 8.11)", "Najmocniej prowadzi do 39 zł i Pakietu, a kalkulator jest w strategii jako magnes."),
    ("Plan Mailchimpa", "✅ Free (decyzja 8.11) + automaty z WordPressa", "Free nie ma wieloetapowych automatyzacji i ma 500 wysyłek w miesiącu, więc serie wysyła WordPress. Próg zmiany planu: 200 kontaktów albo 450 wysyłek w miesiącu."),
    ("Głos maili marketingowych", "✅ Pierwsza osoba: „Angelika z Guliwera 4” (decyzja 8.11)", "Newsletter sprzedaje markę osobistą. Maile sklepu zostają w formie „my”."),
    ("Rabat Akademii 30%", "Zostawić w komunikacji, ale mocniej sprzedawać Pro", "Strategia krytykuje rabat 30% za koszt marży; do 30% działa teraz tylko z Pro, więc wspiera sprzedaż Pro."),
    ("Black Week", "Pakiet −30% 24–30.11, 3 maile do listy", "Zgodne z planem reklam (K2); kupony Akademii się nie łączą."),
]


# ─────────────────────────── Wyjście: Markdown ───────────────────────────
def md():
    o = ["# Newsletter dla osób z Akademii Guliwera: plan na listopad 2026 – styczeń 2027", "",
         "Źródło: `marketing/newsletter_akademia.py` (z niego powstaje ten plik i strona z planem). Stan: 8.11.2026.", "",
         "**Teza:** " + CEL["teza"], "", "## KPI", "", "| KPI | Cel | Uwagi |", "|---|---|---|"]
    o += [f"| {a} | {b} | {c} |" for a, b, c in CEL["kpi"]]
    o += ["", f"## {PAKIET['nazwa']}", "", "| Element | Co to jest | Stan |", "|---|---|---|"]
    o += [f"| {a} | {b} | {c} |" for a, b, c in PAKIET["elementy"]]
    o += [""] + [f"- {z}" for z in PAKIET["zasady"]]
    o += ["", "## Wejścia na listę", "", "| Wejście | Jak działa | Oznaczenie |", "|---|---|---|"]
    o += [f"| {a} | {b} | {c} |" for a, b, c in WEJSCIA]
    o += ["", "## Kto co wysyła (Mailchimp Free)", "", ARCHITEKTURA["zalozenie"], "", "| Narzędzie | Co robi | Jak |", "|---|---|---|"]
    o += [f"| {a} | {b} | {c} |" for a, b, c in ARCHITEKTURA["podzial"]]
    o += [""] + [f"- {x}" for x in ARCHITEKTURA["progi"]]
    o += ["", "## Dane w Mailchimpie (pola i tagi)", "", "| Pole / tag | Zawartość | Aktualizacja |", "|---|---|---|"]
    o += [f"| `{a}` | {b} | {c} |" for a, b, c in DANE]
    for tytul, lista in (("Seria powitalna (14 dni, automatyczna)", SERIA), ("Maile wyzwalane zachowaniem (automatyczne)", ZACHOWANIA)):
        o += ["", f"## {tytul}", ""]
        for x in lista:
            o += [f"### {x['kod']} · {x['temat_a']}", "",
                  f"- **Kiedy:** {x['kiedy']} · **Segment:** {x['segment']}",
                  f"- **Temat A:** {x['temat_a']}" + (f" · **Temat B:** {x['temat_b']}" if x['temat_b'] else ""),
                  f"- **Preheader:** {x['preheader']}", f"- **Cel:** {x['cel']}",
                  f"- **CTA:** {x['cta'][0]} → {x['cta'][1]}"]
            if x["warunek"]:
                o.append(f"- **Uwagi:** {x['warunek']}")
            o += ["", "Treść:", ""] + [f"> {p}" for p in x["tresc"]] + [""]
    o += ["## Newsletter cykliczny i kampanie (ręczne, każda wysyłka po akceptacji)", "",
          "Rytm: **co 2 tygodnie, wtorek 7:30**. Managerowie i właściciele firm czytają pocztę przed pracą; wtorek omija poniedziałkowy zalew maili. Kampanie promocyjne: dodatkowo piątek 12:00 (przerwa) i ostatni dzień promocji 18:00. Po 6 tygodniach test A/B: wtorek 7:30 vs czwartek 12:00.", "",
          "| Data | Typ | Temat | Zawartość | Segment |", "|---|---|---|---|---|"]
    o += [f"| {a} | {b} | {c} | {d} | {e} |" for a, b, c, d, e in KAMPANIE]
    o += ["", "### Szablon wydania", "", "| Część | Zasada |", "|---|---|"] + [f"| {a} | {b} |" for a, b in SZABLON]
    o += ["", "## Prawo i dostarczalność", ""] + [f"- {p}" for p in PRAWO]
    o += ["", "## Wdrożenie", "", "| Kto | Co | Termin | Stan |", "|---|---|---|---|"] + [f"| {a} | {b} | {c} | {d} |" for a, b, c, d in WDROZENIE]
    o += ["", "## Decyzje Angeliki", "", "| Decyzja | Rekomendacja | Dlaczego |", "|---|---|---|"] + [f"| {a} | {b} | {c} |" for a, b, c in DECYZJE]
    return "\n".join(o) + "\n"


# ─────────────────────────── Wyjście: strona (Artifact) ───────────────────────────
def e(s):
    return html.escape(s)

def karta(x):
    akap = "".join(f"<p>{e(p)}</p>" for p in x["tresc"])
    b = f'<p class="tb"><span>B</span>{e(x["temat_b"])}</p>' if x["temat_b"] else ""
    uw = f'<p class="uw">{e(x["warunek"])}</p>' if x["warunek"] else ""
    return f'''<article class="mail" id="{x["kod"].lower()}">
<div class="mh"><span class="kod">{e(x["kod"])}</span><span class="kiedy">{e(x["kiedy"])}</span></div>
<h4><span class="ab">A</span>{e(x["temat_a"])}</h4>{b}
<p class="pre">{e(x["preheader"])}</p>
<dl><div><dt>Segment</dt><dd>{e(x["segment"])}</dd></div><div><dt>Cel</dt><dd>{e(x["cel"])}</dd></div><div><dt>Przycisk</dt><dd><b>{e(x["cta"][0])}</b> → {e(x["cta"][1])}</dd></div></dl>
<details><summary>Treść maila</summary><div class="tresc">{akap}</div>{uw}</details>
</article>'''

def tabela(nag, wiersze, klasa=""):
    h = f'<div class="tab {klasa}"><table><thead><tr>' + "".join(f"<th>{e(n)}</th>" for n in nag) + "</tr></thead><tbody>"
    for w in wiersze:
        h += "<tr>" + "".join(f"<td>{e(c)}</td>" for c in w) + "</tr>"
    return h + "</tbody></table></div>"

def strona():
    dni = [("0", "W1", "pakiet"), ("1", "W2", "39 zł"), ("3", "W3", "nawyk"), ("6", "W4", "liczby"), ("8", "W5", "Pakiet"), ("10", "W6", "pytanie"), ("14", "W7", "co dalej")]
    os_ = "".join(f'<li style="--d:{d}"><a href="#{k.lower()}"><b>{k}</b><span>dzień {d}</span><em>{e(t)}</em></a></li>' for d, k, t in dni)
    drabina = [("0 zł", "Akademia + Pakiet startowy + newsletter"), ("39 zł", "Strategia na 1 stronie"), ("297 zł", "Pakiet Kierunek Zysk"), ("249 zł", "Akademia Pro (12 mies.)"), ("lista", "Kurs wideo: przedsprzedaż")]
    dr = "".join(f'<li><span class="c">{e(c)}</span>{e(n)}</li>' for c, n in drabina)
    return f'''<title>Newsletter Akademii Guliwera</title>
<meta name="claude:viewport-fit-sides" content="cover">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=DM+Sans:opsz,wght@9..40,400;9..40,600;9..40,700&family=Caveat:wght@600&display=swap">
<style>
/* Układ: lejek od lewej do prawej (wejścia → seria 14 dni → newsletter), pod nim karty maili i plan wdrożenia */
:root{{--bg:#FFF6EE;--card:#FFFFFF;--fg:#3A2340;--muted:#7A6580;--accent:#E8512A;--coral:#FF6B3D;--line:#F0DDCF;--lil:#EDE5FB;--zolty:#FFEFC7;--roz:#FFD9E4;
--f-d:"Fraunces",Georgia,serif;--f-b:"DM Sans",system-ui,sans-serif;--f-h:"Caveat",cursive;--f-m:ui-monospace,Menlo,monospace}}
@media (prefers-color-scheme: dark){{:root:not([data-theme="light"]){{--bg:#1F1424;--card:#2A1D30;--fg:#F6ECF3;--muted:#BBA8BF;--accent:#FF8A63;--coral:#FF8A63;--line:#3E2C45;--lil:#352646;--zolty:#3F3320;--roz:#40263A;color-scheme:dark}}}}
:root[data-theme="dark"]{{--bg:#1F1424;--card:#2A1D30;--fg:#F6ECF3;--muted:#BBA8BF;--accent:#FF8A63;--coral:#FF8A63;--line:#3E2C45;--lil:#352646;--zolty:#3F3320;--roz:#40263A;color-scheme:dark}}
*{{box-sizing:border-box}}
body{{background:var(--bg);color:var(--fg);font:16.5px/1.6 var(--f-b);padding-left:max(16px,env(safe-area-inset-left,0px));padding-right:max(16px,env(safe-area-inset-right,0px))}}
main{{max-width:1120px;margin:0 auto;padding-block:40px 90px;display:grid;gap:64px}}
h1,h2,h3,h4{{font-family:var(--f-d);font-weight:600;line-height:1.12;margin:0;text-wrap:balance}}
h1{{font-size:clamp(34px,5vw,56px);max-width:18ch}} h2{{font-size:clamp(26px,3.2vw,34px);margin-bottom:8px}} h3{{font-size:20px;margin:22px 0 10px}}
.pis{{font-family:var(--f-h);color:var(--accent);font-size:27px;line-height:1;display:block;margin-bottom:6px}}
.lead{{font-size:19px;max-width:62ch;color:var(--muted);margin:16px 0 0}}
.sek>p.opis{{max-width:68ch;color:var(--muted);margin:0 0 18px}}
.drabina{{list-style:none;margin:26px 0 0;padding:0;display:flex;flex-wrap:wrap;gap:10px}}
.drabina li{{background:var(--card);border:1px solid var(--line);border-radius:999px;padding:8px 16px 8px 8px;display:flex;align-items:center;gap:10px;font-size:15px}}
.drabina li:first-child{{background:var(--accent);color:#fff;border-color:var(--accent)}} .drabina li:first-child .c{{background:#fff;color:var(--accent)}}
.drabina .c{{font-weight:700;font-size:13px;background:var(--zolty);color:var(--fg);border-radius:999px;padding:3px 10px;font-variant-numeric:tabular-nums;white-space:nowrap}}
.tab{{overflow-x:auto;border:1px solid var(--line);border-radius:16px;background:var(--card)}}
table{{border-collapse:collapse;width:100%;font-size:15px}} th,td{{text-align:left;padding:11px 14px;border-bottom:1px solid var(--line);vertical-align:top}}
th{{font-size:12px;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);font-weight:700}} tr:last-child td{{border-bottom:0}}
.kpi td:nth-child(2){{font-weight:700;white-space:nowrap;font-variant-numeric:tabular-nums}}
.os{{list-style:none;margin:8px 0 26px;padding:28px 0 0;position:relative;display:flex;justify-content:space-between;gap:6px}}
.os::before{{content:"";position:absolute;left:0;right:0;top:12px;height:4px;border-radius:4px;background:linear-gradient(90deg,var(--coral),#F4799F 55%,#B9A5E3)}}
.os li{{flex:1;min-width:0;position:relative}} .os a{{display:grid;gap:2px;text-decoration:none;color:var(--fg)}}
.os a::before{{content:"";position:absolute;top:-22px;left:0;width:14px;height:14px;border-radius:50%;background:var(--card);border:3px solid var(--accent)}}
.os b{{font-family:var(--f-d);font-size:20px}} .os span{{font-size:13px;color:var(--muted)}} .os em{{font-style:normal;font-size:14px}}
.os a:hover b,.os a:focus-visible b{{color:var(--accent)}}
@media(max-width:640px){{.os{{flex-direction:column;padding:0 0 0 28px;gap:12px}} .os::before{{left:5px;right:auto;top:0;bottom:0;width:4px;height:auto}} .os a{{grid-template-columns:44px 70px 1fr;align-items:baseline}} .os a::before{{top:4px;left:-28px}}}}
.siatka{{display:grid;grid-template-columns:repeat(auto-fill,minmax(min(100%,330px),1fr));gap:16px}}
.mail{{background:var(--card);border:1px solid var(--line);border-radius:18px;padding:18px 20px;min-width:0;display:flex;flex-direction:column;gap:8px;scroll-margin-top:20px}}
.mh{{display:flex;gap:10px;align-items:center;flex-wrap:wrap}} .kod{{font:700 13px var(--f-m);background:var(--lil);border-radius:8px;padding:3px 8px}} .kiedy{{font-size:13.5px;color:var(--muted)}}
.mail h4{{font-size:19px;display:flex;gap:8px;align-items:baseline}} .ab,.tb span{{font:700 11px var(--f-b);color:var(--accent);border:1.5px solid currentColor;border-radius:6px;padding:0 5px;flex:none;position:relative;top:-2px}}
.tb{{margin:0;display:flex;gap:8px;align-items:baseline;color:var(--muted);font-size:15px}}
.pre{{margin:0;font-size:14.5px;color:var(--muted);font-style:italic}}
.mail dl{{margin:4px 0 0;display:grid;gap:6px;font-size:14.5px}} .mail dl div{{display:grid;grid-template-columns:76px minmax(0,1fr);gap:8px}} .mail dt{{color:var(--muted);font-size:13px}} .mail dd{{margin:0;min-width:0;overflow-wrap:anywhere}}
details{{margin-top:auto;padding-top:6px}} summary{{cursor:pointer;color:var(--accent);font-weight:600;font-size:15px}} summary:focus-visible{{outline:2px solid var(--accent);outline-offset:3px;border-radius:4px}}
.tresc{{margin-top:10px;border-left:3px solid var(--roz);padding-left:14px;font-size:15px}} .tresc p{{margin:0 0 10px}}
.uw{{font-size:13.5px;color:var(--muted);background:var(--zolty);border-radius:10px;padding:8px 12px;margin:6px 0 0}}
.pakiet{{display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,250px),1fr));gap:16px}}
.pakiet div{{background:var(--card);border:1px solid var(--line);border-radius:18px;padding:18px 20px}} .pakiet h4{{font-size:19px;margin-bottom:6px}} .pakiet p{{margin:0 0 8px;font-size:15px}} .pakiet small{{color:var(--muted);font-size:13px}}
ul.pr{{max-width:76ch;padding-left:20px;margin:0}} ul.pr li{{margin-bottom:8px}}
.stan td:last-child{{font-size:18px;text-align:center}}
.wazne{{background:var(--zolty);border-radius:16px;padding:16px 20px;max-width:76ch}}
</style>
<main>
<header><span class="pis">plan newslettera</span><h1>Newsletter Akademii Guliwera</h1>
<p class="lead">{e(CEL["teza"])} Plan na 17.11.2026–2.02.2027: darmowy Pakiet startowy, seria powitalna na 14 dni, maile wyzwalane nauką w Akademii i wydania co 2 tygodnie.</p>
<ol class="drabina" aria-label="Drabina produktów">{dr}</ol></header>

<section class="sek"><h2>Cele i liczby</h2><p class="opis">Liczymy kliknięcia, nie otwarcia: Apple Mail zawyża otwarcia. Pierwszy przegląd 1.12, drugi 29.12.</p>
{tabela(["KPI", "Cel", "Uwagi"], CEL["kpi"], "kpi")}</section>

<section class="sek"><h2>{e(PAKIET["nazwa"])}</h2><p class="opis">Magnes na zapis. Trzy krótkie narzędzia, każde prowadzi do kolejnego szczebla drabiny.</p>
<div class="pakiet">{"".join(f"<div><h4>{e(a)}</h4><p>{e(b)}</p><small>{e(c)}</small></div>" for a, b, c in PAKIET["elementy"])}</div>
<ul class="pr" style="margin-top:16px">{"".join(f"<li>{e(z)}</li>" for z in PAKIET["zasady"])}</ul></section>

<section class="sek"><h2>Jak ktoś trafia na listę</h2><p class="opis">Zawsze osobna zgoda i potwierdzenie mailem (double opt-in). Najważniejsze wejście to ekran po rejestracji w Akademii.</p>
{tabela(["Wejście", "Jak działa", "Oznaczenie w Mailchimpie"], WEJSCIA)}
<h3>Kto co wysyła</h3><p class="wazne">{e(ARCHITEKTURA["zalozenie"])}</p>
{tabela(["Narzędzie", "Co robi", "Jak"], ARCHITEKTURA["podzial"])}
<ul class="pr" style="margin-top:14px">{"".join(f"<li>{e(x)}</li>" for x in ARCHITEKTURA["progi"])}</ul>
<h3>Dane, które WordPress przekazuje do Mailchimpa</h3>
{tabela(["Pole / tag", "Zawartość", "Aktualizacja"], DANE)}</section>

<section class="sek"><h2>Seria powitalna: 14 dni, automatycznie</h2><p class="opis">Wysyła WordPress, nie zużywa limitu Mailchimpa. P0 potwierdza zapis, W1 dostarcza pakiet. Każdy mail ma jedną lekcję albo narzędzie i jeden przycisk. Oferty: 39 zł w dniu 1, Pakiet w dniu 8, Akademia Pro po ukończeniu darmowych działów.</p>
<ol class="os" aria-label="Oś serii powitalnej">{os_}</ol>
<div class="siatka">{"".join(karta(x) for x in SERIA)}</div></section>

<section class="sek"><h2>Maile wyzwalane nauką i zakupami</h2><p class="opis">Lecą same z WordPressa, tylko do osób ze zgodą. WordPress sprawdza dane z Akademii raz dziennie.</p>
<div class="siatka">{"".join(karta(x) for x in ZACHOWANIA)}</div></section>

<section class="sek"><h2>Wydania i kampanie: listopad – styczeń</h2><p class="opis">Co 2 tygodnie we wtorek o 7:30: managerowie czytają pocztę przed pracą, a wtorek omija poniedziałkowy zalew. Kampanie mają dodatkowy piątek 12:00 i ostatni dzień promocji 18:00. Każda wysyłka ręczna wychodzi dopiero po Twojej akceptacji.</p>
{tabela(["Data", "Typ", "Temat", "Zawartość", "Segment"], KAMPANIE)}
<h3>Szablon każdego wydania</h3>
{tabela(["Część", "Zasada"], SZABLON)}</section>

<section class="sek"><h2>Prawo i dostarczalność</h2><ul class="pr">{"".join(f"<li>{e(p)}</li>" for p in PRAWO)}</ul></section>

<section class="sek"><h2>Wdrożenie do startu reklam 17.11</h2><p class="opis">🔧 przygotowuję · 👤 Twoja czynność · ❓ Twoja decyzja · 🔁 stały rytm · 📊 raport</p>
{tabela(["Kto", "Co", "Termin", ""], WDROZENIE, "stan")}</section>

<section class="sek"><h2>Twoje decyzje</h2>
{tabela(["Decyzja", "Rekomendacja", "Dlaczego"], DECYZJE)}</section>
</main>'''


if __name__ == "__main__":
    here = os.path.dirname(os.path.abspath(__file__))
    open(os.path.join(here, "NEWSLETTER-AKADEMIA.md"), "w", encoding="utf-8").write(md())
    if len(sys.argv) > 1:
        os.makedirs(sys.argv[1], exist_ok=True)
        open(os.path.join(sys.argv[1], "newsletter-akademia.html"), "w", encoding="utf-8").write(strona())
    print("ok")
