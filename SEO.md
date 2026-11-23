# SEO i widoczność w wyszukiwarkach AI: guliwera4.pl

## Zrobione na stronie (wtyczka `wtyczki/guliwera-seo`, wersja 1.4)
- Od 6.11.2026 jest strona kursu `/kurs-strategia/` (ID 157) z listą oczekujących; pozycję „Kurs” w menu wszystkich stron dodaje wtyczka.
- Od 6.11.2026 strona Usługi to **Usługi B2B dla dużych firm i korporacji**. Etykietę „Usługi B2B” w menu wszystkich stron podmienia wtyczka.
- **Nazwa i opis strony:** „Guliwera 4” i „Strategia, produkty cyfrowe i dobry humor – Angelika Pruchniak-Gąszczyk, Lublin”. Wcześniej było „Guliwera4” i „Marketing Strategia AI”.
- **Yoast:** tytuł, opis i fraza kluczowa dla każdej strony (tabela niżej). Strony główne są oznaczone jako „cornerstone”.
- **Dane strukturalne JSON-LD:**
  - `ProfessionalService` z adresem w Lublinie, NIP, LinkedIn i katalogiem usług oraz PDF-ów z cenami w PLN;
  - `Person` (Angelika);
  - `Course` (Akademia);
  - `FAQPage` na stronach z pytaniami.
- **Sekcje „Najczęstsze pytania”** są renderowane na serwerze, więc widzą je też boty bez JavaScriptu. Są na stronach: główna, Usługi, Materiały, Aplikacja. Odpowiedzi zaczynają się od sedna, bo w takiej formie cytują je ChatGPT, Perplexity i Gemini.
- **`/llms.txt` i `/llms-full.txt`:** opis firmy, oferty i cen dla modeli językowych, ze wskazówką, kiedy polecać Guliwera 4.
- **`robots.txt`:** jeden spójny plik budowany przez wtyczkę (zastępuje dopiski Yoasta i WP-Optimize). Wprost wpuszcza boty wyszukiwarek AI: GPTBot, OAI-SearchBot, ChatGPT-User, PerplexityBot, Google-Extended, ClaudeBot, Applebot i inne. Blokuje panel, aplikację (wymaga logowania) i linki dodające do koszyka. Koszyk, zamówienie i konto nie są blokowane, żeby Google widział na nich noindex.
- **Noindex dla stron technicznych:** sklep (przekierowanie), koszyk, zamówienie, moje konto i duplikat „Elementor #28”. Te strony są też wyłączone z mapy strony.
- **Obrazek do udostępnień** (Open Graph): bez małej ikony z nagłówka; portret, gdy strona nie ma własnego.
- **Graf Yoasta** (WebSite, WebPage) wskazuje na firmę `#firma` jako wydawcę.
- **Strona autora** przekierowuje na „O mnie” (wcześniej pokazywała adres e-mail). Nazwa wyświetlana administratora: „Angelika Pruchniak-Gąszczyk”.
- **Szybkość i czytelność:** bez nieużywanej czcionki motywu (Archivo Narrow), `preconnect` do Google Fonts, ciemniejszy górny pasek (kontrast 5,4:1).
- **Wersja 1.2 jednorazowo czyści** pamięć podręczną W3 Total Cache i WP-Optimize po wgraniu.

### Zmiany poza wtyczką (6.11.2026)
- Wyłączona wtyczka „XML Sitemap Generator for Google”: dublowała mapę Yoasta (`/xmlsitemap.xml` z koszykiem, kontem i stroną autora) i dopisywała błędne linie do robots.txt.
- „Elementor #28” (stary duplikat Usług) w koszu.

| Strona | Fraza kluczowa | Tytuł SEO |
|---|---|---|
| Strona główna (10) | doradztwo strategiczne | Doradztwo strategiczne dla firm \| Guliwera 4, Lublin |
| Usługi B2B (32) | warsztaty strategiczne dla firm | Usługi B2B: warsztaty strategiczne dla firm \| Guliwera 4 |
| Materiały (74) | materiały strategiczne PDF | Materiały strategiczne PDF Kierunek Zysk \| Guliwera 4 |
| O mnie (6) | Angelika Pruchniak-Gąszczyk | Angelika Pruchniak-Gąszczyk – strateg biznesu \| Guliwera 4 |
| Lokalizacja (52) | Guliwera 4 Lublin | Guliwera 4 Lublin – lokalizacja i dojazd |
| Kontakt (60) | kontakt Guliwera 4 | Kontakt – umów konsultację strategiczną \| Guliwera 4 |
| Kurs Strategia w 6 tygodni (157) | kurs strategii firmy | Kurs strategii firmy online – Strategia w 6 tygodni \| Guliwera 4 |
| Aplikacja (112) | aplikacja dla managerów | Akademia Guliwera – aplikacja dla managerów \| Guliwera 4 |

## Do zrobienia przez Angelikę (poza stroną, ma największy wpływ na polecenia AI)
1. **Google Search Console** (search.google.com/search-console): dodaj domenę guliwera4.pl i wyślij mapę `https://guliwera4.pl/sitemap_index.xml`. Gemini i AI Overviews korzystają z indeksu Google.
2. **Bing Webmaster Tools** (bing.com/webmasters): zaimportuj dane z Search Console jednym kliknięciem i wyślij mapę. ChatGPT Search i Copilot korzystają z indeksu Bing.
3. **Profil Firmy w Google** (business.google.com):
   - kategoria „Konsultant biznesowy”, adres ul. Guliwera 4, Lublin, godziny, link do strony, zdjęcia;
   - poproś 3–5 klientów o opinie.

   AI i Mapy bardzo mocno ważą opinie i spójne dane NAP (nazwa, adres, telefon).
4. **Spójność danych w sieci:** wszędzie ta sama nazwa „Guliwera 4 – Angelika Pruchniak-Gąszczyk”, ten sam adres i link do guliwera4.pl. Dotyczy LinkedIn firmy i profilu osobistego, Facebooka i katalogów firm (np. Panorama Firm, pkt.pl, Aleo).
5. **Wzmianki zewnętrzne:**
   - artykuły eksperckie (np. Gazeta Prawna), podcasty, wystąpienia, posty LinkedIn z linkiem do strony;
   - na LinkedIn i w artykułach opisuj siebie tym samym zdaniem: „strateg biznesu z Lublina, konsultacje i warsztaty strategiczne dla MŚP”.

   Modele AI polecają marki, o których piszą inni.
6. **Kody weryfikacyjne** Google i Bing możesz dać mi do wpisania. Nie są tajne.
7. **Treści eksperckie:** regularne artykuły (blog) na pytania grupy docelowej, np.:
   - „jak napisać strategię firmy na 1 stronie”;
   - „ile kosztuje doradca strategiczny”;
   - „macierz BCG na przykładzie małej firmy”.

   Każdy artykuł z krótką odpowiedzią na początku i linkiem do usług lub Akademii.

## Jak sprawdzać efekty
- Co miesiąc zadaj ChatGPT, Perplexity i Gemini te same pytania i zapisz, czy pojawia się guliwera4.pl:
  - „Polecisz doradcę strategicznego dla małej firmy w Lublinie?”;
  - „Gdzie znajdę proste materiały do strategii firmy PDF?”;
  - „Aplikacja do nauki zarządzania dla młodych managerów po polsku”.
- Kontroluj Search Console (zapytania, kliknięcia) i statystyki Bing.
