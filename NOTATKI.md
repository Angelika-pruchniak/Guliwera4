# Guliwera 4: notatki z prac nad stroną guliwera4.pl

Właścicielka: **Angelika Pruchniak-Gąszczyk** (Guliwera 4, ul. Guliwera 4, Lublin, NIP 9462713420)
LinkedIn firmy: https://www.linkedin.com/company/guliwera-4/
E-mail na stronie: kontakt@guliwera4.pl

## Marka (paleta v3 „liliputy”)
- Kolory: krem #FFF6EE, koral #FF6B3D, koral ciemny #E8512A, róż #F4799F, róż jasny #FFD9E4,
  liliowy #B9A5E3, liliowy jasny #EDE5FB, żółty #FFC84A, żółty jasny #FFEFC7, brzoskwinia #FFD8C2,
  atrament #3A2340, atrament 60% #7A6580, granat #1E2A4A.
- Fonty: Fraunces (nagłówki), DM Sans (tekst), Caveat (odręczne wstawki).
- Hasło: „strategia, produkty cyfrowe i dobry humor”.
- Logo i favicon: folder `logo/` (ikona-512.png, logo-poziome.png, mark.svg, avatar i baner LinkedIn).
- Zdjęcie: `zdjecia/angelika-portret.jpg`. Tylko kadr i wyostrzenie, bez zmiany rysów twarzy (wyraźna prośba).

## Strona (WordPress + Jetpack, site ID 258438467)
- Strony: 10 Strona główna, 32 Usługi, 6 O mnie, 52 Lokalizacja, 60 Kontakt (formularz Jetpack), 74 Materiały.
  Treść to bloki `wp:html` z szablonem `elementor_canvas` (bez Elementora, żeby uniknąć starego cache).
- WooCommerce: 62 Sklep (przekierowuje na /materialy/), 63 Koszyk, 64 Zamówienie (/zamowienie/), 65 Moje konto.
- Produkty „Kierunek Zysk” (cyfrowe, każdy tylko raz w koszyku):
  - 67 Pakiet (297 zł, ZIP)
  - 68 Przewodnik (147 zł)
  - 69 Strategia na 1 stronie (39 zł)
  - 70 Planer 90 dni (59 zł)
  - 71 Skrypty (79 zł)
  - 72 Audyt (59 zł)
  - 73 Dziennik decyzji (49 zł)
  - Link zakupu: `/zamowienie/?add-to-cart=ID`.

## Wtyczki własne (aktywne, zostawić)
- `guliwera-favicon`: ikona strony (media 96).
- `guliwera-gtm`: Google Tag Manager GTM-5JKVC955 + zdarzenia e-commerce GA4 (view_item, add_to_cart, begin_checkout, purchase).
- `guliwera-sklep-styl` (kod w `wtyczki/`):
  - produkty, koszyk, zamówienie i konto w wyglądzie strony;
  - Polska, ceny w formacie „39,00 zł”;
  - zamówienie bez adresu;
  - PDF tylko raz w koszyku.
- Instalacja wtyczek: ZIP z GitHub raw tego repozytorium (adres z hashem commita), bo bezpośredni upload jest blokowany.

## Do zrobienia
- [ ] Angelika: połączyć konto Stripe (WooCommerce → Ustawienia → Płatności → Stripe) i włączyć Kartę, BLIK, Przelewy24. Potem zakup testowy.
- [ ] Angelika: w Tag Managerze dodać „Tag Google” z identyfikatorem GA4 (G-…) i opublikować kontener.
- Repozytorium: github.com/Angelika-pruchniak/guliwera4 (publiczne: tylko kod, logo, notatki; bez płatnych PDF-ów i bez danych firm trzecich).
- [ ] Opcjonalnie:
  - polityka prywatności i regulamin sklepu (strona 3 i 66 to szkice);
  - usunąć duplikat strony 28;
  - okładki produktów jako obrazki w koszyku;
  - PDF „budżet marketingowy”.

## Aplikacja Akademia Guliwera (gałąź `claude/aplikacja-lekcje`)
- Kod: `aplikacja/` (React + Vite, PWA). Treść lekcji: `aplikacja/src/content/kurs.ts`. Maskotka: Lilek.
- Na stronie: wtyczka `guliwera-akademia` (wersja 1.1, aktywna) serwuje aplikację pod https://guliwera4.pl/akademia/.
  Aktualizacja: `aplikacja/pakuj-wtyczke.sh` → commit `wtyczki/guliwera-akademia.zip` → wgrać ZIP z GitHub raw (hash commita, overwrite).
- Strona 112 „Aplikacja” (https://guliwera4.pl/aplikacja/): opis, instrukcja instalacji, kod QR. W menu pozostałych stron jeszcze nie ma linku.
- Sklepy: Google Play przez TWA (PWABuilder), wymaga konta Google Play Console i pliku /.well-known/assetlinks.json; App Store wymaga konta Apple Developer i Maca.

## Następny projekt
Aplikacja do nauki dla młodych managerów: krótkie codzienne lekcje, seria dni, punkty, poziomy.
Wiedza z publicznie opisanych frameworków strategii i zarządzania (np. macierz BCG, 7S, MECE, piramida Minto).
Oryginalna marka Guliwera 4, bez logo i materiałów chronionych prawem autorskim.

## Strategia 2027 – decyzja z 8.11.2026 (gałąź `claude/strategia`, plik `strategia/STRATEGIA-I-KRYTYKA.md`)
- Dźwignia zysku: **płatne produkty cyfrowe na stronie**, nie szkolenia. Czas Angeliki: 2 soboty/mies. + 2–3 spotkania 1:1 po 1 h tygodniowo.
- **Drabina produktów (zaakceptowana):** Akademia + newsletter (0 zł, zapis na listę) → Strategia na 1 stronie 39 zł → szablony/PDF 49–99 zł →
  Pakiet 297 zł → kurs wideo 697 zł (nagranie I–III 2027, premiera IV) → Klub 49 zł/mies. (od V) → warsztat sobotni online 249 zł/os.
  i sesje 1:1 1 200 zł (max 10/mies.) → licencje B2B dla zespołów.
- KPI nr 1: nowe zapisy na listę miesięcznie. Model z wzorami: `strategia/model-szablon-2027.xlsx` (prawdziwe liczby tylko w prywatnej kopii).
- Wstrzymane: planer papierowy, aplikacja w sklepach, kurs na żywo 6 tygodni, warsztaty B2B w dni robocze. 🧾 VAT dla sesji 1:1 do rozstrzygnięcia.
