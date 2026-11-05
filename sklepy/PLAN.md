# Akademia Guliwera w Google Play i App Store: plan

## Decyzje (5.11.2026)
| Temat | Decyzja |
|---|---|
| Konto Google Play | osobiste → test zamknięty: min. 12 testerów przez 14 dni przed produkcją |
| Mac | brak → iOS budowany w chmurze (GitHub Actions macOS / Codemagic), test w TestFlight na iPhonie 17 Pro |
| Android | Trusted Web Activity (Bubblewrap), targetSdk 36, `assetlinks.json` z wtyczki |
| iOS | wersja „solidna”: Capacitor, lekcje w paczce (offline), logowanie w aplikacji, lokalne przypomnienia |
| Logowanie | e-mail + Google wszędzie; na iPhonie dodatkowo „Zaloguj przez Apple” (wymóg 4.8) |
| Rabat w wersji sklepowej | postęp i % widoczne, bez kodu kuponu i bez linków do zakupu; kupon wysyłany e-mailem (za zgodą) |
| Usuwanie konta | w aplikacji; usuwa konto, postęp, kupony; zamówienia z danymi zostają ~5 lat (obowiązek podatkowy) |
| Nazwa / ID | „Akademia Guliwera”, `pl.guliwera4.akademia` |
| Analityka | GA4 na stronie i w aplikacji → wymaga zgody na cookies (RODO) i deklaracji w Data safety / App Privacy |

## Zadania Angeliki
- [ ] Play Console (25 USD): konto osobiste, weryfikacja dowodem, status „trader”. Aplikacji jeszcze nie tworzyć.
- [ ] Zebrać 12 adresów Gmail testerów (Android), gotowych zainstalować aplikację i otwierać ją przez 14 dni.
- [ ] Apple Developer (99 USD/rok) jako osoba fizyczna, przez aplikację Apple Developer na iPhonie.
- [ ] Po akceptacji Apple: klucz API App Store Connect (.p8) i klucz Sign in with Apple, wg mojej instrukcji. Pliki wpisać tylko w sekretach GitHuba, nigdy w repo.
- [ ] Konto recenzenta w WordPressie (np. recenzja@guliwera4.pl), hasło tylko w konsolach sklepów.
- [ ] Przeczytać i zaakceptować politykę prywatności (szkic przygotuje Claude).
- [ ] Google Cloud: klient OAuth dla logowania Google w aplikacji iOS (instrukcja w przygotowaniu).

## Zadania Claude (kolejność)
1. Wtyczka guliwera-akademia 1.4: `assetlinks.json`, REST usuwania konta, kupon e-mailem, tryb aplikacji (bez menu sklepu, bez linków zakupu), logowanie tokenem dla iOS.
2. Aplikacja: tryb sklepowy, „Usuń konto”, manifest (id, maskable, zrzuty, kategorie), GA4 ze zgodą.
3. Android: projekt Bubblewrap + workflow GitHub Actions budujący AAB (klucz w sekretach).
4. iOS: projekt Capacitor + workflow macOS → TestFlight; Sign in with Apple, Google Sign-In.
5. Polityka prywatności, strona usuwania konta, baner zgody na cookies (jeśli brak).
6. Materiały do sklepów: opisy, słowa kluczowe, grafiki, zrzuty, Data safety, App Privacy, checklista.
