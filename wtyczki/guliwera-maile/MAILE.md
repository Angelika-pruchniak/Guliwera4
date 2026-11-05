# Maile systemowe Guliwera 4 (wtyczka `guliwera-maile`)

| Mail | Kiedy | Temat |
|---|---|---|
| Powitanie | rejestracja na „Moje konto” (WooCommerce) albo konto dodane w panelu | Witaj w Guliwera 4! Twoje konto jest gotowe |
| Reset hasła | „Nie pamiętasz hasła?” (Moje konto albo wp-login) | Ustaw nowe hasło do konta Guliwera 4 |
| Hasło zmienione | po resecie albo zmianie hasła na koncie | Twoje hasło do Guliwera 4 zostało zmienione |
| Nowe logowanie | logowanie z urządzenia, którego konto jeszcze nie znało | Nowe logowanie na Twoje konto Guliwera 4 |
| Zmiana e-maila | zmiana adresu e-mail konta (na stary adres) | Zmieniono adres e-mail konta Guliwera 4 |

## Zamówienia, Akademia i administrator (wersja 1.1)

| Mail | Kiedy | Temat (≤ 50 znaków) |
|---|---|---|
| Czeka na płatność | zamówienie wstrzymane (płatność w toku) | Zamówienie nr {n} czeka na płatność |
| Oto Twój PDF / pakiet / pliki | zamówienie zrealizowane (po zapłacie, od razu) | Oto Twój PDF · zamówienie nr {n} |
| Płatność przyjęta | zamówienie w realizacji | Płatność przyjęta · zamówienie nr {n} |
| Płatność nieudana | status „nieudane” (włączony przez wtyczkę) | Płatność nie przeszła · zamówienie nr {n} |
| Anulowane | wstrzymane/w realizacji → anulowane (włączony przez wtyczkę) | Zamówienie nr {n} zostało anulowane |
| Zwrot / zwrot częściowy | zwrot w panelu zamówienia | Zwrot pieniędzy · zamówienie nr {n} |
| Wiadomość do zamówienia | notatka „dla klienta” w panelu | Wiadomość do zamówienia nr {n} |
| Szczegóły / zapłać | ręcznie z panelu („wyślij szczegóły”) | Zapłać za zamówienie nr {n} |
| Odstąpienie od umowy | formularz odstąpienia WooCommerce 11 | Mamy Twoje oświadczenie o odstąpieniu |
| Kupon z Akademii | „Odbierz kupon” w aplikacji (kupon `AKADEMIA-…`) | Twój kupon −{p}% z Akademii jest gotowy |
| Admin: nowe / nieudane / anulowane / odstąpienie | zdarzenia sklepu | Nowe zamówienie nr {n} · {kwota} · faktura |

Treści są w `includes/tresci-sklep.php` (bez zależności od WooCommerce), integracja w `includes/sklep.php`,
podgląd w `includes/podglad.php`. Szablony w `templates/emails/` to cienkie nakładki na `gk_ml_wc_render()`.

**Pola w zamówieniu** (blokowy formularz zamówienia, sekcja „Dodatkowe informacje”):
- obowiązkowe pole zgody `guliwera/zgoda-cyfrowe` (tekst w `GK_ML_ZGODA_TEKST`); data zgody trafia do meta `_gk_zgoda_cyfrowe`
  i jest cytowana w mailach jako potwierdzenie na trwałym nośniku (art. 21 i art. 38 ust. 1 pkt 13 u.p.k., §6 regulaminu);
- „Chcę fakturę” + dane i NIP (pokazują się po zaznaczeniu, NIP sprawdzany sumą kontrolną); meta `_gk_faktura*`.
  Fakturę bez VAT wystawia się ręcznie w fakturowo.pl: mail do administratora ma gotowe dane.

Podgląd (zalogowany administrator): `https://guliwera4.pl/?gk_mail_podglad=lista`, wersja tekstowa: `&tekst=1`.
Lokalnie: `php narzedzia/podglad-maili.php KATALOG` (HTML, TXT, spis tematów).

**Uwaga:** szablony działają, gdy w WooCommerce jest wyłączony nowy „edytor maili blokowych”
(WooCommerce → Ustawienia → E-maile). Prośba o opinię (`customer_review_request`) jest wyłączona: to marketing (sekcja B).

## Zastosowane dobre praktyki
- **Jeden cel, jeden przycisk**: duży przycisk (min. 44 px wysokości), a pod nim link zapasowy.
- **Konkretny temat do ~50 znaków i preheader**, czyli zdanie widoczne w skrzynce obok tematu.
- **Rozpoznawalny nadawca** (Guliwera 4, kontakt@guliwera4.pl), Reply-To na prawdziwą skrzynkę i wysyłka przez SMTP nazwa.pl, zgodnie z SPF domeny.
- **Bezpieczeństwo**: nigdy nie wysyłamy hasła; link resetu ważny 24 h i jednorazowy; zawsze „to nie Ty?” z instrukcją; potwierdzenie po zmianie hasła i e-maila (na stary adres).
- **Alert logowania tylko z nowego urządzenia**, nie przy każdym logowaniu (to byłby spam). Pierwsze urządzenie i rejestracja nie wywołują alertu. IP jest częściowo ukryte.
- **Budowa odporna na programy pocztowe**: tabele, style w linii, szerokość 600 px i układ mobilny, czcionki systemowe (Georgia/Arial), kolory bez zależności od obrazków, `color-scheme: light`, `lang="pl"`.
- **Dostępność**: kontrast tekstu atrament na bieli 13:1, przycisk biały na #E8512A, tekst 18 px pogrubiony (duży tekst, kontrast ponad 3:1), alt przy logo.
- **Wersja tekstowa** (multipart) dla filtrów antyspamowych i czytników.
- **Stopka**: dane firmy (RODO), kontakt, linki i powód wysłania (konto, zamówienie albo kupon). Maile transakcyjne nie zawierają reklam ani cross-sellingu, więc nie wymagają zgody marketingowej.
- **Prawo konsumenckie przy treściach cyfrowych**: potwierdzenie zamówienia z danymi sprzedawcy, ceną, licencją, linkiem do regulaminu i cytatem zgody na natychmiastowe dostarczenie.
- **Mniej szumu dla administratora**: wyłączone powiadomienia do admina o resecie hasła przez klienta.
