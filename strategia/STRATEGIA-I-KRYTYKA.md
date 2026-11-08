# Strategia Guliwera 4: ścieżka zakupowa, koszty, EBITDA i krytyka (stan 8.11.2026)

Repozytorium jest publiczne. Są tu tylko ceny ze strony, wzory i **założenia**. Prawdziwe koszty, sprzedaż i podatki
wpisujemy wyłącznie w prywatnej kopii modelu (`model-szablon-2027.xlsx` → Google Drive).
Legenda: 🧾 potwierdzić z księgową · ❓ decyzja Angeliki.

---

## 1. Obecna strategia na stronie i ścieżka zakupowa

**Co sprzedaje strona dziś (ceny ze strony):**

| Linia | Produkt | Cena | Dla kogo |
|---|---|---|---|
| B2B | Strateg na abonament | od 3 500 zł netto / mies., min. 3 mies. | zarządy, duże firmy |
| B2B | Warsztat pół dnia / cały dzień | 4 900 / 8 900 zł netto (do 12 os.) | zespoły kierownicze |
| B2B | Sesja strategiczna 60 min | 1 200 zł netto | 1 osoba |
| B2B (plan) | Przegląd Decyzji | 3 000 zł netto | kupujący pakiet PDF (strony /konsultacja jeszcze nie ma) |
| Kurs | Strategia w 6 tygodni (luty 2027) | 1 990 zł, z listy 1 590 zł, do 20 os. | właściciele MŚP, młodzi managerowie |
| PDF | Kierunek Zysk: 7 materiałów + pakiet | 39–147 zł, pakiet 297 zł | właściciele małych firm |
| Darmowe | Akademia Guliwera (aplikacja) | 0 zł, rabat do 30% na PDF | młodzi managerowie |
| Fizyczny (plan) | Planer roczny (ADHD), 50 szt. | 109–129 zł | osoby z ADHD w korporacji |

**Ścieżki (gdzie się zaczynają i gdzie kończą):**

1. **B2C – PDF:** wejście (reklama / LinkedIn / wyszukiwarka) → strona główna albo /materialy/ →
   `/zamowienie/?add-to-cart=ID` → zgoda na natychmiastowe dostarczenie (bez adresu) → płatność Stripe → mail z plikiem.
   **Koniec ścieżki = mail z plikiem.** Nie ma dalszego kroku: brak sekwencji maili po zakupie na żywo,
   brak oferty kolejnego produktu, brak strony konsultacji (/konsultacja).
2. **Akademia:** strona główna → /akademia/ (konto) → lekcje → kupon do 30% → PDF. Koniec = tańszy PDF.
3. **Kurs:** /kurs-strategia/ → formularz listy oczekujących (Jetpack) → (luty) mail z zapisem. Koniec = zapis na listę;
   trzeba sprawdzić, czy zgłoszenia trafiają do Mailchimp.
4. **B2B:** strona główna (hero kieruje na Usługi B2B) → /uslugi/ → /kontakt/ → rozmowa 20 min → oferta → faktura.
   Koniec = faktura; naturalny następny krok (abonament) jest opisany, ale nie ma go w procesie.

**Stan faktyczny lejka (Jetpack, 26.10–8.11):** 144 odsłony, ok. 20 osób, **0 odsyłaczy z zewnątrz**.
Odsłony to głównie strona główna, Moje konto, Materiały – w dużej mierze testy. Lejek jeszcze nie istnieje: nie ma ruchu,
nie ma historii konwersji, a według `NOTATKI.md` połączenie konta Stripe i tag GA4 są otwartymi zadaniami
(wtyczka Stripe jest aktywna, połączenia konta nie da się sprawdzić z kontenera).

**Ekonomia ścieżek (założenia modelu, wariant: VAT dla doradztwa = tak):**

| Produkt | Cena netto dla firmy | Marża po CAC / godz. | Ćwiartka macierzy |
|---|---|---|---|
| Abonament | 3 500 zł | ok. 500 zł/h | Dojna krowa |
| Kurs (uczestnik) | ok. 1 455 zł | ok. 470 zł/h | Dojna krowa (staje się Gwiazdą przy grupie 15–20 os.) |
| Sesja 60 min | 1 200 zł | ok. 470 zł/h | Dojna krowa |
| Przegląd Decyzji | 3 000 zł | ok. 370 zł/h | Dojna krowa |
| Warsztat ½ / 1 dzień | 4 900 / 8 900 zł | ok. 340–350 zł/h | Dojna krowa |
| PDF (średni koszyk 95 zł) | ok. 77 zł | ok. 95 zł/h przy CAC 60 zł | Znak zapytania |
| Planer papierowy | 97 zł | ok. 16 zł/h (z 80 h projektu) | **Pies** |

---

## 2. Koszty, budżet, podatki – co znalazłam, czego brakuje

