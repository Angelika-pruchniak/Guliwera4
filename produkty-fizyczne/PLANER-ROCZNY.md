# Planer roczny Q1–Q4 (Guliwera 4 · Kierunek Zysk)

Decyzje z 8.11.2026: planer roczny w układzie kwartałów jak w firmie (Q1–Q4), **niedatowany**, z krótkimi lekcjami
i narzędziami dla osób z ADHD i „głów z wieloma otwartymi kartami”. Miękka okładka, wygląd do korporacji.
Nakład próbny **50 szt.**, druk w Polsce, sprzedaż **przed świętami**, magazyn i pakowanie w domu w Lublinie,
wysyłka **InPost**, tylko Polska, dostawa płatna. Bez personalizacji.

Repozytorium jest publiczne: tu jest **struktura i specyfikacja**. Pełne teksty lekcji (treść płatna) trzymamy prywatnie
(Google Drive / Canva).

---

## 0. Zasada nadrzędna (8.11.2026, wymóg Angeliki – zawsze)
Planer to **bank wiedzy i wizytówka Angeliki jako ekspertki**, a nie zwykły kalendarz:
- **identyfikacja Guliwera 4 na każdej stronie**: logo G4 (`logo/mark.svg`), paleta „liliputy”, Fraunces / DM Sans / Caveat,
  hasło „strategia, produkty cyfrowe i dobry humor”, styl okładek „Kierunek Zysk” (pigułka granatowa, krem, koła w tle,
  żółte podkreślenie jak na banerze LinkedIn);
- **treść oparta na wartościowej wiedzy**: techniki konsultingu strategicznego (MECE, drzewo problemów, piramida Minto, 80/20,
  macierz BCG, model 7S, SWOT, 5 sił, OKR) i **narzędzia ekonomistów** (koszt alternatywny, koszt utopiony, analiza krańcowa,
  elastyczność, próg rentowności, ekonomia jednostkowa, cash flow, ROI, NPV); spójnie z Akademią Guliwera i PDF-ami;
- autorka na okładce i w środku, jej przekaz („Duże strategie zamieniam w konkretną trasę”);
- techniki opisujemy z autorami (np. „opracowana w Boston Consulting Group”, „Barbara Minto”), ale **bez cudzych logo
  i bez nazw firm konsultingowych jako hasła sprzedażowego** (np. nie „metoda McKinsey” na okładce) – znaki towarowe;
- elementy przyjazne ADHD są „podwoziem”: krótko, 1 rzecz najważniejsza, zrzut z głowy, kolor kwartału.

Podgląd wyglądu: `planer/generuj.js` (okładka przód i tył, otwarcie Q1, lekcja BCG z ćwiczeniem, rozkładówka tygodnia).

## 1. Nazwa
Robocza: **„Rok w czterech kwartałach” · Kierunek Zysk · planer roczny** (❓ do akceptacji).

## 2. Zasady projektu „przyjazne ADHD” (bez obietnic medycznych)
- **Jedna rzecz najważniejsza** na każdym poziomie: 1 cel kwartału (OKR), 3 rezultaty, Top 3 tygodnia (80/20), 1 rzecz dziennie.
- **Zewnętrzna pamięć**: „zrzut z głowy” i parking „nie teraz” na każdej rozkładówce tygodnia.
- **Kolor = kwartał**: znacznik na krawędzi stron – Q1 koral, Q2 żółty, Q3 liliowy, Q4 róż.
- **Niedatowany**: zaczynasz w dowolnym miesiącu, opuszczony tydzień nie „marnuje” stron.
- **Lekcja = 1 strona wiedzy + 1 strona ćwiczenia (10 min)**, duże pola, kropki zamiast linii.
- Wstęp: „Planer nie zastępuje diagnozy ani wsparcia specjalisty.” W reklamach nie zwracamy się do cech zdrowotnych.

