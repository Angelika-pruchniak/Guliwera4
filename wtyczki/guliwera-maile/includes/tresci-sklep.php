<?php
/**
 * Guliwera 4: treści maili o zamówieniach, kuponie z Akademii i powiadomień dla administratora.
 *
 * Funkcje w tym pliku nie korzystają z WooCommerce. Dostają gotową tablicę danych
 * (patrz gk_ml_dane_wc() w sklep.php albo gk_ml_przyklad() w podglad.php), dzięki czemu
 * podgląd każdego maila da się zbudować bez prawdziwego zamówienia.
 *
 * Tablica danych zamówienia ($d):
 *   numer, data, imie, email, pozycje [[nazwa, ilosc, kwota]], podsumowanie [[etykieta, wartosc]],
 *   suma, metoda, pliki [[nazwa, url, pozostalo ('' = bez limitu), wazne_do ('' = bez terminu)]],
 *   konto (bool), url_pliki, url_zamowienia, url_platnosci, url_ponownie, url_admin,
 *   zgoda (znacznik czasu albo 0), faktura (null albo [nip, dane]), notatka_klienta, instrukcje (HTML bramki płatności)
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Treść pola zgody w zamówieniu. Ten sam tekst cytujemy w mailu jako potwierdzenie (art. 21 ust. 1 i art. 38 ust. 1 pkt 13 u.p.k.). */
define( 'GK_ML_ZGODA_TEKST', 'Żądam dostarczenia plików od razu po zapłacie, przed upływem 14 dni na odstąpienie od umowy. Przyjmuję do wiadomości, że z chwilą rozpoczęcia dostarczania tracę prawo do odstąpienia od umowy.' );

define( 'GK_ML_POWOD_SKLEP', 'To wiadomość o Twoim zamówieniu w sklepie guliwera4.pl, dlatego otrzymujesz ją niezależnie od zgód marketingowych.' );

/* ─────────────────────────── Klocki HTML ─────────────────────────── */
function gk_ml_ramka( $html, $bg = '#FFF6EE', $odstep = 20 ) {
	return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 ' . (int) $odstep . 'px;"><tr><td bgcolor="' . $bg . '" style="background:' . $bg . ';border-radius:14px;padding:14px 18px;font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.6;color:#3A2340;">' . $html . '</td></tr></table>';
}

function gk_ml_akapit( $html, $rozmiar = 16, $kolor = '#3A2340' ) {
	return '<p style="margin:0 0 16px;font-family:Arial,Helvetica,sans-serif;font-size:' . $rozmiar . 'px;line-height:1.6;color:' . $kolor . ';">' . $html . '</p>';
}

function gk_ml_naglowek( $txt ) {
	return '<h2 style="margin:26px 0 12px;font-family:Georgia,\'Times New Roman\',serif;font-size:19px;line-height:1.25;font-weight:bold;color:#3A2340;">' . gk_ml_esc( $txt ) . '</h2>';
}

/** Tabela pozycji i podsumowania zamówienia. */
function gk_ml_tabela( array $d ) {
	$f = 'font-family:Arial,Helvetica,sans-serif;color:#3A2340;';
	$h = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 20px;border-collapse:collapse;">';
	$h .= '<tr><td colspan="2" style="' . $f . 'font-size:13px;font-weight:bold;letter-spacing:1px;text-transform:uppercase;color:#7A6580;padding:0 0 8px;border-bottom:2px solid #F3E3D6;">Zamówienie nr ' . gk_ml_esc( $d['numer'] ) . ' · ' . gk_ml_esc( $d['data'] ) . '</td></tr>';
	foreach ( $d['pozycje'] as $p ) {
		$ile = (int) $p[1] > 1 ? ' × ' . (int) $p[1] : '';
		$h  .= '<tr><td style="' . $f . 'font-size:15.5px;line-height:1.4;padding:12px 12px 12px 0;border-bottom:1px solid #F3E3D6;"><b>' . gk_ml_esc( $p[0] ) . '</b>' . $ile . '</td>';
		$h  .= '<td align="right" style="' . $f . 'font-size:15.5px;padding:12px 0;border-bottom:1px solid #F3E3D6;white-space:nowrap;">' . gk_ml_esc( $p[2] ) . '</td></tr>';
	}
	foreach ( $d['podsumowanie'] as $i => $r ) {
		$ost = $i === count( $d['podsumowanie'] ) - 1;
		$s   = $ost ? 'font-size:17px;font-weight:bold;padding:12px 0 0;' : 'font-size:14.5px;color:#7A6580;padding:8px 0 0;';
		$h  .= '<tr><td style="' . $f . $s . '">' . gk_ml_esc( $r[0] ) . '</td><td align="right" style="' . $f . $s . 'white-space:nowrap;">' . gk_ml_esc( $r[1] ) . '</td></tr>';
	}
	return $h . '</table>';
}

