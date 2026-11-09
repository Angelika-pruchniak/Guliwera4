# Instagram: założenie konta krok po kroku (ok. 30 minut)

Konto zakładasz sama (Instagram wymaga weryfikacji telefonem lub mailem). Ja przygotowałam wszystkie teksty i pliki.

## 1. Nazwa użytkownika (@handle)

Nie mogę sprawdzić dostępności nazw z tego środowiska (Instagram blokuje automaty). Wpisz je po kolei w aplikacji; pierwsza wolna wygrywa:

| Kolejność | @handle | Dlaczego |
|---|---|---|
| 1 | **@angelika.guliwera4** | Twarz + marka w jednym. Najlepsze dla marki osobistej. Używam tej nazwy w grafikach. |
| 2 | @angelika.pruchniak | Czysta marka osobista, gdyby kiedyś Guliwera 4 zmieniła nazwę. |
| 3 | @guliwera4 | Gdy wolisz konto firmowe. Zarezerwuj je i tak (nawet puste), żeby nikt go nie zajął. |

Jeśli wybierzesz inną nazwę niż @angelika.guliwera4, daj znać: podmienię stopkę na grafikach (1 linijka w `social/narzedzia/tresci.py`).

## 2. Zakładanie konta

1. Pobierz aplikację Instagram, kliknij **Utwórz nowe konto**.
2. Załóż je na **ten sam e-mail, co Facebook** (łatwiejsze łączenie w Meta Business Suite i Metricool).
3. Wpisz handle, hasło (menedżer haseł, nie do repozytorium), datę urodzenia.
4. Od razu: **Ustawienia → Centrum kont → Hasło i zabezpieczenia → Uwierzytelnianie dwuskładnikowe** (aplikacja uwierzytelniająca).

## 3. Konto profesjonalne: Twórca czy Firma?

**Wybierz: Twórca (Creator).** Ustawienia → Typ konta i narzędzia → Przełącz na konto profesjonalne → **Twórca** → kategoria **„Edukacja”** albo **„Konsultant biznesowy”** (do wyboru w aplikacji).

- Twórca: lepsza biblioteka muzyki do rolek (konto Firma ma tylko muzykę „komercyjną”), a to dla rolek ze zdjęć bardzo ważne.
- Metricool, statystyki i reklamy działają tak samo na obu typach.
- Pokaż kategorię w profilu: tak. Dane kontaktowe: e-mail kontakt@guliwera4.pl (bez telefonu i adresu).

## 4. Profil

**Imię i nazwisko (pole „Imię”, jest przeszukiwane!):**
`Angelika | strategia i liczby dla małych firm`

**Zdjęcie profilowe:** `social/instagram/zdjecie-profilowe-1080.jpg` (kadr Twojego prawdziwego zdjęcia w jasnym garniturze, bez żadnych zmian). Takie samo jak na stronie i LinkedIn = łatwiej Cię rozpoznać.

**Bio (do 150 znaków, wersja A, polecam):**
```
Strategia i liczby dla małych firm, po ludzku 📊
Produkty cyfrowe + etat + mama, więc bez lania wody
👇 Zacznij za darmo: Akademia Guliwera
```

Wersja B (z humorem):
```
Liczę marże, żebyś Ty nie musiała 🧮
Strategia, produkty cyfrowe i dobry humor
👇 Darmowa Akademia: 5 minut dziennie
```

**Zaimki:** opcjonalnie. **Link:** Edytuj profil → Linki → Dodaj link zewnętrzny:
- 1. `https://guliwera4.pl/aplikacja/` tytuł: **Zacznij za darmo · Akademia**
- 2. `https://guliwera4.pl/materialy/` tytuł: **Strategia na 1 stronie · 39 zł**
- 3. `https://guliwera4.pl/` tytuł: **guliwera4.pl**

Pierwszy link wyświetla się w profilu, pozostałe po kliknięciu. Zalecam dodać do linków parametry UTM, żeby GA4 pokazywał ruch z Instagrama, np.
`https://guliwera4.pl/aplikacja/?utm_source=instagram&utm_medium=social&utm_campaign=bio`.

## 5. Wyróżnione relacje (5 sztuk, okładki w kolorach marki)

| Nazwa | Co w środku |
|---|---|
| **Start** | Kim jestem, czym jest Guliwera 4, dla kogo (3–5 relacji) |
| **Akademia** | Jak zacząć za darmo, zrzuty z aplikacji, instalacja na telefonie |
| **Liczby** | Marża, narzut, próg rentowności: skróty z karuzel |
| **Produkty** | Strategia na 1 stronie, Pakiet Kierunek Zysk, kurs (lista oczekujących) |
| **Kulisy** | Etat + marka + dom: jak planuję tydzień |

Okładki wyróżnionych relacji przygotuję po założeniu konta (jednolite koła: koral, róż, liliowy, żółty, granat ze znakiem G4).

## 6. Połączenie z Facebookiem

Ustawienia → **Centrum kont** → Dodaj konta → Facebook → zaloguj się i wybierz **stronę Guliwera 4** (nie tylko profil prywatny).
Potem w Meta Business Suite sprawdź, czy strona i Instagram są w jednym miejscu. Nie włączaj automatycznego udostępniania wszystkich postów na Facebooka: to zrobi Metricool z osobnym opisem.

## 7. Połączenie z Metricool

Metricool → marka **LAP** (masz już tam Facebook i LinkedIn) → Ustawienia marki / Connections → **Instagram** → „Connect via Facebook” → zaznacz stronę Guliwera 4 i konto Instagram → zatwierdź wszystkie uprawnienia (publikowanie i statystyki).
Napisz mi „Instagram podłączony”, sprawdzę połączenie i najlepsze godziny publikacji.

## 8. Pierwsze 3 dni (zanim pojawi się pierwszy post)

- Zaobserwuj 30–50 kont: klientki, partnerów, polskie konta o finansach i strategii, konta z researchu (`social/research/instagram-konta-zagraniczne.md`).
- Udostępnij link do nowego konta na Facebooku i LinkedIn (tekst przygotuję).
- Wrzuć 3 relacje do wyróżnionej „Start”.
- Potem publikujemy 3 pierwsze posty w 3 kolejne dni (feed nie wygląda pusto), dalej rytm z planu.

## 9. Oznaczanie treści AI (ważne)

Zdjęcia z sesji są częściowo generowane/retuszowane. Zasady Meta (stan na dziś, sprawdzaj przy reklamach, bo się zmieniają):
- Przy publikacji jest przełącznik **„Dodaj etykietę AI”** (Ustawienia zaawansowane). Dla zdjęć generowanych lub mocno zmienionych **włączaj go** – to najbezpieczniejsze i uczciwe wobec odbiorców. Meta może też dodać etykietę sama, jeśli plik ma metadane AI.
- Reklamy płatne: w Menedżerze reklam przy kreacji zaznaczaj informację o treściach cyfrowo wygenerowanych/zmienionych, gdy jest wymagana. Nie używaj zdjęć w reklamach, które mogłyby sugerować coś nieprawdziwego (np. „to moje biuro”, jeśli nim nie jest).
- Grafiki tekstowe (karuzele) i kadrowanie zdjęć nie wymagają etykiety.
