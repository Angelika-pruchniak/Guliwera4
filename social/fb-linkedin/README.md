# Social media: Facebook (strona) i LinkedIn (profil Angeliki)

Teksty i grafiki postów na FB i LinkedIn przygotowuje czat „Agent social media: posty FB i LinkedIn”.
Publikuje je czat w przeglądarce Angeliki: Meta Business Suite dla FB, LinkedIn bezpośrednio. Metricool nie jest używany.

- Plan i cele: `strategia/PLAN-SOCIAL-100-PDF.md` (gałąź `claude/strategia`).
- Zasady wyglądu i tonu: brandbook `brand/brandbook/` (gałąź `claude/brandbook`).

## Struktura
- `RRRR-MM-DD_tydzien_posty.md`: teksty tygodnia, godziny, linki z UTM, ścieżki do grafik.
- `html/`: źródła grafik zrobione z szablonów `brand/szablony/` (fonty Fraunces, DM Sans, Caveat; logo z `brand/logo/svg/`).
- `png/`: gotowe grafiki w dokładnym wymiarze (FB 1080 × 1350, LinkedIn 1200 × 1200).

## Eksport PNG
Z katalogu repozytorium (wymaga Node.js i Playwright):
`node brand/narzedzia/eksport-szablonow.js`. Działa na `brand/szablony/`. Dla tego folderu wystarczy ten sam kod
z katalogiem `social/fb-linkedin/html` (zrzut elementu `.plotno` po `document.fonts.ready`).

## Checklista przed publikacją (brandbook, rozdz. 9)
1. Tylko polski tekst.
2. Fonty Fraunces, DM Sans i Caveat, logo G4 w stopce.
3. Kolory z palety v3. Bez białego tekstu na koralu i liliowym.
4. Jedno żółte podkreślenie i jedna wstawka Caveat (do 6 słów).
5. Jeden link z UTM na post. Na LinkedIn link idzie w pierwszym komentarzu.
6. Formy neutralne płciowo. Bez obietnic dochodu i bez nazw firm, z którymi pracowała Angelika.