## 3. Struktura (A5, 220 stron)
| Część | Strony | Zawartość |
|---|---|---|
| Wstęp | 12 | tytułowa, o autorce, jak korzystać w 2 minuty, mój rok na 1 stronie, mapa roku Q1–Q4, przegląd 12 miesięcy |
| Każdy kwartał (×4) | 46 | otwarcie w kolorze (2), 4 lekcje × 2 strony (8), plan kwartału OKR (2), 3 × miesiąc (6), 13 × tydzień (26), przegląd kwartału jak w zarządzie (2) |
| Bank wiedzy | 8 | słownik 40 pojęć biznesu, ściągawka wzorów (marża, próg rentowności, LTV, ROI, NPV), 10 pytań konsultanta, mapa: które narzędzie do jakiego problemu |
| Koniec | 16 | podsumowanie roku (2), plan awaryjny (2), notatki (10), dane producenta (2) |
| **Razem** | **220** | 12 + 4 × 46 + 8 + 16 |

Każda rozkładówka tygodnia ma wbudowane narzędzia: Top 3 wg zasady 80/20, „liczba tygodnia (KPI) vs cel kwartału”,
„decyzja jak ekonomista: czego nie zrobię (koszt alternatywny)”. Przegląd kwartału: liczby, co zadziałało, „Czego mnie
to nauczyło?”, decyzja kontynuuję / zmieniam / kończę (koszt utopiony).

## 4. Lekcje (16; pełne teksty poza repo)
Każda: tytuł + hasło (Caveat), skąd technika, 3–5 zdań wiedzy, 4-polowy schemat, przykład z małej firmy, „dla rozproszonej głowy”,
„pytanie konsultanta”, „typowy błąd”, odnośnik do Akademii / PDF; po prawej ćwiczenie z szablonem.

| Q | Motyw | Lekcje | Akademia / PDF |
|---|---|---|---|
| Q1 | **Kierunek** – strategia | 1. SWOT i 5 sił Portera · 2. Macierz BCG · 3. Strategia na 1 stronie i piramida Minto · 4. OKR: 1 cel, 3 rezultaty, 13 tygodni | u6, u5l3, u7l2 · PDF Strategia na 1 stronie |
| Q2 | **Liczby** – ekonomia firmy | 5. Przychód, koszt, zysk, marża (i EBITDA) · 6. Próg rentowności · 7. Ekonomia jednostkowa: AOV, CAC, LTV · 8. Cash flow: zysk to nie gotówka | u1, u2, u3l3, u4l3 · PDF Audyt |
| Q3 | **Problemy** – myśl jak konsultant | 9. MECE · 10. Drzewo problemów i hipoteza · 11. Zasada 80/20 · 12. Model 7S: czy firma/zespół gra razem | u5, u6l3 · PDF Przewodnik |
| Q4 | **Decyzje** – ekonomia decyzji | 13. Koszt alternatywny · 14. Koszt utopiony: kiedy zamknąć projekt · 15. Analiza krańcowa i elastyczność cenowa · 16. ROI, NPV i plan na kolejny rok | u3l2, u8 · PDF Dziennik decyzji |

## 5. Specyfikacja dla drukarni (do zapytania ofertowego)
| Cecha | Wariant A (zalecany) | Wariant B (taniej) |
|---|---|---|
| Nakład | 50 szt. (cena też dla 30 i 100 szt.) | jw. |
| Format netto | A5, 148 × 210 mm, pion | jw. |
| Objętość | 220 stron środka (110 kartek) | jw. |
| Papier środka | offset 100 g/m² biały lub kremowy (np. Munken / Amber) | offset 90 g/m² biały |
| Druk środka | cyfrowy 4/4 (kolorowe znaczniki kwartałów na krawędzi) | 1/1 czarny + 4/4 tylko otwarcia kwartałów (16 stron) |
| Okładka | karton 350 g/m² kreda matowa, druk 4/0, folia **soft-touch** na zewnątrz | 300 g/m², folia matowa |
| Uszlachetnienie | lakier wybiórczy UV 3D na tytule (cyfrowo, jeśli jest) | brak |
| Skrzydełka okładki | tak, 80 mm (wygląda premium, służą jako zakładki) | nie |
| Oprawa | klejona **PUR** (mocna, lepiej się otwiera) | klejona PUR |
| Narożniki | zaokrąglone (jeśli możliwe) | proste |
| Pakowanie | każdy egzemplarz w folii, karton zbiorczy | karton zbiorczy |
| Proof | 1 egzemplarz próbny przed nakładem | PDF + wydruk okładki |
| Pliki | PDF/X-4, spady 3 mm, CMYK, 300 dpi, czcionki osadzone | jw. |
| Termin | pliki do 24.11, proof do 28.11, nakład w Lublinie do 5.12.2026 | jw. |

