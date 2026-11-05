<?php
/**
 * Guliwera 4: podgląd wszystkich maili na przykładowych danych.
 * guliwera4.pl/?gk_mail_podglad=lista (spis), …=pliki, …=pliki&tekst=1 (wersja tekstowa). Tylko zalogowany administrator.
 * Te same dane służą do zrzutów ekranu robionych lokalnie (narzedzia/podglad-maili.php).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Przykładowe zamówienie. $w: jeden | pakiet | wiele | gosc | czeka */
function gk_ml_przyklad( $w = 'jeden' ) {
	$ts = strtotime( '2026-11-05 14:03:00' );
	$d  = array(
		'numer' => '1042', 'data' => wp_date( 'j.m.Y, H:i', $ts ), 'imie' => 'Ola', 'imie_nazwisko' => 'Ola Przykładowa', 'email' => 'ola@przyklad.pl',
		'pozycje' => array( array( 'Kierunek Zysk: Planer 90 dni', 1, '59,00 zł' ) ),
		'podsumowanie' => array( array( 'Kwota', '59,00 zł' ), array( 'Razem', '59,00 zł' ) ),
		'suma' => '59,00 zł', 'metoda' => 'BLIK',
		'pliki' => array( array( 'Kierunek Zysk: Planer 90 dni', 'https://guliwera4.pl/?download_file=70&order=wc_order_PRZYKLAD&key=…', 5, wp_date( 'j.m.Y', $ts + 30 * DAY_IN_SECONDS ) ) ),
		'konto' => true, 'zaplacone' => true,
		'url_pliki' => home_url( '/moje-konto/downloads/' ), 'url_zamowienia' => home_url( '/moje-konto/view-order/1042/' ),
		'url_platnosci' => home_url( '/zamowienie/order-pay/1042/?pay_for_order=true&key=wc_order_PRZYKLAD' ),
		'url_ponownie' => home_url( '/zamowienie/?add-to-cart=70' ), 'url_admin' => admin_url( 'admin.php?page=wc-orders&action=edit&id=1042' ),
		'zgoda' => $ts, 'faktura' => null, 'kupon' => '', 'notatka_klienta' => '', 'instrukcje' => '',
	);
	if ( 'pakiet' === $w ) {
		$d['pozycje']      = array( array( 'Kierunek Zysk: Pakiet (7 materiałów)', 1, '297,00 zł' ) );
		$d['podsumowanie'] = array( array( 'Kwota', '297,00 zł' ), array( 'Rabat (AKADEMIA-22-K7PQ2X)', '−65,34 zł' ), array( 'Razem', '231,66 zł' ) );
		$d['suma']         = '231,66 zł';
		$d['metoda']       = 'Karta płatnicza';
		$d['kupon']        = 'AKADEMIA-22-K7PQ2X';
		$d['pliki']        = array( array( 'Kierunek Zysk: Pakiet (ZIP)', 'https://guliwera4.pl/?download_file=67&order=wc_order_PRZYKLAD&key=…', 5, wp_date( 'j.m.Y', $ts + 30 * DAY_IN_SECONDS ) ) );
		$d['faktura']      = array( '9462713420', "Przykładowa Firma sp. z o.o.\nul. Przykładowa 1, 20-001 Lublin" );
	}
	if ( 'wiele' === $w || 'gosc' === $w ) {
		$d['pozycje']      = array( array( 'Kierunek Zysk: Planer 90 dni', 1, '59,00 zł' ), array( 'Kierunek Zysk: Strategia na 1 stronie', 1, '39,00 zł' ) );
		$d['podsumowanie'] = array( array( 'Kwota', '98,00 zł' ), array( 'Razem', '98,00 zł' ) );
		$d['suma']         = '98,00 zł';
		$d['pliki'][]      = array( 'Kierunek Zysk: Strategia na 1 stronie', 'https://guliwera4.pl/?download_file=69&order=wc_order_PRZYKLAD&key=…', 5, wp_date( 'j.m.Y', $ts + 30 * DAY_IN_SECONDS ) );
	}
	if ( 'gosc' === $w ) {
		$d['konto']          = false;
		$d['url_zamowienia'] = home_url( '/zamowienie/order-received/1042/?key=wc_order_PRZYKLAD' );
	}
	if ( 'czeka' === $w ) {
		$d['metoda']    = 'Przelewy24';
		$d['zaplacone'] = false;
		$d['pliki']     = array();
	}
	return $d;
}

