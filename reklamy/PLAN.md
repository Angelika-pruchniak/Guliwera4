# Plan kampanii v2 (po drugiej opinii Gemini, 8.11.2026)

Budżet: **1000 zł/mies.** łącznie. Priorytet: 1) sprzedaż PDF, 2) społeczność w Akademii. Szkolenia – później.
Źródła: mój plan v1 (czat), brief `gemini/01-strategia-brief.md`, odpowiedź `gemini/01-strategia-odpowiedz-gemini.md`.

## Ocena opinii Gemini
| Uwaga Gemini | Ocena | Decyzja |
|---|---|---|
| 4 kampanie przy 33 zł/dzień to rozdrobnienie | ✅ trafne | Maks. 2 kampanie |
| Osobny retargeting nie ma sensu przy ~50 osobach/mies. (min. 100 w Meta) | ✅ trafne | Bez kampanii retargetingowej; Advantage+ i tak pokazuje reklamy ciepłym osobom; dogrzewanie mailem (Mailchimp, osobny czat) |
| Optymalizacja na zakup od pierwszego dnia, nie na dodanie do koszyka | ✅ przyjmuję z bezpiecznikiem | Purchase; jeśli 0 zakupów po wydaniu 150 zł → InitiateCheckout na 2 tygodnie |
| 100% Meta, Google wcale | ⚠️ częściowo | Listopad–grudzień 100% Meta. W styczniu test Google Search (dopasowanie ścisłe, „planer 2027”, „plan roczny firmy”) ok. 250 zł, jeśli Meta da ≥5 zakupów/mies. |
| PMax odłożyć (do Q2 2027) | ✅ zgoda | PMax po ≥50 konwersjach/mies. |
| Formularze błyskawiczne Meta + Zapier dla Akademii | ❌ odrzucam | Akademia wymaga konta w aplikacji; lead bez konta = brak nauki i rabatu, dodatkowy koszt Zapiera. Zostaje konwersja na stronie `sign_up` |
| Tryb podstawowy zgody gubi 30–40% danych | ⚠️ liczba bez źródła | Ale kierunek słuszny: Conversions API przyspieszam na grudzień |
| Cyber Monday 1.12 | ❌ błąd | Cyber Monday 2026 = 30.11 |
| „Najniższa cena z 23 dni” | ⚠️ | Lepiej: „Najniższa cena od początku sprzedaży (4.11.2026): 297 zł” |
| Nagłówki | ⚠️ część niezgodna | Odrzucone: „Odbierz rabat do -30%” (rabat zależy od regularnej nauki), „Rozwiń firmę za 0 zł”, „Ogarnij biznes i zysk” (obietnica wyniku), „Strategia firmy w 1 dzień” (nieweryfikowalne), „Dołącz do Akademii PDF” (bez sensu). Do wykorzystania po korekcie: „5 minut nauki dziennie”, „Koniec chaosu w firmie”, „Planer 90 dni dla firmy”, „Pakiet Kierunek Zysk”, „Mikrolekcje 5 min dziennie” |

## Struktura (Meta Ads, od 17.11.2026)
| Kampania | Cel / optymalizacja | Budżet | Odbiorcy | Reklamy |
|---|---|---|---|---|
| **K1 Akademia** | Sprzedaż → zdarzenie CompleteRegistration (`sign_up`) | 13 zł/dzień (~400 zł/mies.) | Advantage+, Polska, 25–55, wykluczeni zarejestrowani | Lilek, „5 minut dziennie, 0 zł”, „rabat do 30% na materiały za regularną naukę” |
| **K2 Sprzedaż PDF** | Sprzedaż → Purchase | 20 zł/dzień (~600 zł/mies.) | Advantage+, Polska (sugestie: małe firmy, zarządzanie) | A: Strategia na 1 stronie 39 zł; B: Pakiet 297 zł |

Listopad (17–30.11, 14 dni) ≈ 466 zł.

## Black Week: Pakiet −30% (do potwierdzenia: 24.11–30.11)
- Cena 207,90 zł (z 297 zł). Komunikat ceny: „Najniższa cena od początku sprzedaży (4.11.2026): 297 zł” – w reklamie z ceną promocyjną i na stronie produktu.
- Bez nowej kampanii: w K2 dodajemy reklamy BF i pauzujemy zwykłe reklamy Pakietu na czas promocji.
- Komunikat bez obietnic dochodu: porządek, czas, jasny plan („Uporządkuj chaos w firmie 30% taniej, do 30.11”).
- Kupony Akademii nie łączą się z promocją (zgodnie z obecnym regulaminem kuponu).

## KPI (start, do korekty po 2 tygodniach)
| KPI | Cel |
|---|---|
| Koszt rejestracji w Akademii | ≤ 8 zł (≈ 50 rejestracji/mies.) |
| Koszt zakupu (K2) | ≤ 60 zł |
| ROAS K2 | ≥ 1,5 (listopad), ≥ 2 (od stycznia) |
| CTR (link) Meta | ≥ 1% |
| Konwersja strony produktu | ≥ 2% |

## Kolejność wdrożenia
1. ✅ guliwera-gtm 2.1 (zgoda marketingowa, sign_up, generate_lead, purchase po opłaceniu) – zainstalowana 8.11.2026.
2. GTM: tag GA4 + zdarzenia, piksel Meta (tylko przy zgodzie ad_storage) – import kontenera.
3. Polityka prywatności: cookies marketingowe, Meta, Google Ads, Mailchimp.
4. Strony docelowe: Pakiet (z ceną BF), Strategia na 1 stronie, /aplikacja/.
5. Kreacje (Canva) + teksty → druga opinia Gemini → akceptacja Angeliki → start 17.11.
6. Grudzień: Conversions API. Styczeń: decyzja o Google Search.
