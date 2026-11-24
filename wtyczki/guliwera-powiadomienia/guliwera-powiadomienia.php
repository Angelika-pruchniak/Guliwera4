<?php
/**
 * Plugin Name: Guliwera – powiadomienia Akademii
 * Description: Przypomnienia o nauce w Akademii Guliwera na telefonie (Web Push, PWA) z Waflem: zgoda w aplikacji po pierwszej lekcji, pora wybrana przez użytkownika (domyślnie 19:00), najwyżej 1 dziennie, nigdy między 21:00 a 8:00. Klucze VAPID powstają na serwerze (opcja w bazie), bez zewnętrznych usług i kosztów. Wymaga wtyczki „Guliwera – Akademia”.
 * Version: 1.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'GK_PW_KONTAKT', 'kontakt@guliwera4.pl' );
define( 'GK_PW_DOMYSLNA', '19:00' );
define( 'GK_PW_CISZA_OD', 21 ); // godz.
define( 'GK_PW_CISZA_DO', 8 );

require_once __DIR__ . '/includes/webpush.php';

/* ─────────────────────────── Klucze i dane ─────────────────────────── */
function gk_pw_vapid() {
	$v = get_option( 'gk_pw_vapid' );
	if ( ! is_array( $v ) || empty( $v['prywatny'] ) ) {
		$v = gk_wp_vapid_nowe();
		update_option( 'gk_pw_vapid', $v, false );
	}
	return $v;
}

function gk_pw_dane( $uid ) {
	$d = get_user_meta( $uid, 'gk_push', true );
	return wp_parse_args( is_array( $d ) ? $d : array(), array( 'subs' => array(), 'godzina' => GK_PW_DOMYSLNA, 'ostatnio' => '', 'zgoda' => 0, 'kamienie' => array(), 'log' => array() ) );
}
function gk_pw_zapisz( $uid, array $d ) {
	if ( ! $d['subs'] ) { delete_user_meta( $uid, 'gk_push' ); return; }
	update_user_meta( $uid, 'gk_push', $d );
}

/** Czy użytkownik ma aktywne powiadomienia (dla wtyczki newslettera: wtedy bez maili-przypomnień). */
function gk_pw_ma_push( $uid ) { return (bool) gk_pw_dane( $uid )['subs']; }

/** Adres obrazka Wafla (także do maili). */
function gk_pw_obrazek( $poza ) { return plugins_url( 'assets/wafel-' . sanitize_key( $poza ) . '.png', __FILE__ ); }

function gk_pw_godziny() {
	$g = array();
	for ( $h = GK_PW_CISZA_DO; $h < GK_PW_CISZA_OD; $h++ ) { $g[] = sprintf( '%02d:00', $h ); $g[] = sprintf( '%02d:30', $h ); }
	return $g; // 08:00 … 20:30
}

/* ─────────────────────────── Treści (emocje Wafla) ─────────────────────────── */
function gk_pw_seria( array $dni, $dzis ) {
	$s = array_flip( $dni );
	$t = strtotime( $dzis . ' 12:00' );
	if ( ! isset( $s[ $dzis ] ) ) { $t -= DAY_IN_SECONDS; }
	$n = 0;
	while ( isset( $s[ gmdate( 'Y-m-d', $t ) ] ) ) { $n++; $t -= DAY_IN_SECONDS; }
	return $n;
}

/**
 * Co wysłać dziś? Zwraca array( poza, tytuł, treść, klucz ) albo null.
 * 1 dzień przerwy → macha, 2–3 dni → czeka, 7/14/30 dni → śpi, kamień milowy serii → brawo. Potem cisza.
 */
function gk_pw_wiadomosc( array $p, array $d, $dzis ) {
	$dni = array_values( array_filter( (array) $p['dni'] ) );
	if ( ! $dni ) { return null; }
	$ost     = max( $dni );
	$przerwa = (int) round( ( strtotime( $dzis . ' 12:00' ) - strtotime( $ost . ' 12:00' ) ) / DAY_IN_SECONDS );
	if ( 0 === $przerwa ) {
		$s = gk_pw_seria( $dni, $dzis );
		if ( in_array( $s, array( 3, 7, 14, 30, 50, 100 ), true ) && ! in_array( $s, (array) $d['kamienie'], true ) ) {
			return array( 'brawo', 'Seria ' . $s . ' dni! 🔥', 'Wafel bije brawo. Tak trzymaj, jutro dzień ' . ( $s + 1 ) . '.', 's' . $s );
		}
		return null;
	}
	if ( 1 === $przerwa ) { return array( 'macha', 'Wafel czeka na Ciebie 🐾', '3 minuty i seria uratowana. Jedna lekcja wystarczy.', 'p1' ); }
	if ( $przerwa <= 3 ) { return array( 'czeka', 'Wafel leży przy drzwiach', 'Dawno Cię nie było. Krótka lekcja na rozgrzewkę? 5 minut.', 'p' . $przerwa ); }
	if ( in_array( $przerwa, array( 7, 14, 30 ), true ) ) { return array( 'spi', 'Wafel zasnął z tęsknoty…', 'Obudzisz go jedną lekcją? Zajmie 5 minut.', 'p' . $przerwa ); }
	return null;
}

