<?php
/**
 * Guliwera 4 – newsletter: treści maili (seria powitalna W1–W7, maile wyzwalane Z1–Z7, potwierdzenie P0).
 * Źródło treści: marketing/NEWSLETTER-AKADEMIA.md. Funkcje nie wołają WooCommerce: dostają gotowe dane.
 *
 * $k (kontakt): imie, email, zrodlo, potwierdzono (ts), token
 * $d (dane):    lekcje, seria, darmowe_ok, darmowe_zostalo, pro, kupon_kod, kupon_proc, kupon_do,
 *               kupil_strategia, kupil_planer, kupil_pakiet, url_potwierdz
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Harmonogram serii powitalnej: kod => dzień po potwierdzeniu. */
function gk_nl_seria() { return array( 'W1' => 0, 'W2' => 1, 'W3' => 3, 'W4' => 6, 'W5' => 8, 'W6' => 10, 'W7' => 14 ); }

/** Maile z ofertą (pomijane przy preferencji „tylko Akademia”). */
function gk_nl_z_oferta() { return array( 'W2', 'W5', 'Z1', 'Z1b', 'Z5', 'Z6b', 'Z7c' ); }

function gk_nl_utm( $url, $kod ) {
	if ( 0 !== strpos( $url, home_url() ) ) { return $url; }
	return add_query_arg( array( 'utm_source' => 'newsletter', 'utm_medium' => 'email', 'utm_campaign' => 'akademia_' . strtolower( $kod ) ), $url );
}

function gk_nl_cz( $k ) { return ! empty( $k['imie'] ) ? 'Cześć, ' . gk_ml_esc( $k['imie'] ) . '!' : 'Cześć!'; }

function gk_nl_p( $html, $styl = '' ) {
	return '<p style="margin:0 0 16px;font-family:Arial,Helvetica,sans-serif;font-size:16px;line-height:1.6;color:#3A2340;' . $styl . '">' . $html . '</p>';
}

function gk_nl_ps( $html ) {
	return '<p style="margin:6px 0 18px;font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.6;color:#7A6580;"><b>P.S.</b> ' . $html . '</p>';
}

function gk_nl_link( $txt, $url ) { return '<a href="' . gk_ml_esc( $url ) . '" style="color:#E8512A;font-weight:bold;">' . gk_ml_esc( $txt ) . '</a>'; }

function gk_nl_powod( $k ) {
	$zr = array(
		'formularz_pakiet'      => 'na stronie Pakietu startowego',
		'akademia_rejestracja'  => 'przy zakładaniu konta w Akademii Guliwera',
		'akademia_profil'       => 'w Profilu Akademii Guliwera',
		'zamowienie'            => 'przy zamówieniu w sklepie',
	);
	$skad = isset( $zr[ $k['zrodlo'] ] ) ? $zr[ $k['zrodlo'] ] : 'na guliwera4.pl';
	$kiedy = ! empty( $k['potwierdzono'] ) ? ' (' . wp_date( 'j.m.Y', (int) $k['potwierdzono'] ) . ')' : '';
	return 'Dostajesz tego maila, bo zapisano ten adres na newsletter Guliwera 4 ' . $skad . $kiedy . '. [[GK_WYPIS]]';
}

/**
 * Zwraca array( temat, treść dla gk_ml_layout() ) albo array( '', array() ), gdy mail nie dotyczy kontaktu.
 */