**Znalezione w repozytorium i wcześniejszych czatach:**
- reklamy: **1 000 zł/mies.** (Meta 650 / Google 350), 17.11.2026 – luty 2027 (`reklamy/gemini/01-strategia-brief.md`);
- KPI reklam: koszt zakupu ≤60 zł, ROAS ≥2, koszt rejestracji w Akademii ≤8 zł;
- Black Friday: −30% na Pakiet (297 → 207,90 zł), 24–30.11 (propozycja, ❓);
- planer: partia 50 szt. = **1 900–2 900 zł** gotówki z góry (druk 35–55 zł/szt. brutto, proof i transport 150 zł, opakowanie 2,5 zł);
  ryczałt dla towaru 3% 🧾; BDO i GPSR do załatwienia;
- forma: **ryczałt, zwolnienie podmiotowe z VAT** (limit 200 000 zł 🧾), opłaty za płatność przyjęte 2%;
- konsultacja Przegląd Decyzji: ok. 8 h pracy, limit 6/mies., konwersja z pakietu 8–12% (założenie).

**Nie znalazłam** (w repo, na Dysku ani w czatach, które mogę przeczytać): ZUS (pełny / preferencyjny / mały ZUS plus / etat),
kosztu księgowej, abonamentów (plan WordPress.com, Canva, Metricool, Mailchimp), stawek ryczałtu dla PDF-ów i konsultingu.
W modelu są jako parametry z wartościami przykładowymi – do podmiany w prywatnej kopii.

---

## 3. Jak to się przekłada na EBITDA

EBITDA w JDG = przychód − koszty działalności (reklamy, narzędzia, księgowa, druk, opłaty). Nie ma amortyzacji ani odsetek,
a ryczałt i składki właścicielki są **pod** EBITDA. Dlatego w JDG EBITDA zawyża wynik: nie zawiera wynagrodzenia za Twój czas.
Model liczy więc trzy miary: **EBITDA**, **zysk „na rękę”** (po ZUS, zdrowotnej, ryczałcie)
i **EBITDA po koszcie alternatywnym czasu** (godziny × stawka alternatywna).

Ilustracja przy domyślnych założeniach (2027, VAT dla doradztwa = tak, ZUS preferencyjny, stawka alternatywna 250 zł/h):

| | Ostrożny | Bazowy | Ambitny | **Rekomendowany** |
|---|---|---|---|---|
| Przychód netto | 71 tys. | 218 tys. | 495 tys. | **190 tys.** |
| Reklamy | 6 tys. | 12 tys. | 24 tys. | **4,8 tys.** |
| EBITDA | 49 tys. | 183 tys. | 435 tys. | **171 tys.** |
| Zysk na rękę | 25 tys. | 141 tys. | 352 tys. | **130 tys.** |
| Godziny / mies. | 42 | 72 ❌ | 128 ❌ | **56 ✅** |
| Zysk na rękę / h | 50 zł | 162 zł | 230 zł | **195 zł** |
| EBITDA po koszcie czasu | −77 tys. | −34 tys. | +52 tys. | **+4 tys.** |
| Udział B2B w przychodzie | 57% | 60% | 60% | **72%** |

Wniosek: w każdym scenariuszu, który w ogóle się opłaca, **60–75% EBITDA daje B2B i kurs**. PDF-y, Akademia i planer
to narzędzia marketingowe, nie źródło zysku. Scenariusz ambitny wymaga 128 h/mies., więc bez zatrudnienia kogoś jest nierealny.

---

## 4. Krytyka: co w obecnym planie nie działa (od najważniejszego)

1. **Sklep może nie przyjmować pieniędzy.** Połączenie Stripe i GA4 to otwarte zadania. Reklamy od 17.11 bez działającej
   płatności i pomiaru to wydatek bez możliwości sprzedaży i bez danych dla algorytmów. **Warunek startu reklam: zakup testowy + GA4.**
2. **VAT dla doradztwa.** Usługi doradcze są wyłączone ze zwolnienia podmiotowego (art. 113 ust. 13 ustawy o VAT) 🧾.
   Jeśli to dotyczy sesji, Przeglądu Decyzji, warsztatów i abonamentu, VAT obowiązuje **od pierwszej faktury za doradztwo**
   i obejmuje **całą** sprzedaż, także PDF, kurs i planer. Przy cenach brutto bez zmian to −18,7% przychodu z B2C.
   Klientom B2B to nie przeszkadza (ceny netto + VAT, odliczają go). Trzeba to rozstrzygnąć **przed pierwszą fakturą B2B**,
   a hasło „zwolniona z VAT” przestaje być prawdziwe w reklamach.
3. **Plan reklam nie spina się ekonomicznie.** PDF: 77 zł netto − opłaty − ryczałt ≈ 68 zł; przy CAC 60 zł zostaje ok. 8 zł
   na zamówieniu. ROAS 2 to dla PDF-ów w praktyce zero. Rejestracje w Akademii za 8 zł z rabatem 30% dodatkowo obniżają marżę.
   1 000 zł/mies. przy CAC 60 zł daje ok. 17 zamówień, a nie 40, które zakłada scenariusz bazowy.
   12 tys. zł rocznie na zimne reklamy PDF to tyle, ile marża z 1,3 warsztatu całodniowego.
