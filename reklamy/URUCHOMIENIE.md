# Uruchomienie reklam Meta – instrukcja krok po kroku

Zgoda Angeliki na podział budżetu: 23.11.2026 (maks. 800 zł/mies., 25 zł/dzień). Black Week: bez rabatu, dopóki Angelika nie zdecyduje inaczej.
Wykonuje: Angelika albo Claude in Chrome na jej komputerze (czat Reklamy nie ma dostępu do Menedżera reklam).
**Nie klikać „Opublikuj” w kampaniach bez wyraźnego „tak” Angeliki przy tym kroku.**

## Krok 1. Piksel Meta (zbiór danych)
1. business.facebook.com → **Menedżer zdarzeń** → **Połącz źródła danych** → **Sieć** → nazwa `guliwera4.pl` → **Utwórz**.
2. Wybierz „Skonfiguruj ręcznie” (NIE instaluj kodu na stronie – idzie przez GTM, bo wtedy działa baner zgody).
3. Skopiuj **identyfikator zbioru danych** (15–16 cyfr).
4. **Ustawienia → Weryfikacja domeny** (w Ustawieniach firmy → Bezpieczeństwo marki → Domeny): dodaj `guliwera4.pl`, metoda „rekord TXT DNS” → rekord dodaj w panelu nazwa.pl.

## Krok 2. Piksel w GTM-KK3LPNV6
1. **Szablony → Galeria szablonów tagów** → wyszukaj „Facebook Pixel” (autor: facebookarchive) → Dodaj.
2. **Zmienne → Nowa → Zmienna warstwy danych**: `ecommerce.value`, `ecommerce.currency`, `ecommerce.transaction_id`, `event_id` (jeśli jeszcze nie istnieją).
3. **Reguły** (Zdarzenie niestandardowe): `purchase`, `view_item`, `add_to_cart`, `begin_checkout`, `sign_up`, `generate_lead`.
4. **Tagi** (szablon Facebook Pixel, w każdym ten sam identyfikator piksela):
   | Tag | Standardowe zdarzenie | Reguła | Parametry |
   |---|---|---|---|
   | Meta – PageView | PageView | All Pages | – |
   | Meta – ViewContent | ViewContent | view_item | value, currency=PLN |
   | Meta – AddToCart | AddToCart | add_to_cart | value, currency |
   | Meta – InitiateCheckout | InitiateCheckout | begin_checkout | value, currency |
   | Meta – Purchase | Purchase | purchase | value={{ecommerce.value}}, currency={{ecommerce.currency}}, Event ID={{event_id}} |
   | Meta – CompleteRegistration | CompleteRegistration | sign_up | Event ID={{event_id}} |
   | Meta – Lead | Lead | generate_lead | – |
5. W **każdym** tagu Meta: **Ustawienia zaawansowane → Ustawienia zgody → Wymagaj dodatkowej zgody** → `ad_storage`. (Bez tego piksel mógłby ruszyć przy samej zgodzie na analitykę.)
6. **Podgląd**: na stronie „Ustawienia” w banerze → zaznacz tylko Analityczne → tagi Meta NIE mogą się uruchomić. Potem „Akceptuję wszystkie” → PageView się uruchamia. **Opublikuj.**
7. W Menedżerze zdarzeń → **Testuj zdarzenia**: wejdź na produkt, dodaj do koszyka – widać ViewContent, AddToCart.

## Krok 3. Bezpiecznik budżetu
Menedżer reklam → **Rozliczenia i płatności → Limit wydatków konta → Ustaw limit: 175 zł** (test 24–30.11). 1.12: podnieść o 800 zł.

