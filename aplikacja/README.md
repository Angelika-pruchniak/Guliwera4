# Akademia Guliwera

Aplikacja (PWA) do nauki języka biznesu dla młodych managerów. Autorskie lekcje Guliwera 4.
Krótkie codzienne lekcje na „Trasie Wafla” (etapy i przystanki z pieczątkami), seria dni (łapki), XP, poziomy, monety, paczki od Wafla, odznaki i powtórki. Bez serc: błędne pytanie wraca na koniec lekcji.
Maskotka: **Wafel**, owczarek australijski Guliwera 4 (blue merle, koralowa bandana „G4”). Źródło: `brand/wafel-g4/`.

## Uruchomienie
```bash
npm install
npm run dev      # podgląd na żywo
npm test         # testy treści i logiki gry
npm run build    # wersja produkcyjna w dist/ (statyczne pliki, dowolny hosting)
```

## Gdzie co jest
- `src/content/kurs.ts`: **cała treść kursu** (8 działów, 24 lekcje). W pytaniach `c(...)` i `fill(...)` pierwsza opcja jest poprawna, a aplikacja tasuje kolejność.
- `src/store.ts`: zasady gry: XP, poziomy, seria, monety, odznaki, powtórki (1/3/7/14/30 dni).
- `src/fx.ts`: dźwięki (syntezowane w przeglądarce, bez plików) i wibracje.
- `src/components/Wafel.tsx`: maskotka (7 min: happy, cheer, sad = zmartwiony przy błędzie, think, wave, sleep = drzemka po 2 min bezczynności, wait = czeka po 45 s bezczynności lub 2+ dniach przerwy); grafiki `public/wafel/*.webp` z `brand/wafel-g4/pozy/`.
- `src/screens/`: ekrany (powitanie, test poziomujący, ścieżka, lekcja, wynik, powtórki, profil).

## Typy ćwiczeń
wybór (`c`), prawda/fałsz (`tf`), luka (`fill`), pary (`match`), kolejność (`order`), szuflady (`sort`).
