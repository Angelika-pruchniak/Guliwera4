<?php
/**
 * Wydania newslettera tygodniowego (czwartek, Mailchimp). Struktura z kalendarza treści (main, marketing/Guliwera4_Kalendarz_tresci_XI2026-I2027.xlsx):
 * 1) wpis tygodnia z bloga, 2) ćwiczenie na 10 minut, 3) produkt tygodnia (jedyny przycisk); Akademia w stopce.
 * Render: php narzedzia/wydanie-newslettera.php 2026-11-26  → marketing/newsletter/2026-11-26.html (do wklejenia w Mailchimp: „Paste in code”) i .txt
 */
return array(
	'2026-11-26' => array(
		'kampania'  => 'black-week-bez-rabatow-strat',
		'temat_a'   => '3 decyzje, które dadzą Ci więcej niż rabat',
		'temat_b'   => 'Black Week bez wyprzedaży? Da się',
		'preheader' => 'Zamiast obniżać ceny: wartość, stali klienci i jedna liczba, którą warto policzyć.',
		'tytul'     => 'Black Week bez wyprzedaży',
		'wstep'     => '*|IF:FNAME|*Cześć, *|FNAME|*!*|ELSE:|*Cześć!*|END:IF|* W tym tygodniu wszyscy obniżają ceny. Zanim zrobisz to samo, policz, ile Cię to kosztuje. Mam dla Ciebie 3 decyzje, które zwykle dają więcej niż rabat.',
		'wpis'      => array(
			'tytul' => 'Black Friday w małej firmie: jak zarobić, nie obniżając cen',
			'url'   => 'https://guliwera4.pl/blog/', // ← podmienić na adres wpisu z 24.11 po publikacji
			'tresc' => array(
				'<b>1. Dodaj wartość zamiast odejmować cenę.</b> Bonus, pakiet albo szybszy termin kosztują Cię mniej niż 20% z każdej sprzedaży.',
				'<b>2. Napisz do tych, którzy już kupili.</b> 10 ostatnich klientów zna Twoją jakość. Krótki mail z propozycją kolejnego kroku działa lepiej niż reklama dla obcych.',
				'<b>3. Policz marżę, zanim dasz rabat.</b> Przy marży 40% obniżka o 20% oznacza, że trzeba sprzedać <b>dwa razy więcej</b>, żeby zarobić tyle samo.',
			),
		),
		'cwiczenie' => array(
			'tytul' => 'Ćwiczenie na 10 minut: rabat czy wartość?',
			'kroki' => array(
				'Otwórz Kalkulator zysku z Pakietu startowego i wpisz cenę oraz koszt swojego najlepiej sprzedającego się produktu albo usługi.',
				'W tabeli „Co jeśli zmienisz cenę?” sprawdź, ile więcej musisz sprzedać przy obniżce o 10% i 20%.',
				'Wypisz 3 rzeczy, które możesz dodać zamiast obniżki (bonus, pakiet, termin, konsultacja). Wybierz jedną i zaproponuj ją klientom w tym tygodniu.',
			),
		),
		'produkt'   => array(
			'nazwa' => 'Strategia na 1 stronie',
			'cena'  => '39 zł',
			'opis'  => 'Jeśli Black Week pokazał Ci, że brakuje jasnych decyzji: cena, klient, czego nie robisz. Pełny szablon do wypełnienia dla własnej firmy, z instrukcją. Bez rabatu, bo to i tak najtańszy krok do planu na 2027.',
			'cta'   => 'Zobacz Strategię na 1 stronie',
			'url'   => 'https://guliwera4.pl/produkt/strategia-na-1-stronie/',
		),
		'ps'        => 'Odpisz jednym zdaniem: obniżasz ceny w tym tygodniu czy nie? Najciekawsze odpowiedzi (anonimowo) pokażę w kolejnym wydaniu.',
	),
);
