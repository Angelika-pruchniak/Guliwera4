# Rabaty za naukę w Akademii Guliwera

Za regularną naukę w Akademii użytkownik dostaje rabat na PDF-y „Kierunek Zysk”.
Rabat rośnie razem z liczbą ukończonych lekcji, ale znika, gdy ktoś przestaje się uczyć.
Wszystko liczy serwer (wtyczka `guliwera-akademia`), a nie przeglądarka. Dzięki temu nie da się dopisać sobie punktów.

## Wzór

```
Rabat % = ⌊ 30 × (L ÷ N) × K ⌋      (kupon wydajemy od 5%)
```

- **L**: ukończone lekcje. Każdą lekcję liczymy raz, a nowe zaliczamy najwyżej 3 dziennie.
- **N**: wszystkie lekcje w kursie (teraz 24). Lista lekcji jest w `app/lekcje.json` w wtyczce.
- **K**: mnożnik regularności. Zależy od liczby aktywnych dni w ostatnich 28 dniach (A):

| Aktywne dni w 28 dniach (A) | K |
|---|---|
| 0–3 | 0 (brak rabatu) |
| 4–7 | 0,5 |
| 8–13 | 0,75 |
| 14 i więcej (co drugi dzień) | 1 |

Dzień jest aktywny, gdy ktoś ukończył w nim lekcję albo zrobił powtórkę. Okno 28 dni się przesuwa, więc po przerwie mnożnik spada.

## Przykłady (N = 24)

| Lekcje | Aktywne dni | Rabat | Przewodnik 147 zł |
|---|---|---|---|
| 6 | 6 | 0% (3,75% < 5%) | 147,00 zł |
| 8 | 8 | 7% | 136,71 zł |
| 12 | 10 | 11% | 130,83 zł |
| 18 | 14 | 22% | 114,66 zł |
| 24 | 10 | 22% | 114,66 zł |
| 24 | 14 | **30%** | 102,90 zł |

Pełne 30% wymaga co najmniej 8 dni na lekcje (limit 3 dziennie) i 14 aktywnych dni w ostatnich 4 tygodniach.

## Kupon
- Kupon odbiera się w aplikacji: Profil → „Rabat na materiały” → „Odbierz kupon”.
- Jeden kupon na 30 dni. Jest ważny 7 dni i można go użyć jeden raz.
- Działa tylko z adresem e-mail konta i tylko na produkty 67–73. Nie łączy się z innymi kuponami.
- Link `/koszyk/?gk_kupon=KOD` od razu dodaje kupon do koszyka.
- Parametry są na górze pliku `wtyczki/guliwera-akademia/guliwera-akademia.php`: `GK_AK_RABAT_MAX`, `GK_AK_LEKCJI_DZIENNIE`, progi w `gk_ak_mnoznik()`, `GK_AK_PRODUKTY`.

## Ograniczenia
- Odpowiedzi do quizów są w aplikacji, więc „ukończenie lekcji” to deklaracja przeglądarki. Ochronę dają limit 3 lekcji dziennie, liczenie dni zegarem serwera, kupon przypisany do e-maila i maksimum 30%.
- Postęp w samej aplikacji (XP, serca, monety) nadal jest zapisany w telefonie. Konto przechowuje tylko to, co jest potrzebne do rabatu, oraz zgodę na maile.
