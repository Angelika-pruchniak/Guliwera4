# Przypomnienia od Wafla (Web Push)

Wtyczka `wtyczki/guliwera-powiadomienia` (serwer) + aplikacja (`src/push.ts`, `PushCard`, `PushSettings`, `public/sw.js`).
Bez zewnętrznych usług i bez kosztów: szyfrowanie i podpis VAPID robi PHP/OpenSSL (`includes/webpush.php`, sprawdzone
niezależną implementacją w Pythonie, 100/100 prób). Klucze VAPID powstają przy pierwszym użyciu w opcji `gk_pw_vapid` (tylko baza WordPressa).

## Zasady
- Zgoda: osobna, pytamy dopiero po 1. ukończonej lekcji (karta „Mam Ci przypominać o lekcji?”), pora 8:00–20:30 (domyślnie 19:00).
- Najwyżej 1 powiadomienie dziennie, nigdy 21:00–8:00, sprawdzane co 15 minut (WP-Cron).
- Treści: 1 dzień przerwy → Wafel macha („3 minuty i seria uratowana”), 2–3 dni → Wafel czeka, 7/14/30 dni → Wafel śpi („zasnął z tęsknoty”),
  seria 3/7/14/30/50/100 dni → Wafel bije brawo. Potem cisza. Zmartwiony Wafel po błędnej odpowiedzi jest tylko w aplikacji.
- iPhone: tylko po dodaniu Akademii do ekranu głównego (iOS 16.4+); wyjaśnia to karta instalacji i Profil.
- Wyłączenie: Profil → „Przypomnienia od Wafla” (usuwa subskrypcję z konta). Wygasłe subskrypcje (404/410) usuwamy automatycznie.
- Dane na koncie (meta `gk_push`): adres powiadomień przeglądarki (endpoint i klucze), pora, data zgody, data ostatniego sprawdzenia.
- Test na własnym koncie: Profil → „Wyślij próbne powiadomienie” (raz na minutę). Panel: Ustawienia → Powiadomienia Akademii.
- WP-Cron uruchamia się przy odwiedzinach strony; przy małym ruchu przypomnienie może przyjść kilka–kilkanaście minut później.

## Polityka prywatności (strona 3) – akapit do dodania
**Przypomnienia w aplikacji Akademia Guliwera.** Jeśli włączysz przypomnienia, zapisujemy na Twoim koncie adres powiadomień Twojej
przeglądarki (generowany przez Google, Apple lub Mozillę), klucze potrzebne do zaszyfrowania wiadomości, wybraną porę i datę zgody.
Podstawą jest Twoja zgoda (art. 6 ust. 1 lit. a RODO). Wysyłamy najwyżej jedno przypomnienie dziennie, nigdy między 21:00 a 8:00.
Treść powiadomienia jest szyfrowana, a dostarcza je usługa powiadomień producenta przeglądarki. Zgodę wycofasz w każdej chwili
w aplikacji (Profil → Przypomnienia od Wafla) albo w ustawieniach przeglądarki; wtedy usuwamy te dane z konta.