function gk_ml_tabela_tekst( array $d ) {
	$t = 'ZAMÓWIENIE NR ' . $d['numer'] . ' · ' . $d['data'] . "\n";
	foreach ( $d['pozycje'] as $p ) { $t .= '- ' . $p[0] . ( (int) $p[1] > 1 ? ' × ' . (int) $p[1] : '' ) . ': ' . $p[2] . "\n"; }
	foreach ( $d['podsumowanie'] as $r ) { $t .= $r[0] . ' ' . $r[1] . "\n"; }
	return $t;
}

function gk_ml_limit_pliku( array $p ) {
	$x = array();
	$x[] = '' === (string) $p[2] ? 'bez limitu pobrań' : 'pozostało pobrań: ' . (int) $p[2];
	$x[] = '' === (string) $p[3] ? 'link bez terminu ważności' : 'link ważny do ' . $p[3];
	return implode( ' · ', $x );
}

/** Lista plików do pobrania: nazwa, limit, ważność i link przy każdym pliku. */
function gk_ml_pliki( array $d ) {
	$f = 'font-family:Arial,Helvetica,sans-serif;color:#3A2340;';
	$h = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 18px;">';
	foreach ( $d['pliki'] as $i => $p ) {
		$typ = strtoupper( pathinfo( wp_parse_url( $p[1], PHP_URL_PATH ) ?: '', PATHINFO_EXTENSION ) );
		$typ = preg_match( '/^(PDF|ZIP|XLSX)$/', $typ ) ? $typ : ( false !== stripos( $p[0], 'pakiet' ) ? 'ZIP' : 'PDF' );
		$kol = array( '#FFD8C2', '#FFD9E4', '#EDE5FB', '#FFEFC7' );
		$h  .= '<tr><td width="52" valign="top" style="padding:0 14px 14px 0;"><div style="width:52px;height:52px;line-height:52px;border-radius:12px;text-align:center;background:' . $kol[ $i % 4 ] . ';' . $f . 'font-size:12px;font-weight:bold;letter-spacing:.5px;">' . $typ . '</div></td>';
		$h  .= '<td valign="top" style="padding:2px 0 14px;' . $f . '"><b style="font-size:15.5px;line-height:1.35;">' . gk_ml_esc( $p[0] ) . '</b><br>';
		$h  .= '<span style="font-size:13px;color:#7A6580;">' . gk_ml_esc( gk_ml_limit_pliku( $p ) ) . '</span><br>';
		$h  .= count( $d['pliki'] ) > 1 ? '<a href="' . gk_ml_esc( $p[1] ) . '" style="font-size:15px;font-weight:bold;color:#E8512A;">Pobierz ' . ( 'ZIP' === $typ ? 'plik ZIP' : 'plik' ) . ' →</a>' : '';
		$h  .= '</td></tr>';
	}
	return $h . '</table>';
}

function gk_ml_pliki_tekst( array $d ) {
	$t = "TWOJE PLIKI\n";
	foreach ( $d['pliki'] as $p ) { $t .= '- ' . $p[0] . ' (' . gk_ml_limit_pliku( $p ) . "):\n  " . $p[1] . "\n"; }
	return $t;
}

/** Główny przycisk przy plikach: jeden plik → link bezpośredni, kilka → Moje konto → Pliki albo strona zamówienia. */
function gk_ml_cta_pliki( array $d ) {
	if ( 1 === count( $d['pliki'] ) ) {
		$zip = false !== stripos( $d['pliki'][0][1] . $d['pliki'][0][0], 'zip' ) || false !== stripos( $d['pliki'][0][0], 'pakiet' );
		return array( $zip ? 'Pobierz pakiet (ZIP)' : 'Pobierz PDF', $d['pliki'][0][1] );
	}
	return array( 'Pobierz pliki', $d['konto'] ? $d['url_pliki'] : $d['url_zamowienia'] );
}

function gk_ml_gdzie_pliki( array $d ) {
	return $d['konto']
		? 'Pliki są też zawsze w <b>Moje konto → Pliki</b> (<a href="' . gk_ml_esc( $d['url_pliki'] ) . '" style="color:#E8512A;">guliwera4.pl/moje-konto/downloads</a>). Zaloguj się adresem ' . gk_ml_esc( $d['email'] ) . '.'
		: 'Zamówienie złożono bez konta, więc <b>zachowaj ten mail</b>: linki działają bez logowania. Pliki są też na <a href="' . gk_ml_esc( $d['url_zamowienia'] ) . '" style="color:#E8512A;">stronie Twojego zamówienia</a>.';
}

/** Potwierdzenie zgody na natychmiastowe dostarczenie (trwały nośnik). */
function gk_ml_zgoda( array $d ) {
	if ( empty( $d['zgoda'] ) ) { return ''; }
	return gk_ml_ramka(
		'<b>Potwierdzenie Twojej zgody</b><br>'
		. '<span style="font-size:14px;">Przy zamówieniu nr ' . gk_ml_esc( $d['numer'] ) . ' (' . gk_ml_esc( gk_ml_data( $d['zgoda'] ) ) . ') zaznaczono oświadczenie:</span><br>'
		. '<i style="font-size:14px;">„' . gk_ml_esc( GK_ML_ZGODA_TEKST ) . '”</i><br>'
		. '<span style="font-size:13px;color:#7A6580;">Podstawa: art. 38 ust. 1 pkt 13 ustawy o prawach konsumenta i §6 Regulaminu sklepu.</span>',
		'#EDE5FB'
	);
}

