# Audyt gotowości do płatnych reklam (Meta Ads, Google Ads)

Stan na 6.11.2026. Źródła: kod `wtyczki/guliwera-gtm/` (v2.0), strony WordPress (lista i treść polityki prywatności),
lista aktywnych wtyczek, statystyki Jetpack. Kontener nie widzi guliwera4.pl, więc tagów na żywo nie sprawdzałam:
punkty „do weryfikacji” wymagają testu w trybie podglądu GTM.

Legenda: ✅ jest · 🔧 do zrobienia (mogę przygotować) · 👤 do zrobienia przez Angelikę (konto, panel, płatność) · ❓ decyzja Angeliki

## Punkt wyjścia
- Ruch: 118 odsłon / 13 osób w tygodniu 2–6.11 (wcześniej 0). Brak historii konwersji, brak list remarketingowych.
  Wniosek: przez pierwsze tygodnie algorytmy (Smart Bidding, optymalizacja Meta pod zakup) nie mają na czym się uczyć.
- Sklep działa od ok. 4.11.2026. Ważne dla Black Friday (patrz „Prawo”).

## 1. Pomiar
| # | Element | Stan | Co dalej |
|---|---|---|---|
| 1.1 | GTM w wtyczce | ✅ | Zmiana na GTM-KK3LPNV6 (konto Google „Guliwera4”), wtyczka 2.0.1. |
| 1.2 | Tag Google GA4 (G-…) w GTM | 👤 brak | Założyć usługę GA4, dodać tag, opublikować. Blokuje wszystko poniżej. |
| 1.3 | Zdarzenia e-commerce w dataLayer (view_item, add_to_cart, begin_checkout, purchase) | ✅ / do weryfikacji | Test w podglądzie GTM; sprawdzić, czy W3 Total Cache nie podaje z cache stron z `add_to_cart`. |
| 1.4 | `purchase` tylko po opłaceniu | 🔧 gotowe w 2.1 (niewgrane) | Teraz wysyła się na stronie podziękowania niezależnie od statusu (BLIK/P24 mogą wrócić jako „oczekujące”). Warunek: status w realizacji/zrealizowane. |
| 1.5 | Zdarzenie rejestracji w Akademii (`sign_up`) | 🔧 gotowe w 2.1 (niewgrane) | Dodać w wtyczce (rejestracja na Moje konto + pierwsze wejście do /akademia/). To główna konwersja celu „Akademia”. |
| 1.6 | Zdarzenie zapytania o usługi (`generate_lead`) | 🔧 gotowe w 2.1 (niewgrane) | Formularz Jetpack na /kontakt/ + kliknięcia w e-mail. Aktywne są też Contact Form 7 i WPForms – ustalić, który formularz zostaje. |
| 1.7 | Consent Mode v2 – ustawienia domyślne „denied” | ✅ | – |
| 1.8 | Zgoda marketingowa (ad_storage, ad_user_data, ad_personalization) | 🔧 gotowe w guliwera-gtm 2.1 (niewgrane) | Baner ma jedną kategorię (analityka). Po „Akceptuję” włącza tylko `analytics_storage`. Bez tego Google Ads i Meta nie mogą mierzyć ani budować list. Potrzebny baner z kategoriami: Niezbędne / Analityczne / Marketingowe (+ „Odrzucam wszystko” równie widoczne). |
| 1.9 | Tryb zgody: podstawowy vs zaawansowany | ❓ | Teraz podstawowy (GTM ładuje się dopiero po zgodzie). Rekomendacja: zostać przy podstawowym – przy naszej skali modelowanie Google i tak się nie włączy, a ryzyko prawne mniejsze. |
| 1.10 | Piksel Meta (przez GTM, tylko po zgodzie marketingowej) | 🔧/👤 | Potrzebny Business Manager + zbiór danych (piksel). Zdarzenia: PageView, ViewContent, AddToCart, InitiateCheckout, Purchase, CompleteRegistration, Lead. |
| 1.11 | Meta Conversions API (z serwera) | 🔧/❓ | Wtyczka wysyła Purchase/CompleteRegistration z serwera, z `event_id` do deduplikacji, hash e-maila. Tylko dla osób ze zgodą marketingową (zapis zgody w zamówieniu). Token CAPI – tylko w panelu WP, nigdy w repo. |
| 1.12 | Weryfikacja domeny w Meta | 👤 | Rekord TXT w DNS w nazwa.pl. |
| 1.13 | Konwersje Google Ads + ulepszone konwersje | 🔧/👤 | Konto Google Ads, połączenie z GA4, import `purchase` (główna), `sign_up`, `generate_lead`. Ulepszone konwersje: hash e-maila w `purchase` (tylko przy ad_user_data = granted). Automatyczne tagowanie (gclid) włączone. |
| 1.14 | Konwencja UTM | 🔧 | Ustalona w planie (utm_source / medium=cpc|paid_social / campaign=kod_kampanii / content=wariant). Meta: parametry URL na poziomie reklamy. |
| 1.15 | Mailchimp jako kanał | ✅ (osobny czat) | Synchronizacja segmentów (kupujący, Akademia) z Meta Custom Audiences / Google Customer Match – tylko przy zgodzie i informacji w polityce. Dzięki temu nie płacimy za reklamy dla osób, które już kupiły. |