function gk_pw_tresc( array $w ) {
	return array(
		'title' => $w[1],
		'body'  => $w[2],
		'icon'  => gk_pw_obrazek( $w[0] ),
		'badge' => plugins_url( 'assets/badge-96.png', __FILE__ ),
		'url'   => home_url( '/akademia/?utm_source=push&utm_medium=powiadomienie&utm_campaign=' . $w[3] ),
		'tag'   => 'akademia-przypomnienie',
	);
}

/** Wysyła do wszystkich urządzeń użytkownika, usuwa wygasłe. Zwraca liczbę przyjętych. */
function gk_pw_wyslij_do( $uid, array $tresc ) {
	$d  = gk_pw_dane( $uid );
	$ok = 0;
	foreach ( $d['subs'] as $h => $s ) {
		$kod = gk_wp_wyslij( $s, $tresc, gk_pw_vapid(), GK_PW_KONTAKT );
		if ( in_array( $kod, array( 404, 410 ), true ) ) { unset( $d['subs'][ $h ] ); }
		elseif ( $kod >= 200 && $kod < 300 ) { $ok++; }
		else { gk_pw_log( 'push ' . $kod . ' (' . wp_parse_url( $s['endpoint'], PHP_URL_HOST ) . ')' ); }
	}
	gk_pw_zapisz( $uid, $d );
	return $ok;
}

function gk_pw_log( $txt ) {
	$l   = (array) get_option( 'gk_pw_log', array() );
	$l[] = wp_date( 'Y-m-d H:i' ) . ' ' . $txt;
	update_option( 'gk_pw_log', array_slice( $l, -50 ), false );
}

/* ─────────────────────────── Harmonogram (co 15 minut) ─────────────────────────── */
add_filter( 'cron_schedules', function ( $s ) { $s['gk_pw_15'] = array( 'interval' => 900, 'display' => 'Co 15 minut (Akademia)' ); return $s; } );
add_action( 'init', function () { if ( ! wp_next_scheduled( 'gk_pw_cron' ) ) { wp_schedule_event( time() + 120, 'gk_pw_15', 'gk_pw_cron' ); } } );
register_deactivation_hook( __FILE__, function () { wp_clear_scheduled_hook( 'gk_pw_cron' ); } );

add_action( 'gk_pw_cron', 'gk_pw_przebieg' );
function gk_pw_przebieg() {
	if ( ! function_exists( 'gk_ak_postep' ) ) { return; }
	$godz = (int) wp_date( 'G' );
	if ( $godz < GK_PW_CISZA_DO || $godz >= GK_PW_CISZA_OD ) { return; }
	$dzis  = wp_date( 'Y-m-d' );
	$teraz = wp_date( 'H:i' );
	foreach ( get_users( array( 'meta_key' => 'gk_push', 'fields' => 'ID', 'number' => 500 ) ) as $uid ) {
		$d = gk_pw_dane( $uid );
		if ( ! $d['subs'] || $d['ostatnio'] === $dzis || $teraz < $d['godzina'] ) { continue; }
		$w = gk_pw_wiadomosc( gk_ak_postep( $uid ), $d, $dzis );
		$d['ostatnio'] = $dzis; // sprawdzone dziś: najwyżej 1 próba dziennie
		if ( $w && 0 === strpos( $w[3], 's' ) ) { $d['kamienie'][] = (int) substr( $w[3], 1 ); }
		gk_pw_zapisz( $uid, $d );
		if ( $w ) { gk_pw_wyslij_do( $uid, gk_pw_tresc( $w ) ); }
	}
}

