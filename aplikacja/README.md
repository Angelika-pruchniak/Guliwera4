# Akademia Guliwera

Aplikacja (PWA) do nauki języka biznesu dla młodych managerów. Autorskie lekcje Guliwera 4.
Krótkie codzienne lekcje, seria dni, XP, poziomy, serca, monety, skrzynie, odznaki i powtórki.
Maskotka: **Wafel**, owczarek australijski Guliwera 4 (liliowy merle, koralowa bandana z „4”). Źródło: `brand/wafel/`.

## Uruchomienie
```bash
npm install
npm run dev      # podgląd na żywo
npm test         # testy treści i logiki gry
npm run build    # wersja produkcyjna w dist/ (statyczne pliki, dowolny hosting)
```

## Gdzie co jest
- `src/content/kurs.ts`: **cała treść kursu** (8 działów, 24 lekcje). W pytaniach `c(...)` i `fill(...)` pierwsza opcja jest poprawna, a aplikacja tasuje kolejność.
- `src/store.ts`: zasady gry: XP, poziomy, seria, serca (1 co 30 min), monety, odznaki, powtórki (1/3/7/14/30 dni).
- `src/fx.ts`: dźwięki (syntezowane w przeglądarce, bez plików) i wibracje.
- `src/components/Wafel.tsx`: maskotka (6 min: happy, cheer, sad, think, wave, sleep); grafiki w `wafelSvg.ts`, odświeżanie: `node aplikacja/generuj-wafla-svg.cjs`.
- `src/screens/`: ekrany (powitanie, test poziomujący, ścieżka, lekcja, wynik, powtórki, profil).

## Typy ćwiczeń
wybór (`c`), prawda/fałsz (`tf`), luka (`fill`), pary (`match`), kolejność (`order`), szuflady (`sort`).