function gk_ml_zgoda_tekst( array $d ) {
	if ( empty( $d['zgoda'] ) ) { return ''; }
	return "POTWIERDZENIE TWOJEJ ZGODY\nPrzy zamówieniu nr " . $d['numer'] . ' (' . gk_ml_data( $d['zgoda'] ) . ") zaznaczono oświadczenie:\n„" . GK_ML_ZGODA_TEKST . "”\nPodstawa: art. 38 ust. 1 pkt 13 ustawy o prawach konsumenta i §6 Regulaminu sklepu.\n";
}

/** Informacja o fakturze dla klienta. */
function gk_ml_faktura_klient( array $d ) {
	if ( ! empty( $d['faktura'] ) ) {
		return 'Fakturę bez VAT' . ( $d['faktura'][0] ? ' na NIP ' . gk_ml_esc( $d['faktura'][0] ) : '' ) . ' wyślemy osobnym mailem w ciągu 2 dni roboczych.';
	}
	return 'Potrzebujesz faktury? Odpisz na tego maila i podaj dane do faktury (nazwa, adres, NIP).';
}

/** Najważniejsze informacje z regulaminu: licencja, reklamacje, faktura. Mały druk pod przyciskiem. */
function gk_ml_informacje( array $d, $z_faktura = true, $o_plikach = true ) {
	$li  = array();
	if ( $z_faktura ) { $li[] = gk_ml_faktura_klient( $d ); }
	if ( $o_plikach ) {
	$li[] = 'Licencja: z materiałów możesz korzystać na własne potrzeby i w jednej firmie. Prosimy, nie udostępniaj plików innym osobom.';
	$li[] = 'Plik się nie otwiera albo link wygasł? Odpisz na tego maila, a bezpłatnie wyślemy nowy (odpowiadamy w ciągu 14 dni, zwykle tego samego dnia).';
	}
	$li[] = 'Zasady zakupu: <a href="' . gk_ml_esc( home_url( '/regulamin/' ) ) . '" style="color:#E8512A;">Regulamin sklepu</a>.';
	$h = gk_ml_naglowek( 'Dobrze wiedzieć' ) . '<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 18px;">';
	foreach ( $li as $x ) {
		$h .= '<tr><td valign="top" style="padding:0 10px 8px 0;font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#E8512A;">●</td><td style="padding:0 0 8px;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.55;color:#3A2340;">' . $x . '</td></tr>';
	}
	return $h . '</table>';
}

function gk_ml_informacje_tekst( array $d, $z_faktura = true, $o_plikach = true ) {
	$t = "DOBRZE WIEDZIEĆ\n";
	if ( $z_faktura ) { $t .= '- ' . gk_ml_bez_html( gk_ml_faktura_klient( $d ) ) . "\n"; }
	if ( $o_plikach ) {
		$t .= "- Licencja: z materiałów możesz korzystać na własne potrzeby i w jednej firmie. Prosimy, nie udostępniaj plików innym osobom.\n";
		$t .= "- Plik się nie otwiera albo link wygasł? Odpisz na tego maila, a bezpłatnie wyślemy nowy.\n";
	}
	$t .= '- Regulamin sklepu: ' . home_url( '/regulamin/' ) . "\n";
	return $t;
}

function gk_ml_data( $ts ) { return wp_date( 'j.m.Y, \o H:i', (int) $ts ); }

function gk_ml_witaj( array $d ) { return $d['imie'] ? 'Cześć, ' . $d['imie'] . '!' : 'Cześć!'; }

/* ─────────────────────────── Maile do klienta ─────────────────────────── */

/**
 * Zwraca array( temat, treść dla gk_ml_layout() ).
 * $typ: oczekuje | oplacone | pliki | nieudane | anulowane | zwrot | zwrot_czesc | notatka | szczegoly | odstapienie
 */