/* ─────────────────────────── REST dla aplikacji ─────────────────────────── */
add_action( 'rest_api_init', function () {
	$zal = function () { return is_user_logged_in(); };
	$ns  = 'akademia/v1';
	register_rest_route( $ns, '/push', array(
		'methods' => 'GET', 'permission_callback' => $zal,
		'callback' => function () {
			$d = gk_pw_dane( get_current_user_id() );
			return array( 'klucz' => gk_pw_vapid()['publiczny'], 'godzina' => $d['godzina'], 'godziny' => gk_pw_godziny(), 'urzadzen' => count( $d['subs'] ) );
		},
	) );
	register_rest_route( $ns, '/push/zapisz', array(
		'methods' => 'POST', 'permission_callback' => $zal,
		'callback' => function ( $r ) {
			$uid = get_current_user_id();
			$e   = esc_url_raw( (string) $r->get_param( 'endpoint' ) );
			$k   = (array) $r->get_param( 'keys' );
			if ( 0 !== strpos( $e, 'https://' ) || empty( $k['p256dh'] ) || empty( $k['auth'] ) ) { return new WP_Error( 'gk_pw', 'Nieprawidłowa subskrypcja.', array( 'status' => 400 ) ); }
			$d = gk_pw_dane( $uid );
			if ( count( $d['subs'] ) >= 5 ) { array_shift( $d['subs'] ); }
			$d['subs'][ md5( $e ) ] = array( 'endpoint' => $e, 'p256dh' => sanitize_text_field( $k['p256dh'] ), 'auth' => sanitize_text_field( $k['auth'] ), 'dodano' => time() );
			$g = (string) $r->get_param( 'godzina' );
			if ( in_array( $g, gk_pw_godziny(), true ) ) { $d['godzina'] = $g; }
			if ( ! $d['zgoda'] ) { $d['zgoda'] = time(); }
			if ( wp_date( 'H:i' ) >= $d['godzina'] ) { $d['ostatnio'] = wp_date( 'Y-m-d' ); } // pierwsze przypomnienie najwcześniej jutro
			gk_pw_zapisz( $uid, $d );
			return array( 'ok' => true, 'godzina' => $d['godzina'] );
		},
	) );
	register_rest_route( $ns, '/push/godzina', array(
		'methods' => 'POST', 'permission_callback' => $zal,
		'callback' => function ( $r ) {
			$uid = get_current_user_id();
			$d   = gk_pw_dane( $uid );
			$g   = (string) $r->get_param( 'godzina' );
			if ( ! in_array( $g, gk_pw_godziny(), true ) ) { return new WP_Error( 'gk_pw', 'Wybierz godzinę między 8:00 a 20:30.', array( 'status' => 400 ) ); }
			$d['godzina'] = $g;
			gk_pw_zapisz( $uid, $d );
			return array( 'ok' => true, 'godzina' => $g );
		},
	) );
	register_rest_route( $ns, '/push/usun', array(
		'methods' => 'POST', 'permission_callback' => $zal,
		'callback' => function ( $r ) {
			$uid = get_current_user_id();
			$d   = gk_pw_dane( $uid );
			$e   = (string) $r->get_param( 'endpoint' );
			if ( $e ) { unset( $d['subs'][ md5( esc_url_raw( $e ) ) ] ); } else { $d['subs'] = array(); }
			gk_pw_zapisz( $uid, $d );
			return array( 'ok' => true );
		},
	) );
	register_rest_route( $ns, '/push/test', array(
		'methods' => 'POST', 'permission_callback' => $zal,
		'callback' => function () {
			$uid = get_current_user_id();
			if ( get_transient( 'gk_pw_test_' . $uid ) ) { return new WP_Error( 'gk_pw', 'Test można wysłać raz na minutę.', array( 'status' => 429 ) ); }
			set_transient( 'gk_pw_test_' . $uid, 1, MINUTE_IN_SECONDS );
			$n = gk_pw_wyslij_do( $uid, gk_pw_tresc( array( 'radosc', 'Działa! 🐾', 'Tak będą wyglądać przypomnienia od Wafla. Najwyżej jedno dziennie.', 'test' ) ) );
			return array( 'ok' => $n > 0, 'wyslano' => $n );
		},
	) );
} );

/* ─────────────────────────── Panel: Ustawienia → Powiadomienia Akademii ─────────────────────────── */
add_action( 'admin_menu', function () {
	add_options_page( 'Powiadomienia Akademii', 'Powiadomienia Akademii', 'manage_options', 'gk-powiadomienia', function () {
		$uids = get_users( array( 'meta_key' => 'gk_push', 'fields' => 'ID' ) );
		$urz  = 0;
		foreach ( $uids as $u ) { $urz += count( gk_pw_dane( $u )['subs'] ); }
		echo '<div class="wrap"><h1>Powiadomienia Akademii</h1>';
		echo '<p>Osoby z włączonymi przypomnieniami: <b>' . count( $uids ) . '</b> (urządzeń: ' . (int) $urz . '). Harmonogram: co 15 minut, najwyżej 1 powiadomienie dziennie na osobę, cisza ' . GK_PW_CISZA_OD . ':00–' . GK_PW_CISZA_DO . ':00.</p>';
		echo '<p>Reguły: 1 dzień przerwy → Wafel macha · 2–3 dni → Wafel czeka · 7, 14 i 30 dni → Wafel śpi · seria 3/7/14/30/50/100 dni → Wafel bije brawo. Potem cisza.</p>';
		echo '<p>Test na własnym koncie: w aplikacji Profil → Przypomnienia → „Wyślij próbne”.</p>';
		echo '<h2>Log</h2><pre style="background:#fff;padding:12px;max-height:260px;overflow:auto">' . esc_html( implode( "\n", array_reverse( (array) get_option( 'gk_pw_log', array() ) ) ) ) . '</pre></div>';
	} );
} );
