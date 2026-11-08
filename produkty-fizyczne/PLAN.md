# Produkty fizyczne Guliwera 4: planery „Kierunek Zysk” na papierze

Stan na 8.11.2026. Etap: pytania i plan (bez zamówień, bez kontaktu z dostawcami, bez zmian na żywej stronie).
Repozytorium jest publiczne: tu są tylko **widełki rynkowe** i założenia. Prawdziwe oferty, ceny zakupu
i dane kontaktowe dostawców trzymamy poza repo (np. prywatny arkusz na Google Drive).

Legenda: ❓ decyzja Angeliki · 🧾 potwierdzić z księgową · 🔧 mogę przygotować · 👤 robi Angelika

---

## 0. Rekomendacja w skrócie
1. **Planer niedatowany** (bez dat): nie traci wartości po styczniu, nie zostaje martwy towar.
2. **Na styczeń: drukarnia w Polsce, nakład próbny 50–100 szt.** Alibaba do stycznia jest zbyt ryzykowna:
   próbki + produkcja + transport to 6–10 tygodni, a fabryki w Chinach od ok. połowy stycznia do końca lutego
   zwalniają przed Chińskim Nowym Rokiem (6.02.2027).
3. **Alibaba jako druga partia** (wiosna 2027), gdy wiemy, ile się sprzedaje. Tam opłaca się dopiero od 300–500 szt.
4. **Wysyłka tylko w Polsce na start** (InPost Paczkomat + kurier), pakowanie w domu/biurze w Lublinie.
5. Na stronie zmiany w osobnej wersji wtyczki, testowane przed wgraniem. Produkty cyfrowe działają jak teraz.

---

## 1. Specyfikacja produktów (wersja robocza do RFQ)