/** Spis wszystkich maili: klucz → [grupa, opis, temat, treść]. */
function gk_ml_podglad_lista() {
	$u = (object) array( 'user_login' => 'ola.przykladowa', 'first_name' => 'Ola', 'user_email' => 'ola@przyklad.pl' );
	$l = array();
	$dodaj = function ( $k, $grupa, $opis, $wyn ) use ( &$l ) { $l[ $k ] = array( $grupa, $opis, $wyn[0], $wyn[1] ); };

	$dodaj( 'powitanie', 'Konto', 'Powitanie po rejestracji', array( 'Witaj w Guliwera 4! Twoje konto jest gotowe', gk_ml_tresc_powitanie( $u, $u->user_login ) ) );
	$dodaj( 'reset', 'Konto', 'Reset hasła', array( 'Ustaw nowe hasło do konta Guliwera 4', gk_ml_tresc_reset( $u->user_login, home_url( '/moje-konto/lost-password/?key=PRZYKLAD' ) ) ) );
	$dodaj( 'haslo', 'Konto', 'Hasło zmienione', array( 'Twoje hasło do Guliwera 4 zostało zmienione', gk_ml_tresc_haslo_zmienione( $u ) ) );
	$dodaj( 'logowanie', 'Konto', 'Nowe logowanie (nowe urządzenie)', array( 'Nowe logowanie na Twoje konto Guliwera 4', gk_ml_tresc_logowanie( $u, 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_0 like Mac OS X) Safari/604.1', '31.61.238.144' ) ) );
	$dodaj( 'email', 'Konto', 'Zmiana adresu e-mail (na stary adres)', array( 'Zmieniono adres e-mail konta Guliwera 4', gk_ml_tresc_email_zmieniony( $u, 'nowy@przyklad.pl' ) ) );

	$dodaj( 'oczekuje', 'Zamówienie', 'Zamówienie przyjęte, czeka na płatność (on-hold)', gk_ml_mail_zamowienie( 'oczekuje', gk_ml_przyklad( 'czeka' ) ) );
	$dodaj( 'pliki', 'Zamówienie', 'Oto Twój PDF (zamówienie zrealizowane, 1 plik, konto)', gk_ml_mail_zamowienie( 'pliki', gk_ml_przyklad( 'jeden' ) ) );
	$dodaj( 'pliki_pakiet', 'Zamówienie', 'Oto Twój PDF: pakiet ZIP z kuponem i fakturą', gk_ml_mail_zamowienie( 'pliki', gk_ml_przyklad( 'pakiet' ) ) );
	$dodaj( 'pliki_gosc', 'Zamówienie', 'Oto Twoje pliki: 2 pliki, zakup bez konta', gk_ml_mail_zamowienie( 'pliki', gk_ml_przyklad( 'gosc' ) ) );
	$dodaj( 'oplacone', 'Zamówienie', 'Płatność przyjęta (processing)', gk_ml_mail_zamowienie( 'oplacone', gk_ml_przyklad( 'jeden' ) ) );
	$dodaj( 'nieudane', 'Zamówienie', 'Płatność nieudana', gk_ml_mail_zamowienie( 'nieudane', gk_ml_przyklad( 'czeka' ) ) );
	$dodaj( 'anulowane', 'Zamówienie', 'Zamówienie anulowane', gk_ml_mail_zamowienie( 'anulowane', gk_ml_przyklad( 'czeka' ) ) );
	$dodaj( 'zwrot', 'Zamówienie', 'Zwrot pieniędzy (pełny)', gk_ml_mail_zamowienie( 'zwrot', gk_ml_przyklad( 'jeden' ), array( 'kwota' => '59,00 zł', 'powod' => 'Plik nie otwierał się na tablecie' ) ) );
	$dodaj( 'zwrot_czesc', 'Zamówienie', 'Zwrot częściowy', gk_ml_mail_zamowienie( 'zwrot_czesc', gk_ml_przyklad( 'wiele' ), array( 'kwota' => '39,00 zł' ) ) );
	$dodaj( 'notatka', 'Zamówienie', 'Wiadomość do zamówienia (notatka dla klienta)', gk_ml_mail_zamowienie( 'notatka', gk_ml_przyklad( 'jeden' ), array( 'notatka' => "Dzień dobry! Wysyłam nowy link do Planera, bo poprzedni wygasł. Działa przez kolejne 30 dni.\nAngelika" ) ) );
	$dodaj( 'szczegoly', 'Zamówienie', 'Szczegóły zamówienia / link do zapłaty (wysyłany ręcznie z panelu)', gk_ml_mail_zamowienie( 'szczegoly', gk_ml_przyklad( 'czeka' ) ) );
	$dodaj( 'odstapienie', 'Zamówienie', 'Potwierdzenie otrzymania odstąpienia od umowy', gk_ml_mail_zamowienie( 'odstapienie', gk_ml_przyklad( 'jeden' ), array( 'wiersze' => array( 'Numer zamówienia' => '1042', 'Imię i nazwisko' => 'Ola Przykładowa', 'Data złożenia' => '6.11.2026, 09:12' ) ) ) );

	$dodaj( 'kupon', 'Akademia', 'Kupon rabatowy wydany', gk_ml_mail_kupon( array(
		'kod' => 'AKADEMIA-22-K7PQ2X', 'procent' => 22, 'wazne_do' => strtotime( '2026-11-12 14:03:00' ), 'imie' => 'Ola',
		'url' => home_url( '/koszyk/?gk_kupon=AKADEMIA-22-K7PQ2X' ),
		'produkty' => array( array( 'Kierunek Zysk: Przewodnik', '147,00 zł', '114,66 zł' ), array( 'Kierunek Zysk: Planer 90 dni', '59,00 zł', '46,02 zł' ), array( 'Kierunek Zysk: Pakiet', '297,00 zł', '231,66 zł' ) ),
	) ) );

	$dodaj( 'admin_nowe', 'Administrator', 'Nowe zamówienie (z fakturą)', gk_ml_mail_admin( 'admin_nowe', gk_ml_przyklad( 'pakiet' ) ) );
	$dodaj( 'admin_nieudane', 'Administrator', 'Nieudane zamówienie', gk_ml_mail_admin( 'admin_nieudane', gk_ml_przyklad( 'czeka' ) ) );
	$dodaj( 'admin_anulowane', 'Administrator', 'Anulowane zamówienie', gk_ml_mail_admin( 'admin_anulowane', gk_ml_przyklad( 'czeka' ) ) );
	$brak_zgody = gk_ml_przyklad( 'jeden' );
	$brak_zgody['zgoda'] = 0;
	$dodaj( 'admin_odstapienie', 'Administrator', 'Odstąpienie od umowy (bez zgody)', gk_ml_mail_admin( 'admin_odstapienie', $brak_zgody, array( 'wiersze' => array( 'Data złożenia' => '6.11.2026, 09:12' ) ) ) );
	return $l;
}

add_action( 'template_redirect', function () {
	if ( empty( $_GET['gk_mail_podglad'] ) || ! current_user_can( 'manage_options' ) ) { return; }
	$lista = gk_ml_podglad_lista();
	$k     = sanitize_key( wp_unslash( $_GET['gk_mail_podglad'] ) );
	nocache_headers();
	if ( ! isset( $lista[ $k ] ) ) {
		header( 'Content-Type: text/html; charset=utf-8' );
		echo '<!doctype html><meta charset="utf-8"><title>Maile Guliwera 4</title><body style="font:16px/1.6 Arial,sans-serif;max-width:760px;margin:30px auto;color:#3A2340"><h1>Maile Guliwera 4</h1><table cellpadding="6" style="border-collapse:collapse">';
		foreach ( $lista as $kk => $m ) {
			echo '<tr><td style="color:#7A6580">' . esc_html( $m[0] ) . '</td><td><a href="' . esc_url( add_query_arg( 'gk_mail_podglad', $kk, home_url( '/' ) ) ) . '">' . esc_html( $m[1] ) . '</a></td><td>' . esc_html( $m[2] ) . ' <small>(' . mb_strlen( $m[2] ) . ' zn.)</small></td></tr>';
		}
		echo '</table></body>';
		exit;
	}
	if ( ! empty( $_GET['tekst'] ) ) {
		header( 'Content-Type: text/plain; charset=utf-8' );
		echo 'Temat: ' . $lista[ $k ][2] . "\n\n" . gk_ml_tekst( $lista[ $k ][3] ); // phpcs:ignore
		exit;
	}
	header( 'Content-Type: text/html; charset=utf-8' );
	echo gk_ml_layout( $lista[ $k ][3] ); // phpcs:ignore
	exit;
} );
