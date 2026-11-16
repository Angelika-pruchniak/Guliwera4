# Plan dla czatu od postów (social media i grupy): 100 płatnych pobrań PDF do 31.12.2026

Przygotowany w czacie „Strategia” 16.11.2026. Bez prawdziwych danych sprzedażowych: tylko ruch i założenia.

## 1. Stan na 16.11 (Jetpack + Metricool)

- **Ruch na stronie 1–16.11:** 194 odsłony, ok. 49 odwiedzin; w ostatnim tygodniu (9–16.11) **25 odsłon**.
  Strony produktów: Pakiet 11, Skrypty 2, Planer 1, Audyt 1 odsłona. Z zewnątrz przyszło **1 wejście z Facebooka**.
- **Publikacja:** posty wychodzą bezpośrednio z konta Angeliki (Meta Business Suite dla strony FB, LinkedIn), prowadzi je czat
  w przeglądarce Angeliki (Claude in Chrome). **Metricool nie jest używany** – leżą tam tylko szkice z 5–8.11; traktujemy je jako bank treści.
- Mimo publikacji w listopadzie strona dostała z Facebooka 1 wejście, a z LinkedIn 0: posty nie prowadzą do strony
  (brak linku, link bez UTM albo wezwanie „napisz w komentarzu” bez odpowiedzi z linkiem).
- GA4 nadal niepodłączone, więc nie widać kliknięć ani konwersji. Do tego czasu mierzymy Jetpackiem i linkami z UTM.

**Wniosek:** brak pobrań to przede wszystkim brak ruchu ze social mediów na stronę, a nie słaba oferta.

## 2. Cel i matematyka (decyzja Angeliki 16.11: **100 płatnych** pobrań do 31.12)

Sam ruch z postów nie wystarczy: przy konwersji strony 1,5–2% 100 zakupów wymaga 5–7 tys. wejść (dziś ok. 25/tydz.).
Dlatego cel dzielimy na kanały, w których konwersja jest wyższa niż na zimnej stronie:

| Kanał | Cel (szt.) | Jak | Kto |
|---|---|---|---|
| 1. Ciepła sieć 1:1 (LinkedIn, Messenger, mail) | 20 | 150 spersonalizowanych wiadomości (ok. 13% kupuje 39–79 zł) | Angelika wysyła, czat pisze szkice i listę |
| 2. Zamówienia firmowe na prezent świąteczny | 30 | 2–3 firmy × 10–15 licencji „Zestaw Plan 2027” dla managerów, faktura, rabat ilościowy | Angelika: 10 rozmów / maili do HR i właścicieli |
| 3. Grupy na Facebooku + The Angels | 15 | posty z wartością, link w komentarzu, dzień autopromocji | Angelika publikuje, czat przygotowuje |
| 4. Posty LinkedIn + strona FB | 15 | 3+3 posty tygodniowo, każdy z linkiem UTM do jednego produktu | czat w przeglądarce: Meta Business Suite + LinkedIn |
| 5. Partnerstwa | 10 | 1–2 osoby z własną społecznością (np. organizatorka grupy przedsiębiorczyń), kod partnerski −15% + prowizja 20% | Angelika: 3 propozycje współpracy |
| 6. Mały test reklamy (retargeting + podobni odbiorcy) | 10 | ok. 600–800 zł do 31.12, koszt zakupu ≤ 60–80 zł | **tylko za zgodą Angeliki** |
| **Razem** | **100** | przychód przy średnio ok. 70 zł/szt. ≈ 7 tys. zł | |

**Produkt bohater na grudzień:** „Zestaw Plan 2027” = Strategia na 1 stronie + Planer 90 dni za **79 zł**
(zamiast 98 zł osobno). Wejście: Strategia na 1 stronie 39 zł. Wyżej: Pakiet 297 zł (Black Friday: bonus zamiast rabatu).