function gk_ml_mail_zamowienie( $typ, array $d, array $x = array() ) {
	$n = $d['numer'];
	$a = array( 'powod' => GK_ML_POWOD_SKLEP, 'uwaga_ikona' => '' );

	switch ( $typ ) {
		case 'oczekuje':
			$temat = 'Zamówienie nr ' . $n . ' czeka na płatność';
			$a    += array(
				'preheader' => 'Gdy płatność dotrze, od razu wyślemy pliki do pobrania.',
				'nadtytul'  => 'Zamówienie przyjęte',
				'tytul'     => 'Dziękujemy! Czekamy na płatność',
				'akapity'   => array(
					gk_ml_witaj( $d ) . ' Zamówienie nr <b>' . gk_ml_esc( $n ) . '</b> jest zapisane. Płatność (' . gk_ml_esc( $d['metoda'] ) . ') nie jest jeszcze potwierdzona przez operatora.',
					'Przy BLIK-u i Przelewach24 zwykle trwa to kilka minut. Gdy tylko płatność dotrze, dostaniesz drugi mail z linkami do pobrania plików.',
				),
				'po'        => ( $d['instrukcje'] ? $d['instrukcje'] : '' ) . gk_ml_tabela( $d ),
				'cta'       => array( 'Sprawdź status zamówienia', $d['url_zamowienia'] ),
				'pod'       => gk_ml_zgoda( $d ) . gk_ml_informacje( $d, true, false ),
				'uwaga'     => '<b>Płatność się nie udała albo przerwano ją w połowie?</b> <a href="' . gk_ml_esc( $d['url_platnosci'] ) . '" style="color:#E8512A;font-weight:bold;">Zapłać ponownie</a>. Zamówienie poczeka na Ciebie kilka dni.',
			);
			$a['tekst_po']  = gk_ml_tabela_tekst( $d );
			$a['tekst_pod'] = gk_ml_zgoda_tekst( $d ) . "\n" . gk_ml_informacje_tekst( $d, true, false );
			break;

		case 'oplacone':
		case 'pliki':
		case 'szczegoly':
			if ( 'szczegoly' === $typ && empty( $d['zaplacone'] ) ) {
				$temat = 'Zapłać za zamówienie nr ' . $n;
				$a    += array(
					'preheader' => 'Kliknij, żeby dokończyć płatność. Pliki dostaniesz od razu po zapłacie.',
					'nadtytul'  => 'Zamówienie czeka na płatność',
					'tytul'     => 'Dokończ zamówienie',
					'akapity'   => array( gk_ml_witaj( $d ) . ' Oto szczegóły zamówienia nr <b>' . gk_ml_esc( $n ) . '</b>. Kliknij przycisk, żeby za nie zapłacić kartą, BLIK-iem albo Przelewami24.' ),
					'po'        => gk_ml_tabela( $d ),
					'cta'       => array( 'Zapłać ' . $d['suma'], $d['url_platnosci'] ),
					'pod'       => gk_ml_informacje( $d ),
				);
				$a['tekst_po']  = gk_ml_tabela_tekst( $d );
				$a['tekst_pod'] = gk_ml_informacje_tekst( $d );
				break;
			}
			$sa_pliki = ! empty( $d['pliki'] );
			$wiele    = count( $d['pliki'] ) > 1;
			if ( 'oplacone' === $typ ) {
				$temat = 'Płatność przyjęta · zamówienie nr ' . $n;
			} elseif ( 'szczegoly' === $typ ) {
				$temat = 'Szczegóły zamówienia nr ' . $n;
			} else {
				$temat = gk_ml_tytul_pliki( $d ) . ' · zamówienie nr ' . $n;
			}
			$a += array(
				'preheader' => $sa_pliki ? 'Pobierz ' . ( $wiele ? 'pliki' : 'plik' ) . ' teraz. Link działa ' . gk_ml_limit_krotko( $d ) . '.' : 'Płatność dotarła. Dziękujemy za zakup!',
				'nadtytul'  => 'oplacone' === $typ ? 'Płatność przyjęta' : 'Zamówienie zrealizowane',
				'tytul'     => $sa_pliki ? gk_ml_tytul_pliki( $d ) : 'Dziękujemy za zakup!',
				'akapity'   => array(
					gk_ml_witaj( $d ) . ' Płatność za zamówienie nr <b>' . gk_ml_esc( $n ) . '</b> dotarła. Dziękujemy, że korzystasz z materiałów Kierunek Zysk.',
				),
			);
			if ( $sa_pliki ) {
				$a['akapity'][] = 'Pobierz ' . ( $wiele ? 'pliki' : 'plik' ) . ' na komputer albo telefon i zapisz w swoim folderze. Każdy link ma limit pobrań i termin ważności, podane przy pliku.';
				$a['po']        = gk_ml_pliki( $d );
				$a['cta']       = gk_ml_cta_pliki( $d );
				$a['pod']       = gk_ml_akapit( gk_ml_gdzie_pliki( $d ), 14.5 )
					. gk_ml_ramka( '<b>Jak zacząć?</b><br>PDF otworzysz w przeglądarce albo w darmowym Adobe Acrobat Reader. Pola do uzupełniania wypełnisz na komputerze, możesz też wydrukować arkusze. Plik ZIP rozpakujesz dwuklikiem.', '#FFF6EE' )
					. gk_ml_naglowek( 'Podsumowanie' ) . gk_ml_tabela( $d ) . gk_ml_zgoda( $d ) . gk_ml_informacje( $d );
				$a['tekst_po']  = gk_ml_pliki_tekst( $d );
				$a['tekst_pod'] = gk_ml_bez_html( gk_ml_gdzie_pliki( $d ) ) . "\n\n" . gk_ml_tabela_tekst( $d ) . "\n" . gk_ml_zgoda_tekst( $d ) . "\n" . gk_ml_informacje_tekst( $d );
			} else {
				$a['akapity'][] = 'Szczegóły zamówienia znajdziesz poniżej i na stronie zamówienia.';
				$a['po']        = gk_ml_tabela( $d );
				$a['cta']       = array( 'Zobacz zamówienie', $d['url_zamowienia'] );
				$a['pod']       = gk_ml_zgoda( $d ) . gk_ml_informacje( $d );
				$a['tekst_po']  = gk_ml_tabela_tekst( $d );
				$a['tekst_pod'] = gk_ml_zgoda_tekst( $d ) . "\n" . gk_ml_informacje_tekst( $d );
			}
			break;

		case 'nieudane':
			$temat = 'Płatność nie przeszła · zamówienie nr ' . $n;
			$a    += array(
				'preheader' => 'Nic się nie stało: możesz zapłacić ponownie jednym kliknięciem.',
				'nadtytul'  => 'Płatność nieudana',
				'tytul'     => 'Płatność nie przeszła',
				'akapity'   => array(
					gk_ml_witaj( $d ) . ' Operator płatności nie potwierdził płatności za zamówienie nr <b>' . gk_ml_esc( $n ) . '</b>. Najczęściej to przerwany BLIK, wygasły kod albo limit na karcie.',
					'Zamówienie czeka. Kliknij przycisk i spróbuj jeszcze raz, tą samą albo inną metodą.',
				),
				'po'        => gk_ml_tabela( $d ),
				'cta'       => array( 'Zapłać ponownie', $d['url_platnosci'] ),
				'uwaga'     => 'Pieniądze zniknęły z konta, a mimo to dostajesz ten mail? Bank zwykle sam zwraca nieudaną płatność w ciągu kilku dni roboczych. Jeśli tak się nie stanie, odpisz na tego maila, a wyjaśnimy to.',
			);
			$a['tekst_po'] = gk_ml_tabela_tekst( $d );
			break;

		case 'anulowane':
			$temat = 'Zamówienie nr ' . $n . ' zostało anulowane';
			$a    += array(
				'preheader' => 'Za to zamówienie nic nie płacisz. Możesz złożyć je ponownie w każdej chwili.',
				'nadtytul'  => 'Zamówienie anulowane',
				'tytul'     => 'Zamówienie anulowane',
				'akapity'   => array(
					gk_ml_witaj( $d ) . ' Zamówienie nr <b>' . gk_ml_esc( $n ) . '</b> zostało anulowane.',
					'Jeśli płatność została już pobrana, zwrócimy ją tą samą metodą w ciągu 14 dni i potwierdzimy to osobnym mailem.',
					'Anulowanie to pomyłka albo chcesz jednak kupić materiały? Złóż zamówienie ponownie, to zajmie minutę.',
				),
				'po'        => gk_ml_tabela( $d ),
				'cta'       => array( 'Zamów ponownie', $d['url_ponownie'] ),
				'uwaga'     => 'Masz pytanie o to zamówienie? Odpisz na tego maila.',
			);
			$a['tekst_po'] = gk_ml_tabela_tekst( $d );
			break;

		case 'zwrot':
		case 'zwrot_czesc':
			$kwota = isset( $x['kwota'] ) ? $x['kwota'] : $d['suma'];
			$czesc = 'zwrot_czesc' === $typ;
			$temat = ( $czesc ? 'Częściowy zwrot' : 'Zwrot pieniędzy' ) . ' · zamówienie nr ' . $n;
			$a    += array(
				'preheader' => 'Zwracamy ' . $kwota . '. Pieniądze wrócą tą samą metodą płatności.',
				'nadtytul'  => $czesc ? 'Częściowy zwrot' : 'Zwrot pieniędzy',
				'tytul'     => 'Zwracamy ' . $kwota,
				'akapity'   => array(
					gk_ml_witaj( $d ) . ' ' . ( $czesc ? 'Zleciliśmy częściowy zwrot' : 'Zleciliśmy zwrot' ) . ' za zamówienie nr <b>' . gk_ml_esc( $n ) . '</b>: <b>' . gk_ml_esc( $kwota ) . '</b>.',
					'Pieniądze wrócą tą samą metodą, którą płacono (' . gk_ml_esc( $d['metoda'] ) . '). Zależnie od banku to zwykle od 1 do 10 dni roboczych.',
				),
				'po'        => ! empty( $x['powod'] ) ? gk_ml_ramka( '<b>Powód zwrotu:</b> ' . gk_ml_esc( $x['powod'] ) ) : '',
				'cta'       => array( 'Zobacz zamówienie', $d['url_zamowienia'] ),
				'pod'       => $czesc ? '' : gk_ml_akapit( 'Po pełnym zwrocie linki do pobrania z tego zamówienia mogą przestać działać.', 14.5, '#7A6580' ),
				'uwaga'     => 'Pieniądze nie wróciły po 10 dniach roboczych? Odpisz na tego maila, a sprawdzimy to u operatora płatności.',
			);
			if ( ! empty( $x['powod'] ) ) { $a['tekst_po'] = 'Powód zwrotu: ' . $x['powod']; }
			break;

		case 'notatka':
			$temat = 'Wiadomość do zamówienia nr ' . $n;
			$a    += array(
				'preheader' => gk_ml_skrot( isset( $x['notatka'] ) ? $x['notatka'] : '', 90 ),
				'nadtytul'  => 'Wiadomość do zamówienia',
				'tytul'     => 'Mamy wiadomość o Twoim zamówieniu',
				'akapity'   => array( gk_ml_witaj( $d ) . ' Do zamówienia nr <b>' . gk_ml_esc( $n ) . '</b> dodaliśmy wiadomość dla Ciebie:' ),
				'po'        => gk_ml_ramka( nl2br( gk_ml_esc( isset( $x['notatka'] ) ? $x['notatka'] : '' ) ), '#FFEFC7' ),
				'cta'       => array( 'Zobacz zamówienie', $d['url_zamowienia'] ),
				'uwaga'     => 'Chcesz odpowiedzieć? Po prostu odpisz na tego maila.',
			);
			$a['tekst_po'] = isset( $x['notatka'] ) ? $x['notatka'] : '';
			break;

		case 'odstapienie':
			$wiersze = isset( $x['wiersze'] ) ? (array) $x['wiersze'] : array();
			$html    = '';
			$txt     = '';
			foreach ( $wiersze as $k => $v ) {
				$html .= '<b>' . gk_ml_esc( $k ) . ':</b> ' . nl2br( gk_ml_esc( $v ) ) . '<br>';
				$txt  .= $k . ': ' . $v . "\n";
			}
			$temat = 'Mamy Twoje oświadczenie o odstąpieniu';
			$a    += array(
				'preheader' => 'Potwierdzamy otrzymanie oświadczenia. Odpowiemy w ciągu 14 dni.',
				'nadtytul'  => 'Odstąpienie od umowy',
				'tytul'     => 'Otrzymaliśmy Twoje oświadczenie',
				'akapity'   => array(
					'Dzień dobry! Potwierdzamy, że otrzymaliśmy oświadczenie o odstąpieniu od umowy' . ( $n ? ' dla zamówienia nr <b>' . gk_ml_esc( $n ) . '</b>' : '' ) . '. Ten mail jest potwierdzeniem jego otrzymania.',
					'Sprawdzimy zamówienie i w ciągu 14 dni napiszemy, co dalej. Jeśli prawo odstąpienia przysługuje, zwrócimy płatność tą samą metodą w ciągu 14 dni od otrzymania oświadczenia (§6 Regulaminu sklepu).',
				),
				'po'        => $html ? gk_ml_ramka( $html, '#EDE5FB' ) : '',
				'cta'       => array( 'Przeczytaj §6 Regulaminu', home_url( '/regulamin/#s6' ) ),
				'uwaga'     => 'To nie Ty wysyłasz to oświadczenie? Odpisz od razu na tego maila.',
			);
			$a['tekst_po'] = $txt;
			break;

		default:
			return array( '', array() );
	}
	return array( $temat, $a );
}

