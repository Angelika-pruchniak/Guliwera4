# Maile systemowe Guliwera 4 (wtyczka `guliwera-maile`)

| Mail | Kiedy | Temat |
|---|---|---|
| Powitanie | rejestracja na „Moje konto” (WooCommerce) albo konto dodane w panelu | Witaj w Guliwera 4! Twoje konto jest gotowe |
| Reset hasła | „Nie pamiętasz hasła?” (Moje konto albo wp-login) | Ustaw nowe hasło do konta Guliwera 4 |
| Hasło zmienione | po resecie albo zmianie hasła na koncie | Twoje hasło do Guliwera 4 zostało zmienione |
| Nowe logowanie | logowanie z urządzenia, którego konto jeszcze nie znało | Nowe logowanie na Twoje konto Guliwera 4 |
| Zmiana e-maila | zmiana adresu e-mail konta (na stary adres) | Zmieniono adres e-mail konta Guliwera 4 |

Maile o zamówieniach (WooCommerce) dostają kolory marki, nadawcę „Guliwera 4 <kontakt@guliwera4.pl>” i stopkę z danymi firmy.

Podgląd (zalogowany administrator): `https://guliwera4.pl/?gk_mail_podglad=powitanie` (oraz `reset`, `haslo`, `logowanie`, `email`).

## Zastosowane dobre praktyki
- **Jeden cel, jeden przycisk**: duży przycisk (min. 44 px wysokości), a pod nim link zapasowy.
- **Konkretny temat do ~50 znaków i preheader**, czyli zdanie widoczne w skrzynce obok tematu.
- **Rozpoznawalny nadawca** (Guliwera 4, kontakt@guliwera4.pl), Reply-To na prawdziwą skrzynkę i wysyłka przez SMTP nazwa.pl, zgodnie z SPF domeny.
- **Bezpieczeństwo**: nigdy nie wysyłamy hasła; link resetu ważny 24 h i jednorazowy; zawsze „to nie Ty?” z instrukcją; potwierdzenie po zmianie hasła i e-maila (na stary adres).
- **Alert logowania tylko z nowego urządzenia**, nie przy każdym logowaniu (to byłby spam). Pierwsze urządzenie i rejestracja nie wywołują alertu. IP jest częściowo ukryte.
- **Budowa odporna na programy pocztowe**: tabele, style w linii, szerokość 600 px i układ mobilny, czcionki systemowe (Georgia/Arial), kolory bez zależności od obrazków, `color-scheme: light`, `lang="pl"`.
- **Dostępność**: kontrast tekstu atrament na bieli 13:1, przycisk biały na #E8512A, tekst 18 px pogrubiony (duży tekst, kontrast ponad 3:1), alt przy logo.
- **Wersja tekstowa** (multipart) dla filtrów antyspamowych i czytników.
- **Stopka**: dane firmy (RODO), kontakt, linki i informacja, że to wiadomość systemowa. Maile transakcyjne nie zawierają reklam, więc nie wymagają zgody marketingowej.
- **Mniej szumu dla administratora**: wyłączone powiadomienia do admina o resecie hasła przez klienta.