**Tygodniowy plan zakupów:** 17–23.11: 5 · 24–30.11 (Black Friday): 20 · 1–7.12: 15 · 8–14.12 (prezenty firmowe): 25 ·
15–21.12: 25 · 22–31.12: 10.

**Warunki konieczne (bez nich nie ruszamy kanałów 2, 5 i 6):**
1. **Zakup testowy działa** (Stripe: karta, BLIK, P24) – Angelika, w tym tygodniu. Bez tego żadnej sprzedaży nie będzie.
2. Produkt „Zestaw Plan 2027” 79 zł i **licencja zespołowa** (np. 10 osób, 590 zł, faktura). Dziś wtyczka pozwala kupić PDF tylko raz w koszyku,
   więc firmy nie kupią kilku sztuk (czat „Dostęp do strony”, po akceptacji Angeliki).
3. Kody rabatowe dla partnerów (WooCommerce → Marketing → Kupony) i linki UTM.
4. Mail po zakupie z prośbą o opinię (dowód społeczny do postów od 8.12).

## 3. Co poprawić w kalendarzu postów (teraz)

1. **Metricool – odłączamy z planu.** Nie planujemy tam postów. Publikacja: Meta Business Suite (strona FB) i LinkedIn z konta Angeliki.
   Szkice z Metricool wolno wykorzystać jako teksty po dostosowaniu do celu sprzedaży.
2. **Zgodność ze strategią (decyzja 8.11: dźwignią są produkty cyfrowe, nie szkolenia):**
   - posty o szkoleniu „AI i sprzedaż w małej firmie” (19.11 LI, 17.11 FB, 1.12 FB) oraz KFS/BUR (12.11 LI, 16.11 FB)
     → wycofać albo zamienić wezwanie: zamiast „napisz AI, prześlę program szkolenia” dać link do Zestawu Plan 2027 / Strategii na 1 stronie;
   - **Black Friday:** posty 25 i 27.11 mówią „bonus zamiast rabatu”. Oferta musi to powtarzać: zamiast −30% na Pakiet
     → **Pakiet 297 zł + Planer 90 dni i Strategia na 1 stronie gratis** (lub darmowa 30-minutowa sesja Q&A w sobotę).
     Inaczej marka mówi jedno, a robi drugie. ❓ decyzja Angeliki.
3. **Każdy post ma jeden cel i jeden link z UTM**, np.
   `guliwera4.pl/produkt/strategia-na-1-stronie/?utm_source=linkedin&utm_medium=social&utm_campaign=plan2027&utm_content=post-1711`
   (facebook / linkedin / fb_grupa_nazwa). Na LinkedIn link w pierwszym komentarzu.
4. **Mechanika „napisz słowo w komentarzu → wyślę link”** działa tylko wtedy, gdy ktoś faktycznie odpisze w ciągu kilku godzin.
   Angelika: 10 minut wieczorem albo autoodpowiedź w Messengerze (Meta Business Suite → Skrzynka → Automatyzacje).

## 4. Rytm publikacji do 31.12 (czas Angeliki: ok. 15 min dziennie na komentarze)

| Kanał | Częstotliwość | Format | Wezwanie |
|---|---|---|---|
| LinkedIn (profil Angeliki) | 3×/tydz. (wt, śr, czw 8:00–9:00) | karuzela PDF / post tekstowy z liczbami | Zestaw Plan 2027 79 zł / Strategia na 1 stronie 39 zł |
| Facebook (strona) | 3×/tydz. | grafika + pytanie | Zestaw Plan 2027 / 39 zł; Akademia raz w tygodniu |
| **Grupy na Facebooku** | 2–3 posty tygodniowo, każda grupa max 1×/tydz. | wartość bez linku w treści, link w komentarzu, jeśli regulamin grupy pozwala | „kto chce przykład wypełnionej kanwy, niech napisze PLAN” → w wiadomości link do zakupu |
| Mailchimp | 1×/tydz. (czwartek) | krótki list + 1 narzędzie | produkt z drabiny |