function gk_ml_tytul_pliki( array $d ) {
	if ( count( $d['pliki'] ) > 1 ) { return 'Oto Twoje pliki'; }
	$p = isset( $d['pliki'][0] ) ? $d['pliki'][0] : array( '', '' );
	return ( false !== stripos( $p[0] . $p[1], 'zip' ) || false !== stripos( $p[0], 'pakiet' ) ) ? 'Oto Twój pakiet' : 'Oto Twój PDF';
}

function gk_ml_limit_krotko( array $d ) {
	$p = $d['pliki'][0];
	$x = array();
	if ( '' !== (string) $p[2] ) { $x[] = (int) $p[2] . ' razy'; }
	if ( '' !== (string) $p[3] ) { $x[] = 'do ' . $p[3]; }
	return $x ? implode( ' ', $x ) : 'bez limitu';
}

function gk_ml_skrot( $s, $max ) {
	$s = trim( preg_replace( '/\s+/', ' ', (string) $s ) );
	return mb_strlen( $s ) > $max ? mb_substr( $s, 0, $max - 1 ) . '…' : $s;
}

/* ─────────────────────────── Akademia: kupon ─────────────────────────── */
/**
 * $k: kod, procent, wazne_do (znacznik czasu), imie, url, produkty [[nazwa, cena, cena_po]]
 * To mail transakcyjny: użytkownik sam kliknął „Odbierz kupon” w aplikacji.
 */