## Krok 4. Kampania 1 – Sprzedaż
- Cel: **Sprzedaż**. Konfiguracja: **ręczna** (nie Advantage+ kampania zakupowa). Nazwa: `G4 | Sprzedaż | Strategia 39 zł | XI-XII 2026`.
- Budżet Advantage+ kampanii: **wyłączony**. Zestaw reklam: **15 zł dziennie**, start po akceptacji, bez daty końca.
- Lokalizacja konwersji: **Strona internetowa**, zbiór danych `guliwera4.pl`, zdarzenie **Zakup**.
- Odbiorcy: **Advantage+**, Polska, wiek 25–55 (minimum 25), sugestie: Małe i średnie przedsiębiorstwa, Przedsiębiorczość, Zarządzanie. Język: polski.
- Umiejscowienia: **Advantage+**.
- Reklamy (3 warianty, Meta sama wybierze najlepszy):
  - Materiał: rolki z kalendarza z twarzą Angeliki („Twoja strategia w 60 sekund”, „przed/po – chaos vs plan na 1 stronie”), zdjęcie „cześć, tu Angelika”, grafika produktu.
  - Adres: `https://guliwera4.pl/produkt/strategia-na-1-stronie/?utm_source=facebook&utm_medium=paid_social&utm_campaign=sprzedaz_strategia39&utm_content={{ad.name}}`
  - Wezwanie do działania: **Kup teraz**.
  - Tekst A: „Najlepsza strategia, jaką widziałam, miała jedną stronę. Pokażę, jak taką zrobić – krok po kroku, bez korporacyjnego żargonu. Strategia na 1 stronie: szablon z przykładem, PDF od ręki za 39 zł.”
  - Tekst B: „Plan firmy na 2027 na jednej kartce: kierunek, klienci, 3 priorytety i liczby do sprawdzania co miesiąc. Szablon i instrukcja w PDF, od ręki, 39 zł.”
  - Tekst C: „Zanim zaplanujesz 2027, odpowiedz na 9 pytań. Strategia na 1 stronie prowadzi przez nie po kolei. Jedno popołudnie, jedna kartka, jasny plan.”
  - Nagłówki: „Plan firmy na 1 stronie” · „Strategia bez żargonu – 39 zł” · „Szablon z przykładem, PDF od ręki”
- Odrzucone przy przeglądzie: „Cele w głowie, a w kalendarzu chaos?” (pytanie o sytuację osobistą odbiorcy – ryzyko odrzucenia przez Meta), „Koniec chaosu w planach” (obietnica efektu).

## Krok 5. Kampania 2 – Obserwujący IG
- Cel: **Ruch**. Miejsce docelowe: **Profil na Instagramie**. Nazwa: `G4 | Obserwujący IG | rolki | XI-XII 2026`.
- Zestaw reklam: **10 zł dziennie**. Odbiorcy jak w K1. Umiejscowienia: tylko Instagram (Kanał, Reels, Relacje).
- Reklamy: 2–3 najlepsze rolki z kalendarza („Zanim dasz rabat, policz to” – bez słowa o promocji Guliwera 4; „dzień z życia – etat + własna marka”).
  - Tekst A: „Strategia, produkty cyfrowe i dobry humor. Co tydzień krótko o planowaniu w małej firmie – bez nudy i bez żargonu. Zajrzyj na profil 👋”
  - Tekst B: „Prowadzę małą firmę i pracuję na etacie. Pokazuję, jak planuję i liczę – po ludzku. Zajrzyj na profil.”
- Ograniczenie: Meta mierzy wejścia na profil, nie obserwowanie – liczbę obserwujących sprawdzamy w Instagramie (przegląd tygodniowy).

## Krok 6. Zasady kreacji (obie kampanie)
- Twarz Angeliki: tylko prawdziwe zdjęcia i nagrania, bez zmian rysów (bez filtrów zmieniających twarz, bez generowania).
- **Etykieta AI**: jeśli zdjęcie, wideo lub głos jest fotorealistyczny i wygenerowany albo zmieniony przez AI (także narzędziami AI w Canvie/Meta) – w reklamie włączyć „Informacje o AI”. Grafiki z Waflem i zwykły montaż – bez etykiety.
- Bez cen promocyjnych, „−30%”, „Black Friday” – dopóki Angelika nie zdecyduje o promocji. Przy promocji: „Najniższa cena od początku sprzedaży (4.11.2026): 297 zł”.

## Krok 7. Przed kliknięciem „Opublikuj” – lista kontrolna
- [ ] Testuj zdarzenia w Menedżerze zdarzeń pokazuje PageView i ViewContent.
- [ ] Limit wydatków konta: 175 zł.
- [ ] K1: 15 zł/dzień, zdarzenie Zakup; K2: 10 zł/dzień, profil IG.
- [ ] Teksty i materiały jak wyżej, etykieta AI gdzie trzeba.
- [ ] „Tak” Angeliki.

## Po starcie
Przegląd po 7 dniach (progi w `PROPOZYCJA-BUDZETU-XI-XII.md`). Kampanie przechodzą weryfikację Meta (zwykle do 24 h).