4. **Za dużo produktów na zerowy ruch.** 8 PDF-ów, aplikacja (z planem Google Play i App Store), kurs, 5 formatów B2B,
   planer papierowy, Alibaba, newsletter, agent social media. Każdy kawałek jest sensowny, razem rozpraszają przy 40–60 h/mies.
   Zasada 80/20: B2B i kurs dają większość zysku.
5. **Planer papierowy przed świętami to „pies” w macierzy.** 1,9–2,9 tys. zł gotówki z góry, ok. 80 h składu 220 stron,
   pliki do drukarni do 24.11, czyli w tym samym tygodniu co Black Friday. Do tego BDO i GPSR. Maksymalny zysk z partii to
   ok. 1,6–1,7 tys. zł, przy sprzedaży wszystkich 50 sztuk. Wychodzi ok. 16 zł/h, przy koszcie alternatywnym 250 zł/h.
6. **Cztery grupy odbiorców w czterech komunikatach.** Strona główna: korporacje („dla dużych firm”). PDF: właściciele
   małych firm. Akademia: młodzi managerowie. Planer: osoby z ADHD. Marka mówi do każdej z tych grup naraz,
   więc żadna nie czuje się zaproszona. Pieniądze są w pierwszej grupie.
7. **Nie ma lejka B2B.** Scenariusz rekomendowany potrzebuje ok. 140 leadów B2B rocznie, czyli ok. 12 miesięcznie (przy 25% skuteczności),
   a dziś ma 0 odsyłaczy. Najważniejszy KPI 2027 to leady B2B, nie sprzedaż PDF-ów.
8. **Rabat Akademii kanibalizuje sprzedaż.** Do 30% dostają najbardziej zaangażowane osoby, które i tak najczęściej by kupiły.
   Lepiej: rabat na kurs albo licencja Akademii dla zespołów (B2B, HR/L&D).
9. **Nazwy firm na stronie głównej** („Na co dzień pracuję z…”) wymagają zgody tych firm. Sprawdź też umowę o pracę
   i zakaz konkurencji, jeśli pracujesz na etacie ❓.
10. **EBITDA jest myląca, bo czas jest darmowy.** Ostrożny i bazowy mają ujemną EBITDA po koszcie czasu. Prowadzenie firmy
    opłaca się wtedy mniej niż alternatywa.

---

## 5. Rekomendacja: scenariusz B2B-first z kursem jako skalą

**Zasada:** zysk z B2B i kursu, PDF-y i Akademia jako dowód kompetencji i wejście do lejka, planer odłożony.

**Do końca 2026 (KPI na 31.12.2026):**
- zakup testowy działa (Stripe), GA4 zbiera `purchase` i `generate_lead`, rozstrzygnięty VAT dla doradztwa 🧾;
- planer papierowy **wstrzymany**; zamiast niego wersja PDF planera do druku (zero zapasu), test popytu;
- Black Friday −30% na Pakiet: komunikat do własnej publiczności (Mailchimp, LinkedIn) + retargeting; zimne reklamy ≤400 zł/mies.;
- strona /konsultacja (Przegląd Decyzji) i 6 maili po zakupie na żywo (po Twojej akceptacji);
- 10 rozmów z ciepłymi kontaktami tygodniowo → cel: **2 sesje i 1 warsztat** zaklepane na I kw. 2027,
  **≥60 osób** na liście kursu do 15.01.2027.

**2027 (miesięczne KPI: arkusz „KPI 2027”):** abonament od II kw. (1 klient, potem 2), kurs w 2–3 edycjach
(10–15 os.), 4 warsztaty pół dnia + 3 całodniowe, sesje 1–3/mies., PDF ok. 20 zamówień/mies. bez zimnych reklam,
≤56 h/mies.

**Kryteria wyjścia (bez nich nie skalujemy):**
- reklamy zimne: po 8 tygodniach CAC >60 zł albo ROAS <2 → stop, budżet na LinkedIn i retargeting;
- kurs: lista <60 osób na 15.01 → przesunięcie edycji, nie obniżka ceny;
- planer papierowy: wraca tylko, gdy PDF planera sprzeda ≥100 szt. do czerwca 2027 (partia jesień 2027 na 2028);
- aplikacja w sklepach (Google Play/App Store): dopiero po ≥300 aktywnych użytkownikach w przeglądarce.

---

## 6. Model (`model-szablon-2027.xlsx`)

Arkusze: Jak czytać · Parametry · Produkty · Wynik i EBITDA · Macierz zysków · KPI 2027.
Każda kwota to parametr albo wzór. Zmiana ceny, kosztu, ZUS, VAT albo wolumenu przelicza EBITDA, zysk na rękę,
próg rentowności, limit VAT, CAC/ROAS, macierz i KPI miesięczne. Skrypt `buduj-model.py` odtwarza plik.
Prawdziwe liczby wpisujemy tylko w kopii na prywatnym Dysku.
