# „Pochwal się! Podsumowanie roku 2026” (produkt cyfrowy, 99 zł)

Zeszyt ćwiczeń A4, 30 stron, do druku i do wypełniania na tablecie (304 pola formularza PDF).
Trzy części: **Zyski** (co Ci się udało) → **Lekcje** (czego nauczył Cię rok) → **Energia** (z czym wchodzisz w 2027).
Maskotka: **Wafel**, owczarek australijski blue merle tricolor (brązowe i niebieskie oko, żółta obroża).

W publicznym repozytorium są tylko źródła i podgląd 3 stron. **Gotowego PDF-u tu nie ma** (`dist/` jest w `.gitignore`).

## Pliki

| Plik | Co to |
|---|---|
| `strony.js` | Treść wszystkich 30 stron (HTML generowany z funkcji, jedna funkcja = jedna strona) |
| `styl.css` | Wygląd stron: paleta i fonty marki, karty, linie do pisania |
| `buduj.js` | HTML → PDF (Playwright) → pola formularza (pdf-lib, font DM Sans z polskimi znakami) |
| `wafel/` | Generator Wafla (SVG w 4 nastrojach: radosny, mruga, dumny, śpi) |
| `okladka/` | Okładka do sklepu 1200×1200 w stylu okładek „Kierunek Zysk” |
| `podglad/` | Podgląd stron 1, 8 i 15 (PNG) |
| `SKLEP.md` | Szkic opisu sprzedażowego do WooCommerce |

## Budowanie

```bash
cd produkty/podsumowanie-roku
./pobierz-fonty.sh          # Fraunces, DM Sans, Caveat z Google Fonts (SIL OFL)
npm i                       # pdf-lib, @pdf-lib/fontkit (Playwright jest w środowisku globalnie)
node buduj.js --podglad     # dist/Pochwal-sie-Podsumowanie-roku-2026-Guliwera4.pdf + podglad/
node okladka/generuj.js     # okladka/pochwal-sie-podsumowanie-roku.png
```

Na kolejny rok wystarczy zmienić `ROK` i `NOWY` na górze `strony.js`.