**Grupy (publikacja z konta Angeliki w przeglądarce, po jej akceptacji każdej grupy i treści):**
- grupy przedsiębiorców z Lublina i Lubelskiego, kobiet w biznesie (w tym The Angels), właścicieli małych firm
  usługowych, młodych managerów / HR;
- przed pierwszym postem: przeczytać regulamin, sprawdzić „dni autopromocji” (np. piątek), 2–3 dni komentować cudze posty;
- czat od postów prowadzi tabelę grup: nazwa, liczba członków, zasady linków, dzień autopromocji, data posta,
  komentarze, wejścia (UTM).

## 5. Kampania „Plan 2027 na 1 stronie” (17.11–31.12)

Motyw: koniec roku i efekt „nowego startu” (już w poście 4.12). Wszystkie posty prowadzą do zakupu: Zestaw Plan 2027 za 79 zł
albo Strategia na 1 stronie za 39 zł.

| Tydzień | Temat | Przykładowe posty i działania |
|---|---|---|
| 17–23.11 | Rozgrzewka + sieć | „3 pytania, zanim zaplanujesz 2027”; start wiadomości 1:1 (50 osób); 3 propozycje partnerstw |
| 24–30.11 | Black Friday po swojemu | bonus zamiast rabatu (spójnie z postami): Pakiet + Zestaw Plan 2027 gratis; ankieta; kolejne 50 wiadomości |
| 1–7.12 | Premiera Zestawu Plan 2027 | „12 dni do planu na 2027” (fragment kanwy w poście, pełna w PDF); maile do 10 firm: licencja jako prezent dla zespołu |
| 8–14.12 | Dowód + prezenty | zdjęcia wypełnionej kanwy (fikcyjna firma), pierwsze opinie, „prezent dla zespołu do 18.12”; ostatnie 50 wiadomości |
| 15–21.12 | Domknięcie | Planer 90 dni na I kwartał; „ostatni tydzień przed świętami: 1 godzina na plan”; przypomnienie do osób, które kliknęły |
| 22–31.12 | Lekko + przypomnienie | podsumowanie roku, „co zostawiasz w 2026”, ostatnie przypomnienie o zestawie |

**Zasady treści:** forma neutralna płciowo, bez obietnic dochodu („zarobisz X zł”), bez zwracania się do cech osobistych,
źródła przy badaniach, identyfikacja Kierunek Zysk (koral, liliowy, Fraunces / DM Sans), maskotka Lilek przy Akademii.
Przy każdej obniżce: najniższa cena z 30 dni przed obniżką (Omnibus).

## 6. KPI tygodniowe (czat od postów raportuje w poniedziałek)

| KPI | Cel / tydzień |
|---|---|
| Opublikowane posty (LI + FB + grupy) | 8–9 |
| Kliknięcia w link (statystyki Meta Business Suite / LinkedIn + UTM) | 40 → 80 |
| Wejścia na strony produktów (Jetpack) | 30 → 100 |
| Zakupy (WooCommerce) | wg planu tygodniowego: 5 · 20 · 15 · 25 · 25 · 10 |
| Wysłane wiadomości 1:1 / odpowiedzi | 50 / tydz. przez 3 tygodnie |
| Rozmowy z firmami o licencjach | 10 do 14.12 |
| Komentarze ze słowem-kluczem i wysłane linki | 100% odpowiedzi w 24 h |

**Kryteria zmiany kursu:** jeśli po 2 tygodniach kliknięcia < 40/tydz., przechodzimy na więcej grup i krótsze posty
z jednym narzędziem. Jeśli 30.11 jest mniej niż 25 zakupów łącznie, przesuwamy siły na kanały 1 i 2 (wiadomości i firmy) i proponujemy test reklamy
(za zgodą Angeliki). Jeśli 14.12 jest mniej niż 50, cel realnie spada do ok. 70 i mówimy to wprost.