### 1.1 Planer 90 dni (produkt główny)
| Cecha | Propozycja | Uwagi |
|---|---|---|
| Format | A5, 148 × 210 mm | Najpopularniejszy, mieści się w Paczkomacie A i kopercie. Alternatywa: B5 (176 × 250) |
| Objętość | ok. 208 stron (104 kartki) | Wstęp i cel, 3 × plan miesiąca, 13 × plan tygodnia, 90 × strona dnia, 3 × przegląd, notatki |
| Papier środka | 100–120 g/m², offsetowy (bezdrzewny) niepowlekany, kremowy | 100 g – lżej i taniej, 120 g – pióra i markery nie przebijają. Prośba o FSC (certyfikat do wglądu) |
| Druk środka | 2 kolory (atrament #3A2340 + koral #FF6B3D jako Pantone) albo 4/4 CMYK | 2 kolory taniej i czytelniej; Pantone do ustalenia z próbnika (koral ≈ PMS Orange 021 / 172 – potwierdzić na wydruku) |
| Okładka | twarda: tektura 2–2,5 mm oklejona papierem 157 g, druk CMYK, laminat soft-touch (mat) | Opcja premium: tłoczenie na gorąco (hot foil) logotypu, lakier wybiórczy UV |
| Oprawa | szyta nićmi, **lay-flat** (otwiera się na płasko), zaokrąglone narożniki | Alternatywa tańsza: wire-O (spirala) w twardej okładce |
| Dodatki | wstążka-zakładka, gumka zamykająca, kieszeń na końcu, numeracja stron | Każdy dodatek to + koszt i + czas; do wyboru po próbkach |
| Wykończenie | wyklejka w kolorze liliowym #B9A5E3 albo różowym #FFD9E4 | Kolory marki z `NOTATKI.md` |
| Opakowanie jednostkowe | folia termokurczliwa + opaska papierowa (banderola) z opisem, danymi producenta i kodem | Banderola spełnia GPSR (rozdz. 5) |
| Opakowanie zbiorcze | karton eksportowy 5-warstwowy, 20–30 szt., ochrona narożników | Etykieta kartonu: nazwa, liczba, numer partii |
| Waga szac. | ok. 450–550 g / szt. | Ważne dla transportu i cennika Paczkomatów |

### 1.2 Kolejne produkty (po teście planera)
- **Strategia na 1 stronie – blok na biurko**: A4 (lub A3) poziomo, 50 kartek, 120 g/m², klejony u góry, podkład z tektury.
- **Dziennik decyzji**: A5, miękka okładka 300–350 g, 128 stron, 100 g/m², szyty (zeszytowo) albo klejony PUR.
- **Zestaw**: planer + blok + PDF-y „Kierunek Zysk” (PDF jako bonus cyfrowy, kod/link w środku).

---

## 2. Dostawca na Alibabie: jak wybrać i sprawdzić

**Kryteria wstępne (filtr wyszukiwania):**
- **Verified Supplier** (firma zweryfikowana przez zewnętrzną firmę audytową, np. TÜV, SGS, Bureau Veritas) –
  zobaczyć raport z weryfikacji: czy to fabryka (manufacturer), czy pośrednik (trading company).
- **Trade Assurance**: płacimy wyłącznie przez Alibabę (karta/przelew w systemie). Nigdy przelew na prywatne konto,
  „nowe konto bankowe”, PayPal poza platformą czy Western Union. W zamówieniu Trade Assurance wpisać:
  specyfikację, termin wysyłki, kryteria jakości (AQL), próbkę wzorcową jako punkt odniesienia.
- Min. 3 lata na platformie, odpowiedzi < 24 h, historia transakcji i opinie w kategorii notebooks / planners,
  zdjęcia i wideo hali (druk offsetowy, oprawa twarda w zakładzie, nie zlecana dalej).
- Certyfikaty: FSC (jeśli piszemy o papierze z odpowiedzialnych źródeł), raporty z badań farb (REACH, metale ciężkie)
  – dla papieru niskie ryzyko, ale warto mieć w dokumentacji GPSR.

**Proces (krok po kroku):**
1. RFQ (zapytanie ofertowe) po angielsku do 5–8 dostawców – przygotuję w etapie 3. Wysyła 👤 Angelika.
2. Porównanie ofert w tabeli (cena przy 300 / 500 / 1000 szt., MOQ, czas, Incoterms, koszt próbek).
3. **Próbki**: najpierw próbki z magazynu (stock samples, papier i oprawa), potem **próbka z naszym drukiem**
   (pre-production sample). Koszt zwykle 30–100 USD + kurier 30–60 USD, często odliczany od zamówienia.
4. Zatwierdzenie próbki wzorcowej (golden sample) – zdjęcia i jeden egzemplarz zostaje u nas.
5. Zamówienie przez Trade Assurance, zwykle 30% zaliczki, 70% przed wysyłką.
6. **Kontrola przed wysyłką** (pre-shipment inspection) przez niezależną firmę (np. QIMA, V-Trust, SGS):
   ok. 250–350 USD za dzień, AQL 2.5 (wady główne) / 4.0 (drobne). Przy pierwszym zamówieniu – zdecydowanie warto.
7. Spedycja, odprawa, dostawa do Lublina.

**MOQ** (minimalne zamówienie): typowo 300–500 szt. na wzór (przy druku offsetowym); przy 100–200 szt. cena
jednostkowa rośnie i znika przewaga nad Polską.

**Incoterms 2020 – co wybrać:**
| Warunek | Kto za co odpowiada | Dla nas |
|---|---|---|
| EXW | wszystko od bramy fabryki po naszej stronie (także odprawa eksportowa w Chinach) | ❌ za trudne na start |
| FOB (np. FOB Shenzhen/Ningbo) | dostawca do statku w porcie; dalej nasz spedytor | ✅ dobre z polskim spedytorem |
| DAP | dostawca dowozi pod adres, my płacimy cło i VAT (agencja celna) | ✅ dobre, jasne dokumenty |
| DDP | dostawca płaci też cło i VAT | ⚠️ wygodne, ale ryzyko zaniżonej wartości w zgłoszeniu i braku dokumentów (SAD/ZC429) na nas. Tylko z pisemną gwarancją, że importerem w zgłoszeniu jest Guliwera 4 i dostaniemy dokumenty celne |

**Transport z Chin:** lotniczy/kurier (7–12 dni, drogo), kolej (ok. 3–5 tyg.), morski LCL do Gdańska (6–8 tyg., najtaniej
przy 1–3 m³). 500 planerów A5 to ok. 250–280 kg i ok. 0,8–1 m³.

---

## 3. Szacunek kosztu jednostkowego (widełki rynkowe, nie oferta)

Założenia: 500 szt. planera A5, twarda okładka, ok. 208 stron, 2 kolory; kurs **1 USD = 3,70 zł** (do aktualizacji);
Guliwera 4 na zwolnieniu z VAT, więc **VAT z importu i z faktur polskich to koszt** (nie odliczamy).

| Pozycja | Alibaba, kolej/morze | Alibaba, lotniczo | Drukarnia PL, 500 szt. (offset) | Drukarnia PL, 100 szt. (cyfrowo) |
|---|---|---|---|---|
| Produkt | 2,2–3,5 USD → 8–13 zł | 8–13 zł | 14–22 zł netto | 28–45 zł netto |
| Transport do Polski | 0,6–1,2 USD → 2–4,5 zł | 2,5–4 USD → 9–15 zł | 0–1 zł | 0–2 zł |
| Cło (CN 4820 10, stawka 0%) 🧾 | 0 zł | 0 zł | – | – |
| VAT 23% (import albo na fakturze) | 2,3–4 zł | 4–6,5 zł | 3,2–5,1 zł | 6,4–10,4 zł |
| Agencja celna, opłaty portowe, bank (ok. 600–1200 zł / partię) | 1,2–2,4 zł | 0,6–1,2 zł | – | – |
| Próbki + inspekcja (ok. 1300–2000 zł / partię) | 2,6–4 zł | 2,6–4 zł | 0,2–0,5 zł (proof) | 1–2 zł |
| Rezerwa na wady i zwroty (5%) | 0,8–1,4 zł | 1,2–2 zł | 0,9–1,4 zł | 1,8–3 zł |
| **Koszt „na półce” w Lublinie** | **ok. 17–29 zł** | **ok. 25–42 zł** | **ok. 18–30 zł** | **ok. 37–62 zł** |

Wniosek: przy 500 szt. Alibaba drogą lądową/morską bywa tylko kilka złotych tańsza od dobrej polskiej drukarni,
a niesie ryzyko jakości, kursu, terminów i zamrożonej gotówki. Przewaga Chin rośnie od ok. 1000 szt. i przy bogatych
dodatkach (gumka, foil, pudełko).

**Koszt na zamówienie (oprócz produktu):** koperta kartonowa lub karton A5 1,5–3 zł, opłata za płatność (Stripe/P24/BLIK)
ok. 1,5–2,5% ceny, wysyłka Paczkomat ok. 12–16 zł przez brokera (np. Furgonetka, Apaczka) – doliczana klientowi.

**Cena i marża (przykład):**
| Cena dla klienta (bez VAT – zwolnienie) | Koszt na półce | Pakowanie + płatność | Zostaje (przed reklamą i podatkiem dochodowym) | Narzut |
|---|---|---|---|---|
| 89 zł | 25 zł | 4 zł | ok. 60 zł (67%) | 3,6× |
| 99 zł | 25 zł | 4,5 zł | ok. 69 zł (70%) | 4× |
| 129 zł (planer + 3 PDF-y) | 25 zł | 5 zł | ok. 99 zł (77%) | 5× |

Planery premium w Polsce kosztują zwykle 79–159 zł. Rekomendacja: **planer 89–99 zł**, zestaw z PDF-ami 129–149 zł,
dostawa płatna albo darmowa od 150 zł. Reklama: przy CPA 30–40 zł marża przy 89 zł nadal jest dodatnia, ale cienka.

---

## 4. Alibaba czy drukarnia w Polsce

| Kryterium | Alibaba (Chiny) | Drukarnia w Polsce |
|---|---|---|
| Nakład minimalny | 300–500 szt. | od 1 szt. (cyfrowo), opłacalnie od 50–100 |
| Czas od plików do towaru | 6–12 tyg. | 1–3 tyg. |
| Korekty i próbki | kurier z Chin, tygodnie | proof w kilka dni, odbiór osobiście |
| Jakość/odpowiedzialność | inspekcja konieczna, reklamacja trudna | łatwa reklamacja, polskie prawo |
| Formalności | EORI, agencja celna, dokumenty importowe | faktura |
| Gotówka zamrożona | duża (zaliczka + transport) | mała, dodruk w razie potrzeby |
| GPSR | Guliwera 4 = producent (własna marka) i importer | Guliwera 4 = producent (własna marka) |
| Ślad węglowy / „lokalnie” | gorszy | lepszy, argument marketingowy („drukowane w Polsce”) |
| Koszt przy 500 szt. | trochę niższy | trochę wyższy |
| Koszt przy 1000+ szt. | wyraźnie niższy | wyższy |

**Plan:** partia 1 (PL, 50–100 szt., grudzień 2026) → sprzedaż i opinie → partia 2 (PL offset albo Alibaba, 300–1000 szt.).

---

## 5. Zgodność z prawem (do potwierdzenia z księgową 🧾)

### 5.1 VAT, cło, podatki
- **Zwolnienie podmiotowe (art. 113 ustawy o VAT)**: limit 200 000 zł sprzedaży w roku. Planery papierowe nie są
  na liście towarów wyłączonych ze zwolnienia (art. 113 ust. 13), więc **faktury bez VAT dalej możliwe**. 🧾
- **Sprzedaż do konsumentów w innych krajach UE** (sprzedaż wysyłkowa): łączny próg 10 000 euro dla UE; prościej
  **na start wysyłać tylko w Polsce**.
- **Import z Chin**: VAT importowy 23% płacimy przy odprawie także na zwolnieniu i **nie odliczamy go** (to koszt).
  Cło na kartki/notesy/planery z działu 48 – zwykle 0% (do sprawdzenia w ISZTAR/TARIC dla konkretnego kodu). 🧾
- **Kody CN do sprawdzenia** (ISZTAR4, ewentualnie WIT – wiążąca informacja taryfowa):
  4820 10 – rejestry, notatniki, terminarze/pamiętniki (planer 90 dni, dziennik decyzji);
  4820 10 30 – notatniki, bloczki (blok „Strategia na 1 stronie”); 4910 – kalendarze drukowane (tylko gdyby planer był kalendarzem).
- **EORI**: numer dla importera, bezpłatny, wniosek przez PUESC. Potrzebne pełnomocnictwo dla agencji celnej.
- **Zakup w innym kraju UE** (np. drukarnia w Czechach): wewnątrzwspólnotowe nabycie towarów; po przekroczeniu
  50 000 zł w roku – rejestracja VAT-UE i VAT do zapłaty w Polsce (zwolnienie zostaje). 🧾
- **Kasa fiskalna**: sprzedaż wysyłkowa opłacona w całości przez bank/instytucję płatniczą (Stripe, P24, BLIK) zwykle
  jest zwolniona z kasy (rozporządzenie w sprawie zwolnień z kas, poz. „sprzedaż wysyłkowa”), pod warunkiem ewidencji
  zapłat. **Bez płatności za pobraniem** (pobranie może wymagać kasy). 🧾
- **Forma opodatkowania**: na ryczałcie handel towarami ma inną stawkę niż produkty cyfrowe; PKD w CEIDG
  (sprzedaż wysyłkowa/internetowa) do sprawdzenia. 🧾
- **KSeF**: harmonogram e-faktur dla podatników zwolnionych do potwierdzenia (faktury dla konsumentów poza KSeF). 🧾

### 5.2 GPSR – ogólne bezpieczeństwo produktów (rozporządzenie UE 2023/988, od 13.12.2024)
- Sprzedając pod własną marką, **Guliwera 4 jest „producentem”** w rozumieniu GPSR (także gdy drukuje Chiny),
  a przy imporcie także importerem. Obowiązki:
  - analiza ryzyka i **dokumentacja techniczna** (opis, materiały, papier, farby, wyniki próbek) – przechowywać 10 lat; 🔧 szablon
  - **oznakowanie** na produkcie albo banderoli/opakowaniu: nazwa i znak „Guliwera 4”, adres pocztowy, e-mail/strona,
    identyfikator produktu (nazwa + numer partii), ostrzeżenia jeśli potrzebne (np. przy gumce/wstążce – zwykle nie);
  - **na stronie produktu**: dane producenta (Guliwera 4, adres, e-mail), identyfikator, zdjęcie, ostrzeżenia;
  - procedura **zgłaszania wypadków i wycofania**, rejestr reklamacji.
- Dane chińskiego dostawcy nie muszą być publiczne, gdy producentem jest Guliwera 4 (trzymamy je w dokumentacji, poza repo).
- EAN/GTIN (GS1 Polska) nie jest wymagany w naszym sklepie; potrzebny dopiero przy Allegro, Empiku, hurcie. ❓

### 5.3 Prawa konsumenta przy produktach fizycznych
- **Odstąpienie 14 dni** od otrzymania paczki, bez podania przyczyny (art. 27 u.p.k.); zwrot pieniędzy w 14 dni,
  także najtańszej dostawy; klient płaci za odesłanie, jeśli go uprzedzimy. Możemy wstrzymać zwrot do otrzymania towaru.
- **Wyjątek**: produkt **personalizowany** (np. imię na okładce) – bez prawa odstąpienia (art. 38 ust. 1 pkt 3).
- **Folia termokurczliwa** nie odbiera prawa odstąpienia (to nie produkt higieniczny); można odliczyć zmniejszenie wartości
  przy zapisanym planerze.
- **Niezgodność z umową (dawna rękojmia)**: 2 lata, reklamacja – odpowiedź w 14 dni.
- **Zestaw fizyczny + PDF**: osobna zgoda na natychmiastowy dostęp do PDF-a dalej potrzebna.
- To samo dla przedsiębiorców na prawach konsumenta (JDG kupujące na siebie, art. 38a).
- Do zmiany: **regulamin (66)** – dostawa, koszty, czas wysyłki, odstąpienie, reklamacje, wzór formularza odstąpienia;
  **polityka prywatności (3)** – przewoźnicy jako odbiorcy danych (adres, telefon). 🔧

### 5.4 Opakowania i BDO
- Wprowadzając produkty w opakowaniach (folia, banderola, karton wysyłkowy) jesteśmy **wprowadzającym produkty
  w opakowaniach**: wpis do **BDO** (bezpłatny, przed rozpoczęciem), roczne sprawozdanie do 15 marca, ewidencja mas
  opakowań wg rodzaju (papier, folia). Przy małych masach zwykle bez obowiązku odzysku/opłaty produktowej, ale
  sprawozdanie i tak jest obowiązkowe. 🧾
- Nowe unijne rozporządzenie o opakowaniach (PPWR, stosowane od 12.08.2026): na razie bez dodatkowych obowiązków
  dla nas, oznakowanie materiałowe dojdzie w kolejnych latach; wybierać opakowania papierowe bez zbędnej pustki.
- Import z Chin: karton eksportowy i folia od dostawcy też liczą się do mas opakowań.

---

## 6. Zmiany w WooCommerce i wtyczkach (projekt, nic nie wgrywam bez zgody)

### 6.1 `guliwera-sklep-styl` → wersja 1.3 („tryb mieszany”)
- Adres w zamówieniu **tylko, gdy koszyk wymaga wysyłki** (`WC()->cart->needs_shipping()`); dla samych PDF-ów jak teraz.
  Telefon obowiązkowy przy wysyłce (Paczkomaty wymagają).
- „Tylko raz w koszyku” zostaje **tylko dla produktów do pobrania** (lista ID 67–73 + flaga `downloadable`);
  fizyczne – zwykła ilość, limit np. 10 szt.
- Okładka i oznaczenie typu produktu w koszyku: „PDF” / „Planer papierowy” / „Zestaw”.
- Pole zgody na natychmiastowe dostarczenie plików (`guliwera/zgoda-cyfrowe`) **tylko, gdy w koszyku jest PDF**.

### 6.2 WooCommerce (ustawienia)
- Produkty: typ „prosty”, nie wirtualny, waga i wymiary, **zarządzanie stanem**, próg „niski stan” (np. 10),
  „brak w magazynie” bez zamówień oczekujących, ewentualnie przedsprzedaż (❓).
- Strefa wysyłki „Polska”: InPost Paczkomat, kurier InPost/DPD, Poczta Polska (opcjonalnie); darmowa dostawa od X zł ❓.
- Integracja przewoźników: oficjalna wtyczka InPost PL albo broker (Furgonetka / Apaczka / Sendit) – mapa Paczkomatów
  w zamówieniu, etykiety z panelu, numer śledzenia. Wybór po decyzji o przewoźnikach. ❓
- Statusy: „w realizacji” → **„wysłane”** (nowy status z numerem śledzenia) → „zrealizowane”.
- Klucze API przewoźników – tylko w panelu WordPress, nigdy w repo.

### 6.3 `guliwera-maile` → wersja 1.2
- „Płatność przyjęta” dla produktów fizycznych: co zamówiono, adres, przewidywany termin nadania.
- Nowy mail **„Paczka w drodze”** z numerem i linkiem śledzenia.
- Zamówienie mieszane: PDF od razu + informacja o paczce.
- Informacja o 14 dniach na odstąpienie i link do formularza w potwierdzeniu (trwały nośnik).
- Mail do administratora: adres, metoda wysyłki, lista do spakowania.

### 6.4 `guliwera-gtm` i reklamy
- `purchase` z kosztem wysyłki (`shipping`) i kategorią produktu; osobny segment „kupił planer”.
- Strona produktu planera jako nowa strona docelowa (patrz `reklamy/AUDYT.md`, pkt 2.1).

### 6.5 Strona produktu (etap 3)
Zdjęcia (okładka, rozkładówki, w dłoni), wymiary i papier, co jest w środku, dla kogo, czas wysyłki, koszt dostawy,
zwroty 14 dni, dane producenta (GPSR), FAQ, przycisk „Kupuję”.

---

## 7. Harmonogram (wariant „planer na styczeń”, drukarnia PL)
| Kiedy | Co |
|---|---|
| do 15.11 | Odpowiedzi na pytania, decyzja o produkcie i nakładzie, wybór drukarni (3 wyceny) 👤 |
| 15–28.11 | Projekt w Canvie (środek + okładka) 🔧, przygotowanie plików do druku (spady 3 mm, CMYK/Pantone, 300 dpi) |
| do 2.12 | Proof / egzemplarz próbny, poprawki |
| 2–12.12 | Druk nakładu próbnego; równolegle wtyczki 1.3/1.2 na stronie testowej, regulamin, BDO, przewoźnik |
| ok. 12.12 | Towar w Lublinie, zdjęcia, start sprzedaży (prezent świąteczny + „start roku”) |
| styczeń | Sprzedaż „90 dni od stycznia”, zbieranie opinii |
| luty–marzec | Decyzja o partii 2; jeśli Alibaba – RFQ, próbki (po Chińskim Nowym Roku), produkcja na kwiecień–maj |

Wariant Alibaba na styczeń: realny tylko przy decyzji i plikach w ciągu kilku dni, próbce z magazynu bez druku własnego
i transporcie lotniczym – wysokie ryzyko, nie polecam.