Waga 1 szt.: ok. 360–400 g → mieści się w gabarycie A Paczkomatu w kopercie kartonowej.

## 6. Koszt i cena (szacunek, przed wycenami)
| Pozycja | Wariant A | Wariant B |
|---|---|---|
| Druk 1 szt. (brutto, VAT 23% to dla nas koszt) | 40–60 zł | 22–35 zł |
| Proof, transport z drukarni (na 50 szt.) | 2–4 zł | 2–4 zł |
| Opakowanie wysyłkowe | 1,5–3 zł | 1,5–3 zł |
| Płatność (Stripe/P24/BLIK ~2%) | 2,5–3 zł | 2,5–3 zł |
| Rezerwa na wady (5%) | 2–3 zł | 1–2 zł |
| **Razem na sztukę** | **ok. 48–73 zł** | **ok. 29–47 zł** |
| **Budżet partii 50 szt.** | **ok. 2 400–3 600 zł** | **ok. 1 500–2 400 zł** |

Proponowana cena: **129 zł** (wariant A) albo 109 zł (wariant B); dostawa InPost Paczkomat ok. 14,99 zł płatna przez
klienta. Przy 129 zł i koszcie ok. 60 zł zostaje ok. 69 zł na sztukę przed podatkiem (ryczałt) i reklamą.
Planery roczne premium w Polsce: zwykle 89–169 zł.

## 6a. Budżet i KPI partii (do podpięcia pod „macierz zysków” – KPI na koniec 2026 i na 2027)
Każdy element budżetu jest parametrem; po wycenach z drukarni podmieniamy liczby i wszystko przelicza się wzorami.
Wartości poniżej to założenia (widełki), nie oferty.

| Parametr | Symbol | Wariant A | Wariant B |
|---|---|---|---|
| Nakład | N | 50 | 50 |
| Druk 1 szt. brutto | d | 55 zł | 35 zł |
| Proof + transport z drukarni (partia) | P | 150 zł | 150 zł |
| Opakowanie wysyłkowe 1 szt. | o | 2,5 zł | 2,5 zł |
| Opłata za płatność | p% | 2% | 2% |
| Cena (bez VAT – zwolnienie) | C | 129 zł | 109 zł |
| Ryczałt od przychodu (handel, 🧾) | r% | 3% | 3% |
| Reklama na 1 sprzedaż (CAC) | CAC | 30 zł | 30 zł |

Wzory:
- koszt partii **K = N × d + P** → A: 2 900 zł · B: 1 900 zł
- marża na sztuce (przed reklamą) **m = C × (1 − p% − r%) − o** → A: 120,05 zł · B: 101,05 zł
- **próg rentowności partii (szt.) = K / m** → A: 25 szt. · B: 19 szt. (z reklamą: K / (m − CAC) → A: 33 · B: 27)
- zysk przy sprzedaży całej partii **Z = N × (m − CAC) − K** → A: 1 603 zł · B: 1 653 zł
- **ROI partii = Z / K** → A: 55% · B: 87%

KPI do macierzy (na koniec 2026 i miesięcznie w 2027): sprzedane sztuki, przychód, marża na sztuce, zysk partii,
ROI partii, CAC i konwersja ze strony produktu, % zwrotów/reklamacji, sprzedaż krzyżowa (planer → PDF / Akademia),
stan magazynu (dni zapasu).

## 7. Treść na banderolę / ostatnią stronę (GPSR)
Producent: Guliwera 4 Angelika Pruchniak-Gąszczyk, ul. Guliwera 4, Lublin, kontakt@guliwera4.pl, guliwera4.pl ·
Produkt: Planer roczny Q1–Q4, wersja 1, partia 2026-12 · Wydrukowano w Polsce · Papier: offset 100 g/m².
(Pełny kod pocztowy dopisać przy składzie.)