function gk_nl_tresc( $kod, array $k, array $d ) {
	$akad   = gk_nl_utm( home_url( '/akademia/' ), $kod );
	$pakiet = gk_nl_utm( add_query_arg( 'gk_nl_pakiet', $k['token'], home_url( '/' ) ), $kod );
	$sklep  = function ( $id ) use ( $kod ) {
		$u = function_exists( 'get_permalink' ) && function_exists( 'wc_get_product' ) && wc_get_product( $id ) ? get_permalink( $id ) : home_url( '/materialy/' );
		return gk_nl_utm( $u, $kod );
	};
	$a = array( 'powod' => gk_nl_powod( $k ), 'uwaga_ikona' => '' );

	switch ( $kod ) {
		case 'P0':
			return array( 'Potwierdź zapis i odbierz pakiet', array(
				'preheader'   => 'Jedno kliknięcie i Pakiet startowy jest Twój.',
				'nadtytul'    => 'Ostatni krok',
				'tytul'       => 'Potwierdź zapis',
				'akapity'     => array(
					gk_nl_cz( $k ) . ' Kliknij przycisk, żeby potwierdzić zapis na newsletter Guliwera 4. Zaraz potem wyślę Ci <b>Pakiet startowy Kierunek Zysk</b>: kalkulator zysku, przykład strategii na 1 stronie i przegląd miesiąca w 15 minut.',
				),
				'cta'         => array( 'Potwierdzam i odbieram pakiet', $d['url_potwierdz'] ),
				'uwaga'       => 'Nie zapisujesz się? Zignoruj tę wiadomość. Bez potwierdzenia nie wyślę nic więcej. Link działa przez 7 dni.',
				'uwaga_ikona' => '🔒',
				'powod'       => 'Dostajesz tego maila, bo ktoś podał ten adres przy zapisie na newsletter Guliwera 4.',
			) );

		case 'W1':
			$a += array(
				'preheader' => 'Kalkulator zysku, przykład strategii na 1 stronie i przegląd miesiąca w 15 minut.',
				'nadtytul'  => 'Pakiet startowy Kierunek Zysk',
				'tytul'     => 'Twój Pakiet startowy jest tutaj',
				'akapity'   => array(
					gk_nl_cz( $k ) . ' Tu Angelika z Guliwera 4. Dziękuję za zapis. Obiecany pakiet jest pod przyciskiem, a w środku są 3 pliki:',
				),
				'lista'     => array(
					'<b>Kalkulator zysku</b>: wpisz cenę i koszty, a zobaczysz, ile musisz sprzedać, żeby wyjść na plus.',
					'<b>Strategia na 1 stronie</b>: wypełniony przykład, żeby zobaczyć, jak wygląda gotowa strategia.',
					'<b>Przegląd miesiąca w 15 minut</b>: 7 pytań na ostatni piątek miesiąca.',
				),
				'po'        => gk_nl_p( 'Zacznij od kalkulatora. To 10 minut i zwykle pierwsze „aha” przy marży.' ),
				'cta'       => array( 'Pobierz Pakiet startowy', $pakiet ),
				'pod'       => gk_nl_p( 'Wolisz zacząć od nauki? W Akademii Guliwera czeka lekcja na 5 minut (Lilek już się rozgrzewa): ' . gk_nl_link( ! empty( $d['ma_konto'] ) ? 'Otwórz Akademię' : 'Załóż darmowe konto w Akademii', $akad ) . '.' )
					. gk_nl_p( 'Co dalej: przez 2 tygodnie dostaniesz ode mnie kilka krótkich maili z jedną lekcją albo narzędziem w każdym, potem newsletter co 2 tygodnie we wtorek rano.', 'color:#7A6580;font-size:15px;' ),
				'tekst_pod' => 'Akademia Guliwera: ' . $akad,
			);
			return array( 'Twój Pakiet startowy jest tutaj', $a );

		case 'W2':
			if ( ! empty( $d['kupil_strategia'] ) ) { return array( '', array() ); }
			$a += array(
				'preheader' => '5 pól, które wystarczą, żeby wiedzieć, na co mówisz „nie”.',
				'nadtytul'  => 'Lekcja 1 z 5',
				'tytul'     => 'Jedna kartka zamiast 40 slajdów',
				'akapity'   => array(
					gk_nl_cz( $k ) . ' Większość strategii, które widziałam w firmach, ma 40 slajdów i nikt ich nie czyta po tygodniu. Dobra strategia mieści się na jednej kartce i odpowiada na 5 pytań:',
				),
				'lista'     => array(
					'<b>Dokąd idziemy</b>: cel na rok, liczbą.',
					'<b>Gdzie gramy</b>: klienci, produkty, kanały.',
					'<b>Czym wygrywamy</b>: dlaczego ktoś wybierze właśnie Ciebie.',
					'<b>Czego NIE robimy</b>: najtrudniejsze pole.',
					'<b>Po czym poznamy postęp</b>: 3–5 liczb.',
				),
				'po'        => gk_nl_p( 'Zajrzyj do przykładu z Pakietu startowego i spróbuj wypełnić tylko jedno pole: „czego nie robimy”. To 10 minut, a porządkuje pół kalendarza.' )
					. gk_nl_p( 'Jeśli chcesz zrobić to porządnie dla swojej firmy, mam pełny szablon <b>Strategia na 1 stronie</b> z instrukcją. Kosztuje 39 zł.' ),
				'cta'       => array( 'Zobacz szablon za 39 zł', $sklep( 69 ) ),
				'pod'       => gk_nl_ps( 'W Akademii jest lekcja o MECE: jak dzielić problem na części, żeby nic nie zgubić. ' . gk_nl_link( 'Otwórz lekcję', $akad ) ),
				'tekst_pod' => 'P.S. Lekcja o MECE w Akademii: ' . $akad,
			);
			return array( 'Jedna kartka zamiast 40 slajdów', $a );

		case 'W3':
			$stan = (int) $d['lekcje'] > 0
				? 'Masz za sobą <b>' . (int) $d['lekcje'] . ' ' . gk_nl_lekcji( (int) $d['lekcje'] ) . '</b>, a Twoja seria to <b>' . (int) $d['seria'] . ' ' . gk_nl_dni( (int) $d['seria'] ) . '</b>. Lilek jest dumny.'
				: 'Twoja pierwsza lekcja nadal czeka. Zajmie mniej czasu niż kawa.';
			$a += array(
				'preheader' => 'Mały nawyk, duży efekt w skali roku. Plus jak działa rabat za naukę.',
				'nadtytul'  => 'Lekcja 2 z 5',
				'tytul'     => '5 minut dziennie. Policzyłam, ile to daje',
				'akapity'   => array(
					gk_nl_cz( $k ) . ' 5 minut dziennie to ok. 30 godzin nauki w roku. Tyle co dwa porządne szkolenia, tylko bez wyjazdu i bez slajdów.',
					$stan,
				),
				'po'        => gk_ml_ramka( '<b>Jak działa rabat za naukę?</b><br>Za regularną naukę Akademia daje kupon na materiały Kierunek Zysk. Im więcej lekcji i aktywnych dni w ostatnich 4 tygodniach, tym wyższy rabat: w darmowych działach do ok. 7%, z Akademią Pro do 30%.', '#EDE5FB' )
					. gk_nl_p( 'Wskazówka: ustaw lekcję na stałą porę, np. zaraz po pierwszej kawie. Aplikację dodasz do ekranu telefonu jak zwykłą apkę.' ),
				'cta'       => array( 'Otwórz dzisiejszą lekcję', $akad ),
			);
			return array( '5 minut dziennie. Policzyłam, ile to daje', $a );

		case 'W4':
			$a += array(
				'preheader' => 'Jedno równanie, które warto znać na pamięć. Kalkulator zrobi resztę.',
				'nadtytul'  => 'Lekcja 3 z 5',
				'tytul'     => 'Ile musisz sprzedać, żeby wyjść na zero?',
				'akapity'   => array( gk_nl_cz( $k ) . ' Dziś jedno równanie:' ),
				'po'        => gk_ml_ramka( '<b style="font-size:17px;">Próg rentowności = koszty stałe ÷ (cena − koszt zmienny na sztukę)</b>', '#FFEFC7' )
					. gk_nl_p( 'Przykład: koszty stałe 6 000 zł miesięcznie, usługa za 300 zł, koszt zmienny 100 zł. 6 000 ÷ 200 = <b>30 usług miesięcznie</b>, żeby wyjść na zero. Każda kolejna to już zysk.' )
					. gk_nl_p( 'Najczęstszy błąd: liczenie od przychodu, a nie od marży. Przy marży 20% obniżka ceny o 10% oznacza, że trzeba sprzedać <b>dwa razy więcej</b>, żeby zarobić tyle samo.' )
					. gk_nl_p( 'Wpisz swoje liczby do kalkulatora z Pakietu startowego. Jeśli wynik Cię zaskoczy, odpisz: chętnie zobaczę, co wyszło (anonimowo, bez nazw).' ),
				'cta'       => array( 'Otwórz kalkulator zysku', $pakiet ),
				'pod'       => empty( $d['kupil_pakiet'] ) ? gk_nl_ps( 'W Pakiecie Kierunek Zysk jest Przewodnik, który rozkłada to na czynniki pierwsze. Więcej za 2 dni.' ) : '',
			);
			return array( 'Ile musisz sprzedać, żeby wyjść na zero?', $a );

		case 'W5':
			if ( ! empty( $d['kupil_pakiet'] ) ) { return array( '', array() ); }
			$kupon = ! empty( $d['kupon_kod'] ) && (int) $d['kupon_do'] > time()
				? gk_ml_ramka( '🎟️ Masz aktywny kupon z Akademii <b>' . gk_ml_esc( $d['kupon_kod'] ) . '</b> (−' . (int) $d['kupon_proc'] . '%) do ' . gk_ml_esc( wp_date( 'j.m.Y', (int) $d['kupon_do'] ) ) . '. Działa także na Pakiet.', '#FFEFC7' ) : '';
			$a += array(
				'preheader' => 'Przewodnik, Strategia na 1 stronie, Planer 90 dni, Skrypty, Audyt i Dziennik decyzji.',
				'nadtytul'  => 'Lekcja 4 z 5 · oferta',
				'tytul'     => 'Co jest w Pakiecie Kierunek Zysk',
				'akapity'   => array( gk_nl_cz( $k ) . ' Pakiet łączy wszystkie materiały Kierunek Zysk, które prowadzą od liczb do planu:' ),
				'lista'     => array( 'Przewodnik Kierunek Zysk (147 zł)', 'Strategia na 1 stronie (39 zł)', 'Planer 90 dni (59 zł)', 'Skrypty i szablony sprzedażowe (79 zł)', 'Audyt (59 zł)', 'Dziennik decyzji (49 zł)' ),
				'po'        => gk_nl_p( 'Osobno to 432 zł, w Pakiecie <b>297 zł</b>, czyli 135 zł mniej.' )
					. gk_nl_p( '<b>Dla kogo:</b> właściciele małych firm, osoby na jednoosobowej działalności i młodzi managerowie, którzy chcą mieć plan na papierze, a nie w głowie.' )
					. gk_nl_p( '<b>Dla kogo nie:</b> jeśli w firmie działa już dział strategii i roczny proces planowania. Wtedy Pakiet niewiele doda.' ) . $kupon,
				'cta'       => array( 'Zobacz Pakiet', $sklep( 67 ) ),
			);
			return array( 'Co jest w Pakiecie Kierunek Zysk', $a );

		case 'W6':
			$tematy = array( 'ceny' => 'ceny i marża', 'plan' => 'plan na rok', 'sprzedaz' => 'sprzedaż', 'zespol' => 'zespół', 'czas' => 'czas i priorytety' );
			$linki  = array();
			foreach ( $tematy as $t => $et ) { $linki[] = gk_nl_link( $et, add_query_arg( array( 'gk_nl_temat' => $t, 't' => $k['token'] ), home_url( '/' ) ) ); }
			$a += array(
				'preheader' => 'Czytam każdą odpowiedź. Z nich powstają kolejne lekcje.',
				'nadtytul'  => 'Lekcja 5 z 5',
				'tytul'     => 'Z czym teraz walczysz w firmie?',
				'akapity'   => array(
					gk_nl_cz( $k ) . ' Mam do Ciebie jedno pytanie: z czym teraz najbardziej walczysz w swojej firmie albo zespole?',
					'<b>Odpisz na tego maila jednym zdaniem.</b> Czytam każdą odpowiedź. Najczęstsze tematy zamieniam w lekcje w Akademii i w wydania newslettera.',
					'Wolisz kliknąć? ' . implode( ' · ', $linki ),
				),
				'tekst_po'  => 'Odpisz na tego maila jednym zdaniem.',
			);
			return array( 'Jedno pytanie (odpisz jednym zdaniem)', $a );

		case 'W7':
			$akad_stan = ! empty( $d['darmowe_ok'] )
				? 'Masz za sobą darmowe działy Akademii. Kolejne działy (finanse, metryki, zespół) są w Akademii Pro.'
				: 'Do końca darmowych działów Akademii zostało Ci ' . (int) $d['darmowe_zostalo'] . ' ' . gk_nl_lekcji( (int) $d['darmowe_zostalo'] ) . '.';
			$a += array(
				'preheader' => 'Newsletter co 2 tygodnie we wtorek. Częstotliwość zmienisz jednym kliknięciem.',
				'nadtytul'  => 'Co dalej',
				'tytul'     => 'Dziękuję za te 2 tygodnie',
				'akapity'   => array(
					gk_nl_cz( $k ) . ' Od teraz piszę co 2 tygodnie, we wtorek o 7:30: jeden temat ze strategii, jedno narzędzie, jedna lekcja z Akademii i jedna oferta, zawsze oznaczona.',
					$akad_stan,
					'Chcesz rzadziej? Ustaw „raz w miesiącu” albo „tylko Akademia, bez ofert”.',
				),
				'cta'       => array( 'Ustaw, jak często piszę', add_query_arg( 'gk_nl_pref', $k['token'], home_url( '/' ) ) ),
				'cta2'      => array( 'Otwórz Akademię', $akad ),
			);
			return array( 'Co dalej (i jak często będę pisać)', $a );

		case 'Z1':
		case 'Z1b':
			if ( ! empty( $d['pro'] ) ) { return array( '', array() ); }
			$pro = ! empty( $d['url_pro'] ) ? $d['url_pro'] : $akad;
			$a  += array(
				'preheader' => 'Kolejne działy: finanse, metryki, zespół. Akademia Pro na 12 miesięcy.',
				'nadtytul'  => 'Akademia Guliwera',
				'tytul'     => 'Z1' === $kod ? 'Darmowe działy ukończone!' : 'Akademia Pro: przypomnienie',
				'akapity'   => array(
					'Z1' === $kod
						? gk_nl_cz( $k ) . ' Gratulacje: darmowe działy Akademii masz za sobą. To <b>' . (int) $d['lekcje'] . ' ' . gk_nl_lekcji( (int) $d['lekcje'] ) . '</b>.'
						: gk_nl_cz( $k ) . ' Krótko, bo obiecałam nie naciskać: Akademia Pro dalej czeka.',
					'Akademia Pro odblokowuje wszystkie kolejne działy i nowe lekcje dodawane w ciągu roku, zdejmuje limit serc i daje certyfikat ukończenia. Lekcje z Pro liczą się też do rabatu na materiały (do 30% zamiast ok. 7%).',
					'<b>249 zł za 12 miesięcy</b>, czyli ok. 68 gr dziennie za 5 minut nauki.',
				),
				'cta'       => array( 'Odblokuj Akademię Pro', gk_nl_utm( $pro, $kod ) ),
			);
			return array( 'Z1' === $kod ? 'Ukończone 2 działy Akademii. Co dalej?' : 'Akademia Pro: ostatnie przypomnienie', $a );

		case 'Z2_7':
		case 'Z2_30':
			$n  = 'Z2_7' === $kod ? 7 : 30;
			$a += array(
				'preheader' => $n . ' dni nauki z rzędu. Krótka gratulacja, bez oferty.',
				'nadtytul'  => 'Seria ' . $n . ' dni',
				'tytul'     => $n . ' dni z rzędu. Szacun!',
				'akapity'   => array( gk_nl_cz( $k ) . ' ' . $n . ' dni nauki z rzędu. Lilek zrobił z tej okazji taniec, którego nie pokażemy. Jutro dzień ' . ( $n + 1 ) . '?' ),
				'cta'       => array( 'Otwórz lekcję', $akad ),
			);
			return array( $n . ' dni z rzędu. Szacun!', $a );

		case 'Z3':
			$a += array(
				'preheader' => 'Twoja seria czeka. Jedna lekcja i wracasz do gry.',
				'nadtytul'  => 'Akademia Guliwera',
				'tytul'     => 'Wracasz na 5 minut?',
				'akapity'   => array(
					gk_nl_cz( $k ) . ' Tydzień przerwy to nic złego. Najtrudniejsza jest pierwsza lekcja po przerwie, więc zrób dziś tylko jedną.',
					'Rabat za naukę liczy aktywne dni z ostatnich 4 tygodni, więc po przerwie trochę spada. Wraca, gdy wracasz Ty.',
				),
				'cta'       => array( 'Wracam do lekcji', $akad ),
			);
			return array( 'Wracasz na 5 minut?', $a );

		case 'Z4':
			$a += array(
				'preheader' => 'Bez presji. Możesz też zmienić, jak często piszę.',
				'nadtytul'  => 'Akademia Guliwera',
				'tytul'     => 'Zostawić Ci miejsce w Akademii?',
				'akapity'   => array(
					gk_nl_cz( $k ) . ' Nie było Cię w Akademii od 3 tygodni. Rozumiem, kalendarz bywa bezlitosny.',
					'Masz 3 opcje: wracasz na lekcję, zostajesz tylko przy newsletterze co 2 tygodnie albo ' . gk_nl_link( 'wybierasz „raz w miesiącu”', add_query_arg( 'gk_nl_pref', $k['token'], home_url( '/' ) ) ) . '.',
				),
				'cta'       => array( 'Wracam do lekcji', $akad ),
			);
			return array( 'Zostawić Ci miejsce w Akademii?', $a );

		case 'Z5':
			if ( empty( $d['kupon_kod'] ) ) { return array( '', array() ); }
			$a += array(
				'preheader' => 'Kod ' . $d['kupon_kod'] . ' działa do ' . wp_date( 'j.m.Y, H:i', (int) $d['kupon_do'] ) . '.',
				'nadtytul'  => 'Kupon z Akademii',
				'tytul'     => 'Twój kupon wygasa pojutrze',
				'akapity'   => array( gk_nl_cz( $k ) . ' Twój kupon z Akademii <b>' . gk_ml_esc( $d['kupon_kod'] ) . '</b> (−' . (int) $d['kupon_proc'] . '%) działa jeszcze do <b>' . gk_ml_esc( wp_date( 'j.m.Y, H:i', (int) $d['kupon_do'] ) ) . '</b>. Przycisk od razu dodaje go do koszyka.' ),
				'cta'       => array( 'Użyj kuponu', gk_nl_utm( home_url( '/koszyk/?gk_kupon=' . rawurlencode( $d['kupon_kod'] ) ), $kod ) ),
			);
			return array( 'Twój kupon −' . (int) $d['kupon_proc'] . '% wygasa pojutrze', $a );

		case 'Z6a':
			$a += array(
				'preheader' => 'Kolejność pól i najczęstsze błędy.',
				'nadtytul'  => 'Strategia na 1 stronie',
				'tytul'     => 'Jak wypełnić Strategię w 45 minut',
				'akapity'   => array(
					gk_nl_cz( $k ) . ' Dziękuję za zakup szablonu Strategia na 1 stronie. Kilka wskazówek, żeby poszło szybko:',
				),
				'lista'     => array( 'Zacznij od pola „czego nie robimy”. Reszta pójdzie łatwiej.', 'Potem cel na rok, liczbą.', 'Na końcu 3–5 liczb, po których poznasz postęp. Mniej znaczy lepiej.', 'Pokaż kartkę jednej zaufanej osobie i zapytaj: „czego tu brakuje?”.' ),
				'cta'       => array( 'Pobierz swój plik', gk_nl_utm( function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'downloads' ) : home_url( '/moje-konto/' ), $kod ) ),
			);
			return array( 'Jak wypełnić Strategię w 45 minut', $a );

		case 'Z6b':
			if ( ! empty( $d['kupil_planer'] ) || ! empty( $d['kupil_pakiet'] ) ) { return array( '', array() ); }
			$a += array(
				'preheader' => 'Strategia mówi dokąd. Planer 90 dni mówi, co robisz w tym kwartale.',
				'nadtytul'  => 'Następny krok',
				'tytul'     => 'Ze strategii do planu na 90 dni',
				'akapity'   => array(
					gk_nl_cz( $k ) . ' Masz już strategię na jednej kartce. Następny krok to zamienić ją w plan na najbliższe 90 dni: 3 priorytety, zadania na tygodnie i krótki przegląd co piątek.',
					'Do tego służy <b>Planer 90 dni</b> (59 zł).',
				),
				'cta'       => array( 'Zobacz Planer 90 dni', $sklep( 70 ) ),
			);
			return array( 'Następny krok: Planer 90 dni', $a );

		case 'Z6c':
		case 'Z7b':
			$a += array(
				'preheader' => 'Jedno zdanie opinii bardzo mi pomoże.',
				'nadtytul'  => 'Twoja opinia',
				'tytul'     => 'Jak Ci poszło?',
				'akapity'   => array(
					gk_nl_cz( $k ) . ' Minęło kilka dni od zakupu. Jak Ci poszło z materiałem? Co było najbardziej przydatne, a czego zabrakło?',
					'<b>Odpisz jednym zdaniem.</b> Czytam wszystko, a za zgodą mogę pokazać Twoją opinię na stronie (bez nazwiska, jeśli wolisz).',
				),
				'tekst_po'  => 'Odpisz na tego maila jednym zdaniem.',
			);
			return array( 'Jak Ci poszło? (jedno zdanie wystarczy)', $a );

		case 'Z7a':
			$a += array(
				'preheader' => 'Kolejność materiałów na 4 tygodnie.',
				'nadtytul'  => 'Pakiet Kierunek Zysk',
				'tytul'     => 'Od czego zacząć w Pakiecie',
				'akapity'   => array( gk_nl_cz( $k ) . ' Dziękuję za zakup Pakietu. Żeby nie utonąć w plikach, proponuję taki plan na 4 tygodnie:' ),
				'lista'     => array( '<b>Tydzień 1:</b> Przewodnik i Audyt: gdzie jesteś dziś.', '<b>Tydzień 2:</b> Strategia na 1 stronie: dokąd idziesz.', '<b>Tydzień 3:</b> Planer 90 dni: co robisz w tym kwartale.', '<b>Tydzień 4:</b> Skrypty i Dziennik decyzji: sprzedaż i nawyk decyzji.' ),
				'cta'       => array( 'Pobierz swoje pliki', gk_nl_utm( function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'downloads' ) : home_url( '/moje-konto/' ), $kod ) ),
			);
			return array( 'Od czego zacząć w Pakiecie', $a );

		case 'Z7c':
			$a += array(
				'preheader' => 'Osoby z listy oczekujących dostaną cenę przedsprzedaży.',
				'nadtytul'  => 'Kurs wideo',
				'tytul'     => 'Nagrywam kurs Strategia w 6 tygodni',
				'akapity'   => array(
					gk_nl_cz( $k ) . ' Skoro masz Pakiet, ten temat może Cię zainteresować: nagrywam kurs wideo <b>Strategia w 6 tygodni</b>. Krok po kroku od diagnozy do strategii na 1 stronie i planu 90 dni.',
					'Osoby z listy oczekujących dostaną cenę przedsprzedaży i informację o starcie jako pierwsze.',
				),
				'cta'       => array( 'Zapisz się na listę oczekujących', gk_nl_utm( home_url( '/kurs-strategia/' ), $kod ) ),
			);
			return array( 'Kurs wideo: lista oczekujących', $a );
	}
	return array( '', array() );
}

