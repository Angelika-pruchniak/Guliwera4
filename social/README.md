# Guliwera 4 · marka osobista na Instagramie i LinkedIn

Właścicielka: Angelika Pruchniak-Gąszczyk. Gałąź robocza: `claude/social-instagram-linkedin`.
**Zasada: nic nie jest publikowane ani planowane w Metricool bez „OK” Angeliki.**

## Plan (pierwsze 6 tygodni)

| Kiedy | Co | Kto |
|---|---|---|
| tydz. 0 (9–15.11) | Akceptacja: research, styl LinkedIn, 12 postów IG, 3 rolki, 8 postów LI. Założenie Instagrama, podłączenie do Metricool. Odpowiedzi na pytania o historie. | Angelika |
| tydz. 0 | Poprawki po uwagach, okładki wyróżnionych relacji, uzupełnienie `[UZUPEŁNIJ]` w postach. | Claude |
| tydz. 1–4 (16.11–11.12) | IG: 3 posty/tydz. + relacje. LinkedIn: 2 posty/tydz. Planowanie w Metricool po „OK”. | Claude planuje, Angelika odpowiada na komentarze |
| tydz. 5 | Przegląd wyników (zasięg, zapisania, kliknięcia w link, wejścia do Akademii w GA4) i plan na kolejny miesiąc. | razem |

Lejek: post → **Akademia (darmowa)** → Strategia na 1 stronie 39 zł → Pakiet Kierunek Zysk 297 zł → kurs wideo 697 zł (przedsprzedaż, premiera IV 2027). Bez obietnic dochodu.

## Pliki

| Plik / folder | Co |
|---|---|
| `research/instagram-konta-zagraniczne.md` | 12 kont z USA/UK + **zasady dla @angelika.guliwera4** |
| `research/linkedin-styl-dominika-zak.md` | analiza stylu + wzór posta |
| `instagram/01-zalozenie-konta.md` | instrukcja: konto, handle, bio, linki, wyróżnione, Facebook, Metricool, etykiety AI |
| `instagram/02-posty-i-plan.md` | siatka 12 postów, opisy, hashtagi, plan 4 tygodni |
| `instagram/podglad-siatki.jpg` | jak będzie wyglądał profil |
| `instagram/zdjecie-profilowe-1080.jpg` | zdjęcie profilowe (kadr portretu) |
| `instagram/zdjecia-4x5/` | 10 zdjęć z sesji w formacie postu 1080×1350 (tylko kadr) |
| `instagram/png/karuzele/` | 4 karuzele gotowe do wrzucenia (1080×1350) |
| `instagram/rolki/` | 3 rolki MP4 1080×1920, 15 s, + okładki |
| `linkedin/posty-start.md` | 8 postów, plan publikacji, pytania o historie |
| `narzedzia/` | generatory: `tresci.py` (teksty grafik), `render.js` (PNG), `rolki.py` (MP4), `siatka.py` |
| `zasoby/` | kopie styl.css, logo i Lilka z brandbooka (gałąź `claude/brandbook`) |

## Jak zmienić tekst na grafice

1. Popraw tekst w `narzedzia/tresci.py`.
2. Z katalogu repozytorium:
   ```bash
   python3 social/narzedzia/tresci.py && node social/narzedzia/render.js
   python3 social/narzedzia/rolki.py            # wszystkie rolki (ok. 1,5 min/szt.) albo: rolki.py r1-trzy-pytania
   python3 social/narzedzia/siatka.py
   ```
Wymaga fontów Fraunces, DM Sans, Caveat (Google Fonts), Playwright i ffmpeg.

## Zasady (skrót)
- Identyfikacja wizualna z brandbooka: krem, koral, róż, liliowy, żółty, atrament, granat; Fraunces / DM Sans / Caveat.
- Zdjęcia: tylko kadr, światło, ostrość. Bez zmian rysów twarzy. Przy publikacji włączaj etykietę AI dla zdjęć generowanych/retuszowanych; w reklamach przestrzegaj zasad Meta.
- Bez nazw firm, z którymi pracowała Angelika, bez danych klientów, bez haseł (repo publiczne).
- Strona WordPress: bez zmian.