function gk_ml_mail_kupon( array $k ) {
	$url = $k['url'];
	$tab = '';
	$txt = '';
	if ( ! empty( $k['produkty'] ) ) {
		$tab = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 20px;border-collapse:collapse;">';
		foreach ( $k['produkty'] as $p ) {
			$tab .= '<tr><td style="font-family:Arial,Helvetica,sans-serif;font-size:15px;color:#3A2340;padding:9px 10px 9px 0;border-bottom:1px solid #F3E3D6;">' . gk_ml_esc( $p[0] ) . '</td>';
			$tab .= '<td align="right" style="font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#7A6580;padding:9px 10px;border-bottom:1px solid #F3E3D6;white-space:nowrap;"><s>' . gk_ml_esc( $p[1] ) . '</s></td>';
			$tab .= '<td align="right" style="font-family:Arial,Helvetica,sans-serif;font-size:15px;font-weight:bold;color:#E8512A;padding:9px 0;border-bottom:1px solid #F3E3D6;white-space:nowrap;">' . gk_ml_esc( $p[2] ) . '</td></tr>';
			$txt .= '- ' . $p[0] . ': ' . $p[2] . ' zamiast ' . $p[1] . "\n";
		}
		$tab .= '</table>';
	}
	$kod_html = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 20px;"><tr><td align="center" bgcolor="#FFEFC7" style="background:#FFEFC7;border:2px dashed #FFC84A;border-radius:16px;padding:18px 12px;">'
		. '<div style="font-family:Arial,Helvetica,sans-serif;font-size:13px;font-weight:bold;letter-spacing:1.5px;color:#7A6580;">TWÓJ KOD</div>'
		. '<div style="font-family:\'Courier New\',Courier,monospace;font-size:21px;font-weight:bold;letter-spacing:1px;white-space:nowrap;color:#3A2340;padding:6px 0;">' . gk_ml_esc( $k['kod'] ) . '</div>'
		. '<div style="font-family:Arial,Helvetica,sans-serif;font-size:15px;color:#3A2340;"><b>−' . (int) $k['procent'] . '%</b> na materiały Kierunek Zysk · ważny do <b>' . gk_ml_esc( gk_ml_data( $k['wazne_do'] ) ) . '</b></div>'
		. '</td></tr></table>';
	$a = array(
		'preheader'  => 'Kod ' . $k['kod'] . ' daje −' . (int) $k['procent'] . '% na materiały Kierunek Zysk. Ważny 7 dni.',
		'nadtytul'   => 'Akademia Guliwera · rabat za naukę',
		'tytul'      => 'Masz −' . (int) $k['procent'] . '% za regularną naukę',
		'akapity'    => array(
			( $k['imie'] ? 'Gratulacje, ' . gk_ml_esc( $k['imie'] ) . '!' : 'Gratulacje!' ) . ' Twój kupon z Akademii Guliwera jest gotowy. To nagroda za regularne lekcje: im częściej się uczysz, tym większy rabat.',
		),
		'po'         => $kod_html . ( $tab ? gk_ml_naglowek( 'Przykładowe ceny z kuponem' ) . $tab : '' ),
		'cta'        => array( 'Użyj kuponu w koszyku', $url ),
		'pod'        => gk_ml_akapit( 'Przycisk od razu dodaje kupon do koszyka. Wystarczy wybrać materiał.', 14.5, '#7A6580' ),
		'uwaga'      => 'Kupon działa raz, tylko z adresem e-mail Twojego konta i tylko na materiały Kierunek Zysk. Nie łączy się z innymi kodami. Kolejny kupon możesz odebrać po 30 dniach.',
		'uwaga_ikona' => '🎟️',
		'powod'      => 'Dostajesz tę wiadomość, bo odebrano kupon w Akademii Guliwera na Twoim koncie. To potwierdzenie, nie reklama.',
		'tekst_po'   => 'TWÓJ KOD: ' . $k['kod'] . "\n−" . (int) $k['procent'] . '% na materiały Kierunek Zysk, ważny do ' . gk_ml_data( $k['wazne_do'] ) . "\n\n" . $txt,
	);
	return array( 'Twój kupon −' . (int) $k['procent'] . '% z Akademii jest gotowy', $a );
}

