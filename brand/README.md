# Guliwera 4 · paczka marki dla grafika

Wszystko, czego potrzebujesz, żeby projektować dla marki Guliwera 4 (guliwera4.pl) bez zgadywania.
**Najpierw przeczytaj brandbook:** `brandbook/guliwera4-brandbook.pdf` (albo `brandbook/index.html` w przeglądarce).

Pytania i akceptacja nowych elementów: kontakt@guliwera4.pl

## Co jest w folderze

| Folder | Zawartość | Kiedy używać |
|---|---|---|
| `brandbook/` | Brandbook (HTML + PDF), obrazy do brandbooka | Zasady marki |
| `logo/svg/` | Znak G4 (gradient, atrament, granat, biały, koral), logo poziome i krótkie (jasne / ciemne tło), logo osobiste, ikona, awatary. Tekst zamieniony na krzywe | Każdy projekt, druk, edycja w Figmie / Illustratorze |
| `logo/png/` | Te same wersje w PNG z przezroczystością (500–2000 px) | Canva, PowerPoint, Word |
| `logo/favicon/` | favicon.ico, favicon.svg, PNG 16–512, apple-touch-icon-180 | Strony i aplikacje |
| `kolory/` | `kolory.css`, `kolory.json`, `guliwera4-paleta.ase` (Adobe), `kolory.gpl` (GIMP, Inkscape, Krita), `kontrasty.md` | Wgranie palety do programu |
| `szablony/` | 23 szablony HTML do edycji + `png/` (podgląd w dokładnym wymiarze) + `png/siatka/` (podgląd z siatką) | Social media, reklamy, prezentacje, okładka PDF |
| `szablony/email/` | Podpis e-mail do skopiowania | Gmail, Outlook |
| `lilek/` | Maskotka Lilek w 6 nastrojach (SVG + PNG 800 px) | Tylko Akademia i darmowe treści |
| `okladki/` | Wizualizacje okładek „Kierunek Zysk” 1200 × 1200 | Sklep, posty produktowe |
| `zdjecia/` | Portret Angeliki w jasnym garniturze | Bez zmian rysów twarzy, bez filtrów |
| `narzedzia/` | Skrypty generujące logo, paletę, Lilka, PNG i PDF | Tylko gdy zmieniasz źródła |

## Szybki start: grafika do social media

1. Zainstaluj fonty z Google Fonts: [Fraunces](https://fonts.google.com/specimen/Fraunces), [DM Sans](https://fonts.google.com/specimen/DM+Sans), [Caveat](https://fonts.google.com/specimen/Caveat) (licencja SIL OFL 1.1, darmowe komercyjnie). Szablony HTML ładują je same z internetu.
2. Skopiuj szablon, np. `szablony/ig-1080x1350-porada.html` → `szablony/2026-11-12_ig-porada_marza.html`.
3. Otwórz go w Chrome. **Kliknij tekst i pisz.** Klawisz **G** pokazuje siatkę (6 kolumn, margines) i zakreskowane strefy, których nie zasłaniaj.
   Zmiany w przeglądarce nie zapisują się same: większe zmiany (kolor tła, obraz) rób w pliku HTML w edytorze tekstu.
4. Eksport do PNG w dokładnym wymiarze (wymaga Node.js i Playwright):
   ```bash
   npm i -g playwright && npx playwright install chromium   # jednorazowo
   node brand/narzedzia/eksport-szablonow.js 2026-11-12_ig-porada   # z katalogu repozytorium
   ```
   Wynik: `brand/szablony/png/<nazwa>.png`. Bez Node.js: w Chrome DevTools (F12) → zaznacz element `.plotno` → menu „Capture node screenshot”.
5. Pracujesz w Figmie lub Canvie? Odtwórz układ 1:1 według podglądu z `szablony/png/siatka/` i tabeli formatów w brandbooku (rozdział 7).

### Lista szablonów

| Plik | Wymiar | Układ |
|---|---|---|
| `ig-1080x1350-cytat` / `-porada` / `-produkt` / `-wydarzenie` | 1080 × 1350 | post IG / FB |
| `ig-1080x1350-karuzela-1-okladka` / `-2-slajd` / `-3-koniec` | 1080 × 1350 | karuzela |
| `ig-1080x1080-cytat` / `-porada` | 1080 × 1080 | post kwadratowy |
| `story-1080x1920-porada` / `-wydarzenie` | 1080 × 1920 | story / reels |
| `linkedin-1200x1200-porada`, `linkedin-1200x627-link` | 1200 × 1200, 1200 × 627 | LinkedIn (627 także jako grafika linku FB) |
| `linkedin-baner-1584x396`, `linkedin-firma-1128x191` | baner profilu i strony firmy | LinkedIn |
| `facebook-okladka-1640x624` | 1640 × 624 | okładka FB |
| `youtube-miniatura-1280x720` | 1280 × 720 | miniatura YT |
| `reklama-meta-1080x1080`, `reklama-google-1200x628`, `reklama-google-300x250` | reklamy | Meta, Google |
| `prezentacja-1920x1080-tytul` / `-tresc` | 1920 × 1080 | slajdy |
| `okladka-pdf-a4` | 1240 × 1754 (eksport ×2 = A4 300 dpi) | okładka PDF „Kierunek Zysk” |

## Najważniejsze zasady w 10 punktach

1. Logo tylko z plików z `logo/`. Nie rozciągaj, nie zmieniaj kolorów, bez cieni. Pole ochronne = ¼ wysokości znaku.
2. Tło krem lub biel, karty w jasnych pastelach. Tekst atrament (#3A2340), nigdy czarny.
3. **Nie pisz białym na koralu (#FF6B3D) ani na liliowym (#B9A5E3)**: za słaby kontrast. Na koralu pisz atramentem, biały tekst tylko na koralu ciemnym (#E8512A) i od 19 px pogrubionym.
4. Nagłówki Fraunces 600, tekst DM Sans, wstawka Caveat do 6 słów. Minimum 26 px na grafice 1080 px.
5. Jedno żółte podkreślenie „zakres” na grafikę.
6. Koła pastelowe, przycięte krawędzią. Przyciski i plakietki jako pigułki. Cienie tylko miękkie.
7. Zdjęcia jasne, naturalne światło. Portret Angeliki bez zmian rysów twarzy i bez filtrów.
8. Lilek tylko przy Akademii i darmowych treściach.
9. Bez obietnic dochodu, bez nazw firm, z którymi pracowała Angelika, bez danych klientów.
10. Przed oddaniem przejdź checklistę z rozdziału 9 brandbooka i oddaj plik źródłowy razem z PNG/PDF.

## Dla osób technicznych: odtworzenie plików

```bash
pip install fonttools
# fonty TTF z Google Fonts (Fraunces opsz 9/72/144 w odmianach 700/600, DM Sans 400) w katalogu $FONTY
FONTY=/sciezka/do/ttf python3 brand/narzedzia/generuj-logo.py   # logo SVG (tekst na krzywych)
python3 brand/narzedzia/paleta.py                              # pliki kolorów + tabela kontrastów
node brand/narzedzia/generuj-lilka.js                          # Lilek SVG
node brand/narzedzia/eksport-logo.js                           # PNG logo, favicony, Lilek PNG
node brand/narzedzia/eksport-szablonow.js                      # PNG szablonów
(cd brand && ./narzedzia/przygotuj-obrazy-brandbooka.sh)       # obrazy do brandbooka (ImageMagick)
node brand/narzedzia/brandbook-pdf.js                          # PDF brandbooka
```

Repozytorium jest publiczne: nie dodawaj tu haseł, płatnych PDF-ów ani danych klientów.