function gk_nl_lekcji( $n ) { return 1 === $n ? 'lekcja' : ( ( $n % 10 >= 2 && $n % 10 <= 4 && ( $n % 100 < 10 || $n % 100 >= 20 ) ) ? 'lekcje' : 'lekcji' ); }
function gk_nl_dni( $n ) { return 1 === $n ? 'dzień' : 'dni'; }

/** Opisy maili do panelu i podglądu. */
function gk_nl_opisy() {
	return array(
		'P0' => 'Potwierdzenie zapisu (double opt-in)', 'W1' => 'Seria · dzień 0: Pakiet startowy', 'W2' => 'Seria · dzień 1: Strategia na 1 stronie (39 zł)',
		'W3' => 'Seria · dzień 3: nawyk i rabat', 'W4' => 'Seria · dzień 6: próg rentowności', 'W5' => 'Seria · dzień 8: Pakiet 297 zł',
		'W6' => 'Seria · dzień 10: pytanie', 'W7' => 'Seria · dzień 14: co dalej', 'Z1' => 'Darmowe działy ukończone → Akademia Pro', 'Z1b' => 'Akademia Pro: przypomnienie po 3 dniach',
		'Z2_7' => 'Seria 7 dni', 'Z2_30' => 'Seria 30 dni', 'Z3' => '7 dni bez lekcji', 'Z4' => '21 dni bez lekcji', 'Z5' => 'Kupon wygasa za 2 dni',
		'Z6a' => 'Po zakupie Strategii · dzień 1', 'Z6b' => 'Po zakupie Strategii · dzień 7: Planer', 'Z6c' => 'Po zakupie Strategii · dzień 14: opinia',
		'Z7a' => 'Po zakupie Pakietu · dzień 2', 'Z7b' => 'Po zakupie Pakietu · dzień 10: opinia', 'Z7c' => 'Po zakupie Pakietu · dzień 21: kurs',
	);
}
