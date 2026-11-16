# Przekazanie: Instagram i LinkedIn (stan na 16.11.2026)

Dla czatu, który prowadzi i publikuje Instagram („Agent social media: posty FB i LinkedIn”).
Źródło: gałąź `claude/social-instagram-linkedin`, folder `social/`.
**Nic nie zostało opublikowane ani zaplanowane w Metricool. Angelika wymaga swojego „OK” przed publikacją.**

## Gotowe pliki
- `README.md`: plan, struktura, zasady.
- `instagram/01-zalozenie-konta.md`: konto Twórca, nazwa konta, bio (2 wersje), linki z UTM (CTA nr 1: guliwera4.pl/aplikacja/), 5 wyróżnionych relacji, Facebook, Metricool, etykiety AI.
  - Marka osobista jest pod nazwiskiem Angeliki. Nazwy konta w kolejności: @angelika.pruchniak → @angelikapruchniak → @angelika.pruchniak.gaszczyk.
  - Konta @guliwera4 nie zakładamy.
- `instagram/zdjecie-profilowe-1080.jpg`: kadr prawdziwego portretu (`logo/angelika-portret-bialy.jpg`).
- `instagram/02-posty-i-plan.md`: 12 postów IG z opisami i hashtagami oraz plan na 4 tygodnie. Daty startowały 16.11, więc trzeba je przesunąć na dzień założenia konta.
- `instagram/podglad-siatki.jpg`: podgląd siatki profilu.
- `instagram/png/karuzele/`: 4 karuzele 1080×1350:
  - k1: strategia na 1 stronie
  - k2: marża a narzut
  - k3: 3 liczby w poniedziałek
  - k4: nowy produkt, 6 pytań
- `instagram/png/posty/`: p04 (cytat) i p10 (produkt „Strategia na 1 stronie”, 39 zł).
- `instagram/zdjecia-4x5/`: 3 zdjęcia z prawdziwą twarzą (angelika-portret-bialy, 07, 09).
- `instagram/rolki/v2/`: **aktualne rolki**, MP4 1080×1920, 8,5–10 s, z okładkami: r1-trzy-pytania, r2-etat-i-wlasna-marka, r3-zacznij-za-darmo.
  - Nie mają muzyki. Dodać ją w IG, cicho.
  - Rolki v1 w `instagram/rolki/*.mp4` Angelika odrzuciła („wolne, źle wykadrowane”). **Nie używać.**
- `instagram/03-scenariusze-nagran.md`: 5 scenariuszy rolek, w których Angelika mówi do kamery, plus lista ujęć B-roll i instrukcja nagrywania.
- `research/`:
  - `instagram-konta-zagraniczne.md`: zasady treści (proporcje 4 porady / 3 twarz / 2 rolki / 1 sprzedaż, 3 posty w tygodniu)
  - `linkedin-styl-dominika-zak.md`
  - `rolki-montaz.md`
- `linkedin/posty-start.md`: 8 postów LinkedIn z miejscami `[UZUPEŁNIJ]`, 10 pytań do Angeliki i plan publikacji.
- `linkedin/pdf/`: karuzele k1 i k2 jako dokument PDF.
- `narzedzia/`: generatory:
  - `tresci.py` + `render.js` → PNG
  - `rolki_v2.py` → MP4
  - `siatka.py` → podgląd siatki
  - Zmiana nazwy konta na grafikach: podmień ją w `tresci.py` i uruchom ponownie render.

## Zasady
- **Twarz Angeliki pokazujemy tylko na:**
  - `logo/angelika-portret-bialy.jpg` (prawdziwe zdjęcie)
  - `brand/zdjecia-sesja/07…` i `09…`
- **Pozostałe zdjęcia z sesji:**
  - 05 i 10: nie używać, przedstawiają inną twarz.
  - 01 i 08: tylko jako kadr klimatyczny, bez twarzy.
  - 02, 03, 04, 06: nie używamy.
- **Obróbka zdjęć:** tylko kadr, światło i ostrość, bez zmian rysów twarzy. Przy publikacji zdjęć generowanych lub retuszowanych włączać etykietę AI.
- **Brandbook** (gałąź `claude/brandbook`):
  - fonty: Fraunces, DM Sans, Caveat
  - tekst atramentem, nigdy czarnym
  - nie pisać białym na koralu
- **Treści:** bez nazw firm, z którymi pracowała Angelika, bez obietnic dochodu. Repozytorium jest publiczne.

## Otwarte sprawy
- **Rolka głosowa:** czeka na nagrania Angeliki (scenariusz nr 4 „3 pytania przed nowym projektem”, 2–3 podejścia, z dźwiękiem, przez Google Drive).
  - Plan montażu: wycięcie pauz, czyszczenie dźwięku, napisy słowo po słowie, zmiany planu, punch-in.
  - Transkrypcja przez Gemini zwracała błąd 401, więc napisy będą robione ze scenariusza.
- **Canva:** jest podłączona i ma jeden Brand Kit (id `kAFo4UtiqeM`, bez nazwy).
  - Angelika ma potwierdzić, czy to Brand Kit Guliwery 4.
  - Propozycja: wgrać rolki v2 i karuzele do folderu „Instagram – do akceptacji”.
  - Generowania wideo w Canvie nie używać do ujęć z Angeliką, bo AI zmienia twarz.
- **Metricool:** marka LAP (id 7010227) ma Facebook i LinkedIn. Instagram nie był jeszcze podłączony.
- **LinkedIn:** 8 postów czeka na odpowiedzi Angeliki na pytania o jej historie.