## 2. Strony docelowe
| # | Strona | Stan | Co poprawić pod konwersję |
|---|---|---|---|
| 2.1 | /materialy/ (wszystkie produkty na jednej stronie) | ✅ działa | Do reklam za ogólna. Potrzebne osobne strony: Pakiet (297 zł), Przewodnik (147 zł) i „produkt wejściowy” za 39 zł. Każda: 1 obietnica, co jest w środku (podgląd 2–3 stron), dla kogo / nie dla kogo, cena końcowa, przycisk „Kupuję” = `/zamowienie/?add-to-cart=ID`, FAQ (format, jak dostanę plik, faktura, brak prawa odstąpienia po pobraniu – za zgodą). |
| 2.2 | Dowód społeczny | ❓ | Opinie klientów / wyniki z konsultacji? Bez nich reklama zimna konwertuje gorzej. |
| 2.3 | /aplikacja/ (112) → rejestracja → /akademia/ | ✅ | Strona docelowa dla kampanii Akademii: krótszy formularz (e-mail + hasło lub Google), „0 zł, bez karty”, zrzuty ekranu aplikacji, korzyść „do 30% rabatu na materiały”. Logowanie Google (Nextend) nieskonfigurowane – warto dokończyć, mocno podnosi % rejestracji. |
| 2.4 | /uslugi/ i /kontakt/ | ✅ | Dla kampanii lokalnej (Lublin): osobna sekcja/strona „Konsultacja strategiczna w Lublinie”, jeden formularz z 3–4 polami, numer telefonu (jeśli chcesz), czas odpowiedzi. |
| 2.5 | Szybkość i mobile | do weryfikacji | Każda strona ładuje Google Fonts i duży inline CSS; test PageSpeed na telefonie przed startem. |
| 2.6 | Strona „Elementor #28” (duplikat, publiczna) | 🔧 | Usunąć lub ukryć, żeby nie trafiała do reklam dynamicznych i indeksu. |
| 2.7 | Pasek „Nowość…” na górze | ✅ | Na czas Black Friday zmienić na komunikat promocji (z najniższą ceną). |

## 3. Prawo i zasady platform
| # | Element | Stan | Co dalej |
|---|---|---|---|
| 3.1 | Polityka prywatności (3) | ✅ dla analityki | Brakuje: cookies marketingowe (_fbp, _fbc, _gcl_au, _gcl_aw), odbiorcy Meta Platforms Ireland i Google Ads, remarketing i listy klientów (Custom Audiences / Customer Match), Mailchimp (Intuit), podstawa: zgoda. Tekst banera też musi wymieniać reklamy. |
| 3.2 | Regulamin (66) | ✅ opublikowany | Przed BF: zapis o promocjach i kuponach (czy łączą się z rabatem Akademii – teraz nie). |
| 3.3 | Najniższa cena z 30 dni (Omnibus, art. 6a ustawy o informowaniu o cenach) | ❓ ważne | Produkty są w sprzedaży od ok. 4.11. Przy promocji przed upływem 30 dni podajemy najniższą cenę od początku sprzedaży. Obniżka liczona od ceny, która faktycznie obowiązywała. Kupony Akademii też są obniżką ceny – warto pokazywać tę informację w koszyku. |
| 3.4 | Ceny końcowe | ✅ | Bez VAT (zwolnienie) – w reklamach podajemy cenę końcową „39 zł”, bez „netto”. |
| 3.5 | Zasady Meta i Google dla treści o zarabianiu | ❓ | „Kierunek Zysk” to nazwa produktu – w tekstach bez obietnic dochodu („zarobisz X zł”), bez „szybko i bez wysiłku”, bez zwracania się do cech osobistych („Masz długi?”). Obietnice typu „uporządkujesz strategię na 1 stronie”. |
| 3.6 | Weryfikacja reklamodawcy (Google), dane firmy na stronie | ✅ / 👤 | Dane firmy w stopce są. Weryfikację tożsamości w Google Ads przechodzi Angelika. |
| 3.7 | Treści i grafiki | ✅ | Własne zdjęcia, kolory i fonty marki, bez cudzych logotypów. |