/* ─────────────────────────── Maile do administratora ─────────────────────────── */
/** $typ: admin_nowe | admin_nieudane | admin_anulowane | admin_odstapienie */
function gk_ml_mail_admin( $typ, array $d, array $x = array() ) {
	$n    = $d['numer'];
	$rows = array(
		'Zamówienie' => 'nr ' . $n . ' · ' . $d['data'],
		'Kwota'      => $d['suma'] . ' · ' . $d['metoda'],
		'Klient'     => trim( $d['imie_nazwisko'] . ' · ' . $d['email'] ) . ( $d['konto'] ? ' (konto)' : ' (bez konta)' ),
		'Produkty'   => implode( ', ', array_map( function ( $p ) { return $p[0] . ( (int) $p[1] > 1 ? ' × ' . (int) $p[1] : '' ); }, $d['pozycje'] ) ),
		'Zgoda na natychmiastowe dostarczenie' => $d['zgoda'] ? 'TAK, ' . gk_ml_data( $d['zgoda'] ) : 'BRAK: klient zachowuje prawo odstąpienia (14 dni)',
	);
	if ( ! empty( $d['kupon'] ) ) { $rows['Kupon'] = $d['kupon']; }
	if ( ! empty( $d['notatka_klienta'] ) ) { $rows['Uwagi klienta'] = $d['notatka_klienta']; }

	$akapity = array();
	$pod     = '';
	$tpod    = '';
	switch ( $typ ) {
		case 'admin_nowe':
			$temat     = 'Nowe zamówienie nr ' . $n . ' · ' . $d['suma'] . ( $d['faktura'] ? ' · faktura' : '' );
			$tytul     = 'Nowe zamówienie nr ' . $n;
			$akapity[] = $d['zaplacone'] ? 'Opłacone. Klient dostał mail z plikami do pobrania.' : 'Czeka na płatność. Klient dostał mail „czeka na płatność”.';
			break;
		case 'admin_nieudane':
			$temat     = 'Nieudane zamówienie nr ' . $n . ' · ' . $d['suma'];
			$tytul     = 'Płatność nieudana: zamówienie nr ' . $n;
			$akapity[] = 'Operator płatności odrzucił płatność albo klient ją przerwał. Klient dostał mail z linkiem „Zapłać ponownie”. Nic nie musisz robić.';
			break;
		case 'admin_anulowane':
			$temat     = 'Anulowane zamówienie nr ' . $n;
			$tytul     = 'Anulowane zamówienie nr ' . $n;
			$akapity[] = $d['zaplacone'] ? '<b>Zamówienie było opłacone.</b> Jeśli to nie był zwrot, zrób zwrot w panelu zamówienia (Stripe zwróci pieniądze automatycznie).' : 'Zamówienie nie było opłacone (np. minął czas na płatność). Nic nie musisz robić.';
			break;
		case 'admin_odstapienie':
			$temat     = 'Odstąpienie od umowy · zamówienie nr ' . $n;
			$tytul     = 'Klient odstępuje od umowy: zamówienie nr ' . $n;
			$akapity[] = 'Masz 14 dni na odpowiedź. Sprawdź pole „Zgoda na natychmiastowe dostarczenie” i czy pliki zostały pobrane. Jeśli zgody nie ma albo plików nie pobrano, zwróć płatność w ciągu 14 dni (panel zamówienia → Zwrot).';
			foreach ( (array) ( isset( $x['wiersze'] ) ? $x['wiersze'] : array() ) as $k => $v ) { $rows[ 'Formularz: ' . $k ] = $v; }
			break;
		default:
			return array( '', array() );
	}

	if ( $d['faktura'] && 'admin_nowe' === $typ ) {
		$pod  = gk_ml_ramka( '<b>📄 Wystaw fakturę bez VAT w fakturowo.pl</b> (w ciągu 2 dni roboczych, klientowi to obiecaliśmy):<br>'
			. 'Nabywca: ' . nl2br( gk_ml_esc( $d['faktura'][1] ) ) . '<br>NIP: <b>' . gk_ml_esc( $d['faktura'][0] ? $d['faktura'][0] : 'brak (faktura imienna)' ) . '</b><br>'
			. 'Kwota: ' . gk_ml_esc( $d['suma'] ) . ' · data sprzedaży: ' . gk_ml_esc( $d['data'] ) . '<br>Wyślij fakturę na: ' . gk_ml_esc( $d['email'] ), '#FFEFC7' );
		$tpod = "WYSTAW FAKTURĘ BEZ VAT W FAKTUROWO.PL\nNabywca: " . $d['faktura'][1] . "\nNIP: " . ( $d['faktura'][0] ? $d['faktura'][0] : 'brak' ) . "\nKwota: " . $d['suma'] . "\nWyślij na: " . $d['email'] . "\n";
	}

	$tab = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 20px;border-collapse:collapse;">';
	$txt = '';
	foreach ( $rows as $k => $v ) {
		$alert = 0 === strpos( $v, 'BRAK' );
		$tab  .= '<tr><td valign="top" width="38%" style="font-family:Arial,Helvetica,sans-serif;font-size:13.5px;color:#7A6580;padding:8px 10px 8px 0;border-bottom:1px solid #F3E3D6;">' . gk_ml_esc( $k ) . '</td>';
		$tab  .= '<td valign="top" style="font-family:Arial,Helvetica,sans-serif;font-size:14.5px;color:' . ( $alert ? '#E8512A;font-weight:bold' : '#3A2340' ) . ';padding:8px 0;border-bottom:1px solid #F3E3D6;">' . nl2br( gk_ml_esc( $v ) ) . '</td></tr>';
		$txt  .= $k . ': ' . $v . "\n";
	}
	$tab .= '</table>';

	return array( $temat, array(
		'prosty'    => true,
		'preheader' => $d['suma'] . ' · ' . $d['email'] . ' · ' . $d['metoda'],
		'tytul'     => $tytul,
		'akapity'   => $akapity,
		'po'        => $tab . $pod,
		'cta'       => array( 'Otwórz zamówienie w panelu', $d['url_admin'] ),
		'tekst_po'  => $txt . "\n" . $tpod,
	) );
}
