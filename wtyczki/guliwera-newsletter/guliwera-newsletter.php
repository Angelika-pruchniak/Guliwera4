<?php
/**
 * Plugin Name: Guliwera – newsletter
 * Description: Newsletter Guliwera 4: zapis z darmowym Pakietem startowym (formularz i rejestracja w Akademii), potwierdzenie zapisu (double opt-in), seria powitalna i maile wyzwalane nauką w Akademii i zakupami, wypis jednym kliknięciem, preferencje i synchronizacja z Mailchimpem (wydania). Wymaga wtyczki „Guliwera – maile”. Wysyłka rusza dopiero po włączeniu w Ustawienia → Newsletter Guliwera.
 * Version: 1.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'GK_NL_WERSJA', '1.0' );
define( 'GK_NL_NADAWCA', 'kontakt@guliwera4.pl' );
define( 'GK_NL_LIMIT_RUN', 40 );   // maili na jedno uruchomienie harmonogramu
define( 'GK_NL_PRODUKTY', array( 'pakiet' => 67, 'strategia' => 69, 'planer' => 70 ) );

require_once __DIR__ . '/includes/tresci.php';
require_once __DIR__ . '/includes/podglad.php';

/* ─────────────────────────── Ustawienia ─────────────────────────── */
function gk_nl_opcje() {
	return wp_parse_args( (array) get_option( 'gk_nl_opcje', array() ), array(
		'aktywny'   => 0,
		'api'       => '',
		'lista'     => '',
		'lista_nazwa' => '',
		'nadawca'   => 'Angelika z Guliwera 4',
		'plik_kalk' => '', 'plik_strat' => '', 'plik_przeg' => '',
		'sekret'    => '',
	) );
}
function gk_nl_aktywny() { $o = gk_nl_opcje(); return ! empty( $o['aktywny'] ) && function_exists( 'gk_ml_layout' ); }

/* ─────────────────────────── Tabela kontaktów ─────────────────────────── */
function gk_nl_tabela() { global $wpdb; return $wpdb->prefix . 'gk_newsletter'; }

function gk_nl_instaluj() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$t = gk_nl_tabela();
	dbDelta( "CREATE TABLE $t (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		email varchar(190) NOT NULL,
		imie varchar(100) NOT NULL DEFAULT '',
		status varchar(20) NOT NULL DEFAULT 'pending',
		zrodlo varchar(40) NOT NULL DEFAULT '',
		token char(32) NOT NULL,
		user_id bigint(20) unsigned NOT NULL DEFAULT 0,
		utworzono bigint(20) unsigned NOT NULL DEFAULT 0,
		potwierdzono bigint(20) unsigned NOT NULL DEFAULT 0,
		wypisano bigint(20) unsigned NOT NULL DEFAULT 0,
		ip_zapis varchar(45) NOT NULL DEFAULT '',
		ip_potw varchar(45) NOT NULL DEFAULT '',
		pref varchar(20) NOT NULL DEFAULT 'wszystko',
		seria_next bigint(20) unsigned NOT NULL DEFAULT 0,
		ostatni_mail bigint(20) unsigned NOT NULL DEFAULT 0,
		wyslane longtext NULL,
		kolejka longtext NULL,
		tagi text NULL,
		PRIMARY KEY  (id),
		UNIQUE KEY email (email),
		KEY token (token),
		KEY status (status)
	) {$wpdb->get_charset_collate()};" );
	update_option( 'gk_nl_db', GK_NL_WERSJA );
	$o = gk_nl_opcje();
	if ( empty( $o['sekret'] ) ) { $o['sekret'] = wp_generate_password( 24, false, false ); update_option( 'gk_nl_opcje', $o ); }
}
register_activation_hook( __FILE__, 'gk_nl_instaluj' );
add_action( 'plugins_loaded', function () { if ( get_option( 'gk_nl_db' ) !== GK_NL_WERSJA ) { gk_nl_instaluj(); } } );

function gk_nl_kontakt( $pole, $wartosc ) {
	global $wpdb;
	$pole = in_array( $pole, array( 'email', 'token', 'id' ), true ) ? $pole : 'email';
	$r    = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . gk_nl_tabela() . " WHERE $pole = %s", $wartosc ), ARRAY_A ); // phpcs:ignore
	if ( $r ) {
		$r['wyslane'] = $r['wyslane'] ? (array) json_decode( $r['wyslane'], true ) : array();
		$r['kolejka'] = $r['kolejka'] ? (array) json_decode( $r['kolejka'], true ) : array();
		$r['tagi']    = $r['tagi'] ? (array) json_decode( $r['tagi'], true ) : array();
	}
	return $r;
}

function gk_nl_zapisz( array $k ) {
	global $wpdb;
	$dane = $k;
	foreach ( array( 'wyslane', 'kolejka', 'tagi' ) as $j ) { if ( isset( $dane[ $j ] ) ) { $dane[ $j ] = wp_json_encode( $dane[ $j ] ); } }
	unset( $dane['id'] );
	if ( ! empty( $k['id'] ) ) {
		$wpdb->update( gk_nl_tabela(), $dane, array( 'id' => (int) $k['id'] ) );
		return (int) $k['id'];
	}
	$wpdb->insert( gk_nl_tabela(), $dane );
	return (int) $wpdb->insert_id;
}

function gk_nl_ip() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '';
}

function gk_nl_log( $txt ) {
	$l   = (array) get_option( 'gk_nl_log', array() );
	$l[] = wp_date( 'Y-m-d H:i' ) . ' ' . $txt;
	update_option( 'gk_nl_log', array_slice( $l, -60 ), false );
}

/* ─────────────────────────── Zapis i potwierdzenie ─────────────────────────── */
/**
 * Zapis (zawsze z potwierdzeniem mailem). Zwraca 'potwierdz' | 'juz' | 'blad'.
 * Ten sam komunikat dla każdego adresu, żeby nie zdradzać, kto jest na liście.
 */
function gk_nl_zapis( $email, $imie, $zrodlo, $user_id = 0 ) {
	$email = strtolower( sanitize_email( $email ) );
	if ( ! is_email( $email ) || ! gk_nl_aktywny() ) { return 'blad'; }
	$k = gk_nl_kontakt( 'email', $email );
	if ( $k && 'subscribed' === $k['status'] ) { return 'juz'; }
	if ( $k && 'pending' === $k['status'] && $k['utworzono'] > time() - 10 * MINUTE_IN_SECONDS ) { return 'potwierdz'; } // bez spamowania potwierdzeniami
	$k = array_merge( $k ? $k : array( 'wyslane' => array(), 'kolejka' => array(), 'tagi' => array() ), array(
		'email' => $email, 'imie' => sanitize_text_field( mb_substr( (string) $imie, 0, 60 ) ), 'status' => 'pending', 'zrodlo' => sanitize_key( $zrodlo ),
		'token' => bin2hex( random_bytes( 16 ) ), 'user_id' => (int) $user_id, 'utworzono' => time(), 'ip_zapis' => gk_nl_ip(), 'wypisano' => 0,
	) );
	if ( ! $k['user_id'] && ( $u = get_user_by( 'email', $email ) ) ) { $k['user_id'] = $u->ID; }
	$k['id'] = gk_nl_zapisz( $k );
	gk_nl_wyslij( $k, 'P0', true );
	return 'potwierdz';
}

function gk_nl_potwierdz( $token ) {
	$k = gk_nl_kontakt( 'token', $token );
	if ( ! $k ) { return null; }
	if ( 'pending' === $k['status'] ) {
		if ( $k['utworzono'] < time() - 7 * DAY_IN_SECONDS ) { return 'wygasl'; }
		$k['status']       = 'subscribed';
		$k['potwierdzono'] = time();
		$k['ip_potw']      = gk_nl_ip();
		$k['tagi']         = array_values( array_unique( array_merge( $k['tagi'], array( 0 === strpos( $k['zrodlo'], 'akademia' ) ? 'akademia' : 'lead' ) ) ) );
		gk_nl_zapisz( $k );
		if ( $k['user_id'] ) { gk_nl_bez_petli( function () use ( $k ) { update_user_meta( $k['user_id'], 'gk_ak_zgoda', time() ); } ); }
		gk_nl_wyslij( $k, 'W1' );
		$k = gk_nl_kontakt( 'token', $token );
		$k['seria_next'] = gk_nl_termin( $k['potwierdzono'], 1 );
		gk_nl_zapisz( $k );
		gk_nl_mc_kontakt( $k );
		gk_nl_log( 'potwierdzono zapis (' . $k['zrodlo'] . ')' );
	}
	return $k;
}

function gk_nl_wypisz( $k, $z_mailchimpa = false ) {
	if ( ! $k || 'unsubscribed' === $k['status'] ) { return; }
	$k['status']     = 'unsubscribed';
	$k['wypisano']   = time();
	$k['seria_next'] = 0;
	$k['kolejka']    = array();
	gk_nl_zapisz( $k );
	if ( $k['user_id'] ) { gk_nl_bez_petli( function () use ( $k ) { delete_user_meta( $k['user_id'], 'gk_ak_zgoda' ); } ); }
	if ( ! $z_mailchimpa ) { gk_nl_mc( 'PATCH', 'members/' . md5( $k['email'] ), array( 'status' => 'unsubscribed' ) ); }
	gk_nl_log( 'wypis' . ( $z_mailchimpa ? ' (z Mailchimpa)' : '' ) );
}

/** Zmiany zgody wywołane przez nas nie uruchamiają ponownie haków. */
function gk_nl_bez_petli( $fn ) { $GLOBALS['gk_nl_wewn'] = true; $fn(); $GLOBALS['gk_nl_wewn'] = false; }

/** Termin: dzień N po potwierdzeniu, 7:30 czasu polskiego. */
function gk_nl_termin( $od, $dni ) {
	$dt = ( new DateTimeImmutable( '@' . (int) $od ) )->setTimezone( wp_timezone() )->setTime( 7, 30 )->modify( '+' . (int) $dni . ' days' );
	return $dt->getTimestamp();
}

/* ─────────────────────────── Wysyłka ─────────────────────────── */
function gk_nl_dane( array $k ) {
	$d = array( 'lekcje' => 0, 'seria' => 0, 'darmowe_ok' => false, 'darmowe_zostalo' => 0, 'pro' => false, 'kupon_kod' => '', 'kupon_proc' => 0, 'kupon_do' => 0,
		'kupil_strategia' => false, 'kupil_planer' => false, 'kupil_pakiet' => false, 'ma_konto' => (bool) $k['user_id'], 'url_pro' => '',
		'url_potwierdz' => add_query_arg( 'gk_nl_potwierdz', $k['token'], home_url( '/' ) ) );
	$uid = (int) $k['user_id'];
	if ( $uid && function_exists( 'gk_ak_postep' ) ) {
		$p                    = gk_ak_postep( $uid );
		$d['lekcje']          = count( $p['lekcje'] );
		$d['seria']           = gk_nl_seria_dni( $p['dni'] );
		$darm                 = gk_nl_darmowe( $p['lekcje'] );
		$d['darmowe_ok']      = $darm[0] > 0 && $darm[1] >= $darm[0];
		$d['darmowe_zostalo'] = max( 0, $darm[0] - $darm[1] );
		$d['pro']             = function_exists( 'gk_ak_jest_pro' ) && gk_ak_jest_pro( $uid );
		if ( ! empty( $p['kupon'] ) ) {
			$d['kupon_kod']  = $p['kupon']['kod'];
			$d['kupon_proc'] = (int) $p['kupon']['procent'];
			$d['kupon_do']   = (int) $p['kupon']['data'] + 7 * DAY_IN_SECONDS;
		}
		$pid = (int) get_option( 'gk_ak_pro_produkt', 0 );
		if ( $pid ) { $d['url_pro'] = home_url( '/zamowienie/?add-to-cart=' . $pid ); }
	}
	if ( function_exists( 'wc_customer_bought_product' ) ) {
		foreach ( GK_NL_PRODUKTY as $n => $pid ) { $d[ 'kupil_' . $n ] = wc_customer_bought_product( $k['email'], $uid, $pid ); }
	}
	return $d;
}

/** Liczba darmowych lekcji (działy 1–2) i ukończonych z nich. */
function gk_nl_darmowe( array $lekcje_ukonczone ) {
	$dzialy = defined( 'GK_AK_DARMOWE_DZIALY' ) ? (int) GK_AK_DARMOWE_DZIALY : 2;
	$all    = function_exists( 'gk_ak_lekcje_kursu' ) ? (array) gk_ak_lekcje_kursu() : array();
	$darm   = array_filter( $all, function ( $id ) use ( $dzialy ) { return preg_match( '/^u(\d+)l/', (string) $id, $m ) && (int) $m[1] <= $dzialy; } );
	$zrob   = array_intersect( $darm, array_keys( $lekcje_ukonczone ) );
	return array( count( $darm ), count( $zrob ) );
}

/** Seria: kolejne aktywne dni kończące się dziś albo wczoraj. */
function gk_nl_seria_dni( array $dni ) {
	$dni = array_flip( $dni );
	$t   = time();
	if ( ! isset( $dni[ wp_date( 'Y-m-d', $t ) ] ) ) { $t -= DAY_IN_SECONDS; }
	$n = 0;
	while ( isset( $dni[ wp_date( 'Y-m-d', $t ) ] ) ) { $n++; $t -= DAY_IN_SECONDS; }
	return $n;
}

function gk_nl_url_wypis( $k ) { return add_query_arg( 'gk_nl_wypis', $k['token'], home_url( '/' ) ); }
function gk_nl_url_pref( $k ) { return add_query_arg( 'gk_nl_pref', $k['token'], home_url( '/' ) ); }

/** HTML i tekst maila (wspólne dla wysyłki i podglądu). */
function gk_nl_zbuduj( $kod, array $k, array $d ) {
	list( $temat, $a ) = gk_nl_tresc( $kod, $k, $d );
	if ( ! $temat ) { return null; }
	$wypis = 'P0' === $kod ? '' : ' <a href="' . esc_url( gk_nl_url_wypis( $k ) ) . '" style="color:#7A6580;">Wypisz się jednym kliknięciem</a> · <a href="' . esc_url( gk_nl_url_pref( $k ) ) . '" style="color:#7A6580;">Ustaw, jak często piszę</a>';
	$html  = str_replace( '[[GK_WYPIS]]', $wypis, gk_ml_layout( $a ) );
	$a['powod'] = str_replace( '[[GK_WYPIS]]', '', $a['powod'] );
	$tekst = gk_ml_tekst( $a ) . ( 'P0' === $kod ? '' : 'Wypisz się: ' . gk_nl_url_wypis( $k ) . "\nUstawienia: " . gk_nl_url_pref( $k ) . "\n" );
	return array( $temat, $html, $tekst );
}

/** Wysyła mail do kontaktu. $wymus pomija sprawdzenie statusu (potwierdzenie P0, test). */
function gk_nl_wyslij( array $k, $kod, $wymus = false, $do = '' ) {
	if ( ! $wymus && 'subscribed' !== $k['status'] ) { return false; }
	if ( ! $do && 'tylko_akademia' === $k['pref'] && in_array( $kod, gk_nl_z_oferta(), true ) ) { return false; }
	$m = gk_nl_zbuduj( $kod, $k, gk_nl_dane( $k ) );
	if ( ! $m ) { return false; }
	$o   = gk_nl_opcje();
	$hdr = array(
		'Content-Type: text/html; charset=UTF-8',
		'From: ' . $o['nadawca'] . ' <' . GK_NL_NADAWCA . '>',
		'Reply-To: ' . $o['nadawca'] . ' <' . GK_NL_NADAWCA . '>',
	);
	if ( 'P0' !== $kod ) {
		$hdr[] = 'List-Unsubscribe: <' . gk_nl_url_wypis( $k ) . '>, <mailto:' . GK_NL_NADAWCA . '?subject=Wypis%20z%20newslettera>';
		$hdr[] = 'List-Unsubscribe-Post: List-Unsubscribe=One-Click';
	}
	$GLOBALS['gk_ml_alt'] = $m[2];
	$ok = wp_mail( $do ? $do : $k['email'], ( $do ? '[TEST] ' : '' ) . $m[0], $m[1], $hdr );
	if ( $ok && ! $do && ! empty( $k['id'] ) ) {
		$k                 = gk_nl_kontakt( 'id', $k['id'] );
		$k['wyslane'][ $kod ] = time();
		$k['ostatni_mail'] = time();
		gk_nl_zapisz( $k );
	}
	if ( ! $ok ) { gk_nl_log( "błąd wysyłki $kod" ); }
	return $ok;
}

/* ─────────────────────────── Harmonogram ─────────────────────────── */
add_action( 'init', function () {
	if ( ! wp_next_scheduled( 'gk_nl_cron' ) ) { wp_schedule_event( time() + 300, 'hourly', 'gk_nl_cron' ); }
} );
register_deactivation_hook( __FILE__, function () { wp_clear_scheduled_hook( 'gk_nl_cron' ); } );

add_action( 'gk_nl_cron', 'gk_nl_harmonogram' );
function gk_nl_harmonogram() {
	if ( ! gk_nl_aktywny() ) { return; }
	global $wpdb;
	$t     = gk_nl_tabela();
	$teraz = time();
	$limit = GK_NL_LIMIT_RUN;
	$seria = gk_nl_seria();
	$kody  = array_keys( $seria );

	// 1) Seria powitalna.
	$ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM $t WHERE status = 'subscribed' AND seria_next > 0 AND seria_next <= %d ORDER BY seria_next LIMIT %d", $teraz, $limit ) ); // phpcs:ignore
	foreach ( $ids as $id ) {
		$k = gk_nl_kontakt( 'id', $id );
		foreach ( $kody as $i => $kod ) {
			if ( isset( $k['wyslane'][ $kod ] ) ) { continue; }
			if ( gk_nl_termin( $k['potwierdzono'], $seria[ $kod ] ) > $teraz ) { break; }
			gk_nl_wyslij( $k, $kod );
			$k = gk_nl_kontakt( 'id', $id );
			$k['wyslane'][ $kod ] = isset( $k['wyslane'][ $kod ] ) ? $k['wyslane'][ $kod ] : -1; // -1 = pominięty (nie dotyczy)
			$limit--;
			break; // najwyżej jeden mail serii na uruchomienie
		}
		$nast = 0;
		foreach ( $kody as $kod ) { if ( ! isset( $k['wyslane'][ $kod ] ) ) { $nast = gk_nl_termin( $k['potwierdzono'], $seria[ $kod ] ); break; } }
		$k['seria_next'] = $nast;
		gk_nl_zapisz( $k );
		if ( $limit <= 0 ) { return; }
	}

	// 2) Kolejka po zakupach (Z6, Z7).
	$ids = $wpdb->get_col( "SELECT id FROM $t WHERE status = 'subscribed' AND kolejka IS NOT NULL AND kolejka NOT IN ('', '[]')" ); // phpcs:ignore
	foreach ( $ids as $id ) {
		$k     = gk_nl_kontakt( 'id', $id );
		$zost  = array();
		foreach ( $k['kolejka'] as $poz ) {
			if ( $poz[0] <= $teraz && $limit > 0 && ! gk_nl_dzis_wyslano( $k ) ) {
				gk_nl_wyslij( $k, $poz[1] );
				$k = gk_nl_kontakt( 'id', $id );
				$limit--;
			} else {
				$zost[] = $poz;
			}
		}
		$k['kolejka'] = $zost;
		gk_nl_zapisz( $k );
	}

	// 3) Raz dziennie po 8:00: maile wyzwalane nauką + synchronizacja Mailchimpa.
	if ( (int) wp_date( 'G' ) >= 8 && get_option( 'gk_nl_dzien' ) !== wp_date( 'Y-m-d' ) ) {
		update_option( 'gk_nl_dzien', wp_date( 'Y-m-d' ), false );
		gk_nl_wyzwalacze( $limit );
		gk_nl_mc_synchronizuj();
	}
}

function gk_nl_dzis_wyslano( $k ) { return $k['ostatni_mail'] && wp_date( 'Y-m-d', (int) $k['ostatni_mail'] ) === wp_date( 'Y-m-d' ); }

/** Z1–Z5: tylko osoby z kontem w Akademii, najwyżej 1 mail marketingowy dziennie, nie w trakcie serii powitalnej (poza Z5). */
function gk_nl_wyzwalacze( $limit ) {
	if ( ! function_exists( 'gk_ak_postep' ) ) { return; }
	global $wpdb;
	$ids = $wpdb->get_col( "SELECT id FROM " . gk_nl_tabela() . " WHERE status = 'subscribed' AND user_id > 0" ); // phpcs:ignore
	foreach ( $ids as $id ) {
		if ( $limit <= 0 ) { return; }
		$k = gk_nl_kontakt( 'id', $id );
		if ( gk_nl_dzis_wyslano( $k ) ) { continue; }
		$p    = gk_ak_postep( (int) $k['user_id'] );
		$d    = gk_nl_dane( $k );
		$w    = $k['wyslane'];
		$kod  = '';
		$ost  = $p['dni'] ? max( $p['dni'] ) : '';
		$przerwa = $ost ? (int) floor( ( strtotime( wp_date( 'Y-m-d' ) ) - strtotime( $ost ) ) / DAY_IN_SECONDS ) : 0;
		$w_serii = (int) $k['seria_next'] > 0;
		$kup = ! empty( $p['kupon'] ) ? $p['kupon'] : null;
		if ( $kup && ! isset( $w[ 'Z5_' . $kup['kod'] ] ) ) {
			$do = (int) $kup['data'] + 7 * DAY_IN_SECONDS - time();
			if ( $do > DAY_IN_SECONDS && $do <= 2 * DAY_IN_SECONDS && ! gk_nl_kupon_uzyty( $kup['kod'] ) ) { $kod = 'Z5'; }
		}
		if ( ! $kod && ! $w_serii ) {
			if ( $d['darmowe_ok'] && ! $d['pro'] && ! isset( $w['Z1'] ) ) { $kod = 'Z1'; }
			elseif ( isset( $w['Z1'] ) && ! isset( $w['Z1b'] ) && ! $d['pro'] && $w['Z1'] < time() - 3 * DAY_IN_SECONDS ) { $kod = 'Z1b'; }
			elseif ( $d['seria'] >= 30 && ! isset( $w['Z2_30'] ) ) { $kod = 'Z2_30'; }
			elseif ( $d['seria'] >= 7 && ! isset( $w['Z2_7'] ) ) { $kod = 'Z2_7'; }
			elseif ( $d['lekcje'] > 0 && $przerwa >= 21 && ( ! isset( $w['Z4'] ) || $w['Z4'] < strtotime( $ost ) ) ) { $kod = 'Z4'; }
			elseif ( $d['lekcje'] > 0 && $przerwa >= 7 && $przerwa < 21 && ( ! isset( $w['Z3'] ) || $w['Z3'] < strtotime( $ost ) ) ) { $kod = 'Z3'; }
		}
		if ( $kod ) {
			gk_nl_wyslij( $k, $kod );
			if ( 'Z5' === $kod ) { $k = gk_nl_kontakt( 'id', $id ); $k['wyslane'][ 'Z5_' . $kup['kod'] ] = time(); gk_nl_zapisz( $k ); }
			$limit--;
		}
	}
}

function gk_nl_kupon_uzyty( $kod ) {
	if ( ! class_exists( 'WC_Coupon' ) ) { return false; }
	$c = new WC_Coupon( $kod );
	return $c->get_id() && $c->get_usage_count() > 0;
}

/* Zakupy: kolejka Z6 / Z7 i tagi w Mailchimpie (tylko osoby zapisane). */
add_action( 'woocommerce_order_status_completed', 'gk_nl_po_zakupie' );
add_action( 'woocommerce_order_status_processing', 'gk_nl_po_zakupie' );
function gk_nl_po_zakupie( $order_id ) {
	$o = function_exists( 'wc_get_order' ) ? wc_get_order( $order_id ) : null;
	if ( ! $o || $o->get_meta( '_gk_nl_przetworzone' ) ) { return; }
	$o->update_meta_data( '_gk_nl_przetworzone', 1 );
	$o->save();
	$k = gk_nl_kontakt( 'email', strtolower( $o->get_billing_email() ) );
	if ( ! $k || 'subscribed' !== $k['status'] ) { return; }
	$ids  = array();
	foreach ( $o->get_items() as $it ) { $ids[] = (int) $it->get_product_id(); }
	$t    = time();
	$tagi = array( 'klient' );
	$dodaj = function ( $dni, $kod ) use ( &$k, $t ) { $k['kolejka'][] = array( gk_nl_termin( $t, $dni ), $kod ); };
	if ( in_array( GK_NL_PRODUKTY['pakiet'], $ids, true ) ) { $tagi[] = 'kupil_pakiet'; $dodaj( 2, 'Z7a' ); $dodaj( 10, 'Z7b' ); $dodaj( 21, 'Z7c' ); }
	elseif ( in_array( GK_NL_PRODUKTY['strategia'], $ids, true ) ) { $tagi[] = 'kupil_strategia'; $dodaj( 1, 'Z6a' ); $dodaj( 7, 'Z6b' ); $dodaj( 14, 'Z6c' ); }
	if ( in_array( GK_NL_PRODUKTY['planer'], $ids, true ) ) { $tagi[] = 'kupil_planer'; }
	if ( in_array( (int) get_option( 'gk_ak_pro_produkt', 0 ), $ids, true ) ) { $tagi[] = 'kupil_pro'; }
	$k['tagi'] = array_values( array_unique( array_merge( $k['tagi'], $tagi ) ) );
	gk_nl_zapisz( $k );
	gk_nl_mc_tagi( $k['email'], $tagi );
}

/* ─────────────────────────── Mailchimp ─────────────────────────── */
function gk_nl_mc( $metoda, $sciezka, $body = null, $lista = true ) {
	$o = gk_nl_opcje();
	if ( empty( $o['api'] ) || ! preg_match( '/-([a-z]+\d+)$/', $o['api'], $m ) || ( $lista && empty( $o['lista'] ) ) ) { return null; }
	$url = 'https://' . $m[1] . '.api.mailchimp.com/3.0/' . ( $lista ? 'lists/' . rawurlencode( $o['lista'] ) . '/' : '' ) . ltrim( $sciezka, '/' );
	$r   = wp_remote_request( $url, array(
		'method'  => $metoda,
		'timeout' => 12,
		'headers' => array( 'Authorization' => 'Basic ' . base64_encode( 'gk:' . $o['api'] ), 'Content-Type' => 'application/json' ),
		'body'    => null === $body ? null : wp_json_encode( $body ),
	) );
	if ( is_wp_error( $r ) ) { gk_nl_log( 'Mailchimp: ' . $r->get_error_message() ); return null; }
	$kod = wp_remote_retrieve_response_code( $r );
	$j   = json_decode( wp_remote_retrieve_body( $r ), true );
	if ( $kod >= 300 ) { gk_nl_log( 'Mailchimp ' . $kod . ': ' . ( isset( $j['detail'] ) ? $j['detail'] : '' ) ); return null; }
	return is_array( $j ) ? $j : array();
}

function gk_nl_mc_kontakt( $k ) {
	$merge = array( 'FNAME' => $k['imie'], 'ZRODLO' => $k['zrodlo'] );
	if ( $k['user_id'] && function_exists( 'gk_ak_postep' ) ) { $merge['AK_LEKCJE'] = count( gk_ak_postep( (int) $k['user_id'] )['lekcje'] ); }
	gk_nl_mc( 'PUT', 'members/' . md5( $k['email'] ), array(
		'email_address' => $k['email'], 'status_if_new' => 'subscribed', 'status' => 'subscribed', 'merge_fields' => $merge,
		'ip_signup' => $k['ip_zapis'], 'timestamp_signup' => gmdate( 'Y-m-d H:i:s', (int) $k['utworzono'] ),
		'ip_opt' => $k['ip_potw'], 'timestamp_opt' => gmdate( 'Y-m-d H:i:s', (int) $k['potwierdzono'] ),
	) );
	gk_nl_mc_tagi( $k['email'], $k['tagi'] );
}

function gk_nl_mc_tagi( $email, array $tagi ) {
	if ( ! $tagi ) { return; }
	gk_nl_mc( 'POST', 'members/' . md5( strtolower( $email ) ) . '/tags', array( 'tags' => array_map( function ( $t ) { return array( 'name' => $t, 'status' => 'active' ); }, array_values( $tagi ) ) ) );
}

/** Raz dziennie: liczba lekcji i preferencja do Mailchimpa (do segmentacji wydań). */
function gk_nl_mc_synchronizuj() {
	$o = gk_nl_opcje();
	if ( empty( $o['api'] ) || empty( $o['lista'] ) || ! function_exists( 'gk_ak_postep' ) ) { return; }
	global $wpdb;
	$rows = $wpdb->get_results( "SELECT email, user_id, pref FROM " . gk_nl_tabela() . " WHERE status = 'subscribed' AND user_id > 0 LIMIT 300", ARRAY_A ); // phpcs:ignore
	foreach ( $rows as $r ) {
		gk_nl_mc( 'PATCH', 'members/' . md5( $r['email'] ), array( 'merge_fields' => array( 'AK_LEKCJE' => count( gk_ak_postep( (int) $r['user_id'] )['lekcje'] ), 'PREF' => $r['pref'] ) ) );
	}
}

/** Pola listy potrzebne do segmentacji (tworzone przy zapisie ustawień). */
function gk_nl_mc_pola() {
	$jest = gk_nl_mc( 'GET', 'merge-fields?count=50' );
	if ( null === $jest ) { return false; }
	$tagi = wp_list_pluck( isset( $jest['merge_fields'] ) ? $jest['merge_fields'] : array(), 'tag' );
	foreach ( array( 'ZRODLO' => array( 'Źródło zapisu', 'text' ), 'AK_LEKCJE' => array( 'Akademia: lekcje', 'number' ), 'PREF' => array( 'Preferencja', 'text' ) ) as $tag => $p ) {
		if ( ! in_array( $tag, $tagi, true ) ) { gk_nl_mc( 'POST', 'merge-fields', array( 'tag' => $tag, 'name' => $p[0], 'type' => $p[1], 'public' => false ) ); }
	}
	return true;
}

/* Webhook Mailchimpa: wypis i usunięcie adresu wracają do WordPressa. */
add_action( 'rest_api_init', function () {
	register_rest_route( 'guliwera/v1', '/mailchimp/(?P<sekret>[A-Za-z0-9]+)', array(
		'methods'             => array( 'GET', 'POST' ),
		'permission_callback' => function ( $r ) { $o = gk_nl_opcje(); return $o['sekret'] && hash_equals( $o['sekret'], (string) $r['sekret'] ); },
		'callback'            => function ( $r ) {
			if ( 'POST' === $r->get_method() ) {
				$typ  = (string) $r->get_param( 'type' );
				$data = (array) $r->get_param( 'data' );
				if ( in_array( $typ, array( 'unsubscribe', 'cleaned' ), true ) && ! empty( $data['email'] ) ) {
					gk_nl_wypisz( gk_nl_kontakt( 'email', strtolower( sanitize_email( $data['email'] ) ) ), true );
				}
			}
			return array( 'ok' => true );
		},
	) );
	register_rest_route( 'guliwera/v1', '/zapis', array(
		'methods'             => 'POST',
		'permission_callback' => '__return_true',
		'callback'            => 'gk_nl_rest_zapis',
	) );
} );

/* ─────────────────────────── Wejścia na listę ─────────────────────────── */
function gk_nl_rest_zapis( $r ) {
	if ( ! gk_nl_aktywny() ) { return new WP_Error( 'gk_nl', 'Zapisy ruszą wkrótce.', array( 'status' => 503 ) ); }
	if ( '' !== (string) $r->get_param( 'www' ) ) { return array( 'ok' => true, 'komunikat' => 'Sprawdź skrzynkę.' ); } // pułapka na boty
	if ( ! $r->get_param( 'zgoda' ) ) { return new WP_Error( 'gk_nl', 'Zaznacz zgodę na newsletter, żeby dostać pakiet.', array( 'status' => 400 ) ); }
	$ip  = gk_nl_ip();
	$klu = 'gk_nl_ip_' . md5( $ip );
	$n   = (int) get_transient( $klu );
	if ( $n >= 5 ) { return new WP_Error( 'gk_nl', 'Za dużo prób. Spróbuj za godzinę.', array( 'status' => 429 ) ); }
	set_transient( $klu, $n + 1, HOUR_IN_SECONDS );
	$wynik = gk_nl_zapis( (string) $r->get_param( 'email' ), (string) $r->get_param( 'imie' ), 'formularz_pakiet' );
	if ( 'blad' === $wynik ) { return new WP_Error( 'gk_nl', 'Sprawdź adres e-mail.', array( 'status' => 400 ) ); }
	return array( 'ok' => true, 'komunikat' => 'Sprawdź skrzynkę: wysłałam mail z linkiem potwierdzającym. Pakiet przyjdzie zaraz po kliknięciu. Nie widzisz maila? Zajrzyj do zakładki Oferty albo Spam.' );
}

// Formularz: [gk_newsletter] na stronie /pakiet-startowy/.
add_shortcode( 'gk_newsletter', function () {
	if ( ! gk_nl_aktywny() ) { return '<div class="gk-nl gk-nl-ok">Zapisy na Pakiet startowy ruszą wkrótce.</div>'; }
	$url = esc_url( rest_url( 'guliwera/v1/zapis' ) );
	ob_start(); ?>
<form class="gk-nl" id="gk-nl" novalidate>
	<label for="gk-nl-imie">Imię <small>(opcjonalnie)</small></label><input id="gk-nl-imie" name="imie" type="text" autocomplete="given-name" maxlength="60">
	<label for="gk-nl-email">E-mail</label><input id="gk-nl-email" name="email" type="email" autocomplete="email" required>
	<input name="www" type="text" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px">
	<label class="gk-nl-zg"><input id="gk-nl-zgoda" name="zgoda" type="checkbox" value="1"> Chcę dostać Pakiet startowy i newsletter Guliwera 4 (co 2 tygodnie: strategia, narzędzia, lekcje z Akademii i oferty). Wypiszę się jednym kliknięciem. Szczegóły: <a href="<?php echo esc_url( home_url( '/polityka-prywatnosci/' ) ); ?>">polityka prywatności</a>.</label>
	<button type="submit">Wyślij mi Pakiet startowy</button>
	<p class="gk-nl-kom" role="status" aria-live="polite"></p>
</form>
<style>.gk-nl{display:grid;gap:8px;max-width:440px;font-family:"DM Sans",Arial,sans-serif;color:#3A2340}.gk-nl label{font-weight:600;font-size:15px}.gk-nl small{font-weight:400;color:#7A6580}.gk-nl input[type=text],.gk-nl input[type=email]{font:inherit;font-size:16px;padding:12px 14px;border:2px solid #F0DDCF;border-radius:12px;background:#fff}.gk-nl input:focus{outline:none;border-color:#E8512A}.gk-nl .gk-nl-zg{font-weight:400;font-size:13.5px;line-height:1.5;display:flex;gap:8px;align-items:flex-start;margin-top:4px}.gk-nl .gk-nl-zg input{margin-top:3px;flex:none;width:18px;height:18px;accent-color:#E8512A}.gk-nl button{font:700 17px "DM Sans",Arial,sans-serif;background:#E8512A;color:#fff;border:0;border-radius:999px;padding:15px 26px;cursor:pointer;margin-top:6px}.gk-nl button:hover{background:#C9431F}.gk-nl button[disabled]{opacity:.6}.gk-nl-kom{margin:4px 0 0;font-size:15px}.gk-nl-kom.ok{background:#EDE5FB;border-radius:12px;padding:12px 14px}.gk-nl-kom.blad{color:#B3261E}</style>
<script>(function(){var f=document.getElementById('gk-nl');if(!f){return;}f.addEventListener('submit',function(e){e.preventDefault();var k=f.querySelector('.gk-nl-kom'),b=f.querySelector('button');k.className='gk-nl-kom';if(!f.zgoda.checked){k.textContent='Zaznacz zgodę na newsletter, żeby dostać pakiet.';k.className+=' blad';return;}b.disabled=true;fetch('<?php echo $url; ?>',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({imie:f.imie.value,email:f.email.value,zgoda:1,www:f.www.value})}).then(function(r){return r.json().then(function(j){return {ok:r.ok,j:j};});}).then(function(x){k.textContent=x.ok?x.j.komunikat:(x.j.message||'Coś poszło nie tak. Spróbuj jeszcze raz.');k.className+=x.ok?' ok':' blad';if(x.ok){f.reset();window.dataLayer=window.dataLayer||[];window.dataLayer.push({event:'gk_newsletter_zapis'});}b.disabled=false;}).catch(function(){k.textContent='Brak połączenia. Spróbuj jeszcze raz.';k.className+=' blad';b.disabled=false;});});})();</script>
	<?php
	return ob_get_clean();
} );

// Rejestracja konta (Moje konto): niezaznaczone pole zgody.
add_action( 'woocommerce_register_form', function () {
	if ( ! gk_nl_aktywny() ) { return; }
	echo '<p class="form-row" style="font-size:14px;line-height:1.5"><label style="display:flex;gap:8px;align-items:flex-start;font-weight:400"><input type="checkbox" name="gk_nl_zgoda" value="1" style="margin-top:4px;accent-color:#E8512A"> <span>Wyślij mi darmowy <b>Pakiet startowy Kierunek Zysk</b> i newsletter co 2 tygodnie (strategia, narzędzia, lekcje z Akademii i oferty). Wypiszę się jednym kliknięciem.</span></label></p>';
}, 25 );
add_action( 'woocommerce_created_customer', function ( $uid ) {
	if ( empty( $_POST['gk_nl_zgoda'] ) ) { return; } // phpcs:ignore WordPress.Security.NonceVerification -- formularz WooCommerce ma własny nonce
	$u = get_userdata( $uid );
	if ( $u ) { gk_nl_zapis( $u->user_email, $u->first_name, 'akademia_rejestracja', $uid ); }
}, 30 );

// Przełącznik zgody w Profilu Akademii (meta gk_ak_zgoda).
add_action( 'added_user_meta', 'gk_nl_zgoda_zmieniona', 10, 3 );
add_action( 'updated_user_meta', 'gk_nl_zgoda_zmieniona', 10, 3 );
function gk_nl_zgoda_zmieniona( $mid, $uid, $klucz ) {
	if ( 'gk_ak_zgoda' !== $klucz || ! empty( $GLOBALS['gk_nl_wewn'] ) ) { return; }
	$u = get_userdata( $uid );
	if ( $u ) { gk_nl_zapis( $u->user_email, $u->first_name, 'akademia_profil', $uid ); }
}
add_action( 'deleted_user_meta', function ( $mids, $uid, $klucz ) {
	if ( 'gk_ak_zgoda' !== $klucz || ! empty( $GLOBALS['gk_nl_wewn'] ) ) { return; }
	$u = get_userdata( $uid );
	if ( $u ) { gk_nl_wypisz( gk_nl_kontakt( 'email', strtolower( $u->user_email ) ) ); }
}, 10, 3 );

/* ─────────────────────────── Strony: potwierdzenie, pakiet, wypis, preferencje ─────────────────────────── */
add_action( 'template_redirect', function () {
	$g = function ( $n ) { return isset( $_GET[ $n ] ) ? preg_replace( '/[^a-f0-9]/', '', (string) wp_unslash( $_GET[ $n ] ) ) : ''; }; // phpcs:ignore
	if ( $t = $g( 'gk_nl_potwierdz' ) ) {
		$k = gk_nl_potwierdz( $t );
		if ( 'wygasl' === $k ) { gk_nl_strona( 'Link wygasł', '<p>Link potwierdzający działa 7 dni. Zapisz się jeszcze raz, a wyślę nowy.</p>', array( 'Zapisz się ponownie', home_url( '/pakiet-startowy/' ) ) ); }
		if ( ! $k ) { gk_nl_strona( 'Nie znaleziono zapisu', '<p>Ten link jest nieprawidłowy. Zapisz się jeszcze raz.</p>', array( 'Pakiet startowy', home_url( '/pakiet-startowy/' ) ) ); }
		gk_nl_strona_pakiet( $k, true );
	}
	if ( $t = $g( 'gk_nl_pakiet' ) ) {
		$k = gk_nl_kontakt( 'token', $t );
		if ( ! $k || 'subscribed' !== $k['status'] ) { gk_nl_strona( 'Pakiet jest dla osób zapisanych', '<p>Zapisz się, a link do pakietu przyjdzie mailem.</p>', array( 'Pakiet startowy', home_url( '/pakiet-startowy/' ) ) ); }
		gk_nl_strona_pakiet( $k, false );
	}
	if ( $t = $g( 'gk_nl_wypis' ) ) {
		$k = gk_nl_kontakt( 'token', $t );
		if ( 'POST' === $_SERVER['REQUEST_METHOD'] || ! empty( $_GET['tak'] ) ) { // POST = wypis jednym kliknięciem z programu pocztowego
			gk_nl_wypisz( $k );
			if ( 'POST' === $_SERVER['REQUEST_METHOD'] && empty( $_POST['z_formularza'] ) ) { status_header( 200 ); exit; } // phpcs:ignore
			gk_nl_strona( 'Wypisano', '<p>Nie dostaniesz już newslettera ani maili z serii. Maile o zamówieniach i koncie przychodzą nadal, bo są systemowe.</p><p>Zmiana zdania? Zapisać się możesz w każdej chwili na stronie Pakietu startowego.</p>' );
		}
		gk_nl_strona( 'Wypisać się z newslettera?', '<form method="post"><input type="hidden" name="z_formularza" value="1"><p>Jedno kliknięcie i nie dostaniesz już maili marketingowych od Guliwera 4.</p><p><button type="submit" class="gk-b">Tak, wypisz mnie</button></p></form><p>Wolisz rzadziej? <a href="' . esc_url( gk_nl_url_pref( array( 'token' => $t ) ) ) . '">Ustaw, jak często piszę</a>.</p>' );
	}
	if ( $t = $g( 'gk_nl_pref' ) ) {
		$k = gk_nl_kontakt( 'token', $t );
		if ( ! $k ) { gk_nl_strona( 'Nie znaleziono zapisu', '<p>Ten link jest nieprawidłowy.</p>' ); }
		$opcje = array( 'wszystko' => 'Co 2 tygodnie (jak teraz)', 'miesiecznie' => 'Raz w miesiącu', 'tylko_akademia' => 'Tylko Akademia: lekcje i przypomnienia, bez ofert' );
		$info  = '';
		if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['pref'] ) && isset( $opcje[ $_POST['pref'] ] ) ) { // phpcs:ignore
			$k['pref'] = sanitize_key( $_POST['pref'] ); // phpcs:ignore
			gk_nl_zapisz( $k );
			gk_nl_mc( 'PATCH', 'members/' . md5( $k['email'] ), array( 'merge_fields' => array( 'PREF' => $k['pref'] ) ) );
			$info = '<p class="gk-ok">Zapisane. Dziękuję!</p>';
		}
		$h = $info . '<form method="post">';
		foreach ( $opcje as $v => $et ) { $h .= '<label class="gk-r"><input type="radio" name="pref" value="' . esc_attr( $v ) . '"' . checked( $k['pref'], $v, false ) . '> ' . esc_html( $et ) . '</label>'; }
		$h .= '<p><button type="submit" class="gk-b">Zapisz</button></p></form><p><a href="' . esc_url( gk_nl_url_wypis( $k ) ) . '">Wypisz się całkowicie</a></p>';
		gk_nl_strona( 'Jak często mam pisać?', $h );
	}
	if ( $t = $g( 't' ) ) {
		$temat = isset( $_GET['gk_nl_temat'] ) ? sanitize_key( wp_unslash( $_GET['gk_nl_temat'] ) ) : ''; // phpcs:ignore
		if ( $temat && ( $k = gk_nl_kontakt( 'token', $t ) ) && in_array( $temat, array( 'ceny', 'plan', 'sprzedaz', 'zespol', 'czas' ), true ) ) {
			$k['tagi'] = array_values( array_unique( array_merge( $k['tagi'], array( 'temat_' . $temat ) ) ) );
			gk_nl_zapisz( $k );
			gk_nl_mc_tagi( $k['email'], array( 'temat_' . $temat ) );
			gk_nl_strona( 'Dziękuję!', '<p>Zapisane. Następne lekcje i wydania będą częściej o tym, co jest dla Ciebie ważne. Jeśli chcesz dopisać jedno zdanie, odpowiedz na mail.</p>', array( 'Otwórz Akademię', home_url( '/akademia/' ) ) );
		}
	}
} );

function gk_nl_strona_pakiet( $k, $po_potwierdzeniu ) {
	$o     = gk_nl_opcje();
	$pliki = array( array( 'Kalkulator zysku', 'Excel / Arkusze Google', $o['plik_kalk'] ), array( 'Strategia na 1 stronie: przykład', 'PDF, 1 strona', $o['plik_strat'] ), array( 'Przegląd miesiąca w 15 minut', 'PDF, 1 strona', $o['plik_przeg'] ) );
	$h     = $po_potwierdzeniu ? '<p>Zapis potwierdzony. Pakiet jest poniżej, a link do niego masz też w mailu.</p>' : '';
	$h    .= '<div class="gk-pl">';
	foreach ( $pliki as $p ) { $h .= '<a class="gk-plik" href="' . esc_url( $p[2] ? $p[2] : '#' ) . '" download><b>' . esc_html( $p[0] ) . '</b><span>' . esc_html( $p[1] ) . '</span></a>'; }
	$h .= '</div><div class="gk-of"><p><b>Następny krok:</b> szablon <b>Strategia na 1 stronie</b> do wypełnienia dla własnej firmy, z instrukcją. 39 zł.</p><a class="gk-b" href="' . esc_url( gk_nl_utm( function_exists( 'wc_get_product' ) && wc_get_product( 69 ) ? get_permalink( 69 ) : home_url( '/materialy/' ), 'potwierdzenie' ) ) . '">Zobacz szablon</a></div>';
	$h .= '<p>Albo zacznij od lekcji na 5 minut w <a href="' . esc_url( home_url( '/akademia/' ) ) . '">Akademii Guliwera</a>.</p>';
	if ( $po_potwierdzeniu ) { $h .= '<script>window.dataLayer=window.dataLayer||[];window.dataLayer.push({event:"generate_lead",lead_zrodlo:"newsletter_' . esc_js( $k['zrodlo'] ) . '"});</script>'; }
	gk_nl_strona( $po_potwierdzeniu ? 'Gotowe! Oto Twój Pakiet startowy' : 'Twój Pakiet startowy', $h );
}

/** Prosta strona w kolorach marki (bez motywu, nie trafia do cache). */
function gk_nl_strona( $tytul, $html, $cta = null ) {
	nocache_headers();
	header( 'Content-Type: text/html; charset=utf-8' );
	header( 'X-Robots-Tag: noindex' );
	$logo = defined( 'GK_ML_LOGO' ) ? GK_ML_LOGO : '';
	echo '<!doctype html><html lang="pl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex"><title>' . esc_html( $tytul ) . ' · Guliwera 4</title>';
	echo '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600&family=DM+Sans:opsz,wght@9..40,400;9..40,700&display=swap">';
	echo '<style>body{margin:0;background:#FFF6EE;color:#3A2340;font:17px/1.6 "DM Sans",Arial,sans-serif}.w{max-width:620px;margin:0 auto;padding:32px 18px 60px}.l{display:flex;gap:12px;align-items:center;text-decoration:none;color:#3A2340;margin-bottom:22px}.l img{width:44px;height:44px;border-radius:12px}.l b{font:600 19px Fraunces,Georgia,serif;display:block}.l span{font-size:12px;color:#7A6580}.k{background:#fff;border-radius:24px;padding:32px 30px;border:1px solid #F3E3D6;border-top:8px solid #FF6B3D}h1{font:600 clamp(26px,5vw,34px)/1.15 Fraunces,Georgia,serif;margin:0 0 16px}a{color:#E8512A}.gk-b{display:inline-block;background:#E8512A;color:#fff!important;border:0;border-radius:999px;padding:14px 26px;font:700 17px "DM Sans",Arial,sans-serif;text-decoration:none;cursor:pointer}.gk-pl{display:grid;gap:10px;margin:18px 0 22px}.gk-plik{display:flex;justify-content:space-between;gap:12px;align-items:center;border:2px solid #F0DDCF;border-radius:14px;padding:14px 16px;text-decoration:none;color:#3A2340}.gk-plik:hover{border-color:#E8512A}.gk-plik span{font-size:14px;color:#7A6580;white-space:nowrap}.gk-plik b::after{content:" ↓";color:#E8512A}.gk-of{background:#FFEFC7;border-radius:16px;padding:16px 18px;margin-bottom:18px}.gk-of p{margin:0 0 12px}.gk-r{display:flex;gap:10px;align-items:center;margin:10px 0;font-size:16px}.gk-r input{accent-color:#E8512A;width:18px;height:18px}.gk-ok{background:#EDE5FB;border-radius:12px;padding:10px 14px}</style></head><body><div class="w">';
	echo '<a class="l" href="' . esc_url( home_url( '/' ) ) . '">' . ( $logo ? '<img src="' . esc_url( $logo ) . '" alt="">' : '' ) . '<span><b>Guliwera 4</b><span>strategia, produkty cyfrowe i dobry humor</span></span></a>';
	echo '<div class="k"><h1>' . esc_html( $tytul ) . '</h1>' . $html; // phpcs:ignore
	if ( $cta ) { echo '<p><a class="gk-b" href="' . esc_url( $cta[1] ) . '">' . esc_html( $cta[0] ) . '</a></p>'; }
	echo '</div></div></body></html>';
	exit;
}

/* ─────────────────────────── Panel: Ustawienia → Newsletter Guliwera ─────────────────────────── */
add_action( 'admin_menu', function () {
	add_options_page( 'Newsletter Guliwera', 'Newsletter Guliwera', 'manage_options', 'gk-newsletter', 'gk_nl_panel' );
} );

add_action( 'admin_post_gk_nl_zapisz', function () {
	if ( ! current_user_can( 'manage_options' ) ) { wp_die( 'Brak uprawnień' ); }
	check_admin_referer( 'gk_nl_zapisz' );
	$o   = gk_nl_opcje();
	$p   = wp_unslash( $_POST );
	$api = trim( (string) ( $p['api'] ?? '' ) );
	if ( $api && preg_match( '/^[a-f0-9]{32}-[a-z]+\d+$/', $api ) ) { $o['api'] = $api; $o['lista'] = ''; }
	if ( ! empty( $p['usun_api'] ) ) { $o['api'] = ''; $o['lista'] = ''; $o['lista_nazwa'] = ''; }
	foreach ( array( 'plik_kalk', 'plik_strat', 'plik_przeg' ) as $f ) { $o[ $f ] = esc_url_raw( (string) ( $p[ $f ] ?? '' ) ); }
	$o['nadawca'] = sanitize_text_field( (string) ( $p['nadawca'] ?? $o['nadawca'] ) );
	$o['aktywny'] = empty( $p['aktywny'] ) ? 0 : 1;
	if ( ! empty( $p['lista'] ) ) { $o['lista'] = sanitize_text_field( $p['lista'] ); }
	update_option( 'gk_nl_opcje', $o );
	$msg = 'zapisano';
	if ( $o['api'] ) {
		$l = gk_nl_mc( 'GET', 'lists?count=10&fields=lists.id,lists.name,lists.stats.member_count', null, false );
		if ( null === $l ) { $msg = 'blad_api'; }
		else {
			$listy = isset( $l['lists'] ) ? $l['lists'] : array();
			update_option( 'gk_nl_listy', $listy, false );
			if ( ! $o['lista'] && 1 === count( $listy ) ) { $o['lista'] = $listy[0]['id']; }
			foreach ( $listy as $x ) { if ( $x['id'] === $o['lista'] ) { $o['lista_nazwa'] = $x['name']; } }
			update_option( 'gk_nl_opcje', $o );
			if ( $o['lista'] ) { gk_nl_mc_pola(); }
		}
	}
	wp_safe_redirect( admin_url( 'options-general.php?page=gk-newsletter&m=' . $msg ) );
	exit;
} );

add_action( 'admin_post_gk_nl_test', function () {
	if ( ! current_user_can( 'manage_options' ) ) { wp_die( 'Brak uprawnień' ); }
	check_admin_referer( 'gk_nl_test' );
	$u   = wp_get_current_user();
	$kod = sanitize_text_field( wp_unslash( $_POST['kod'] ?? 'W1' ) );
	$k   = gk_nl_kontakt_testowy( $u );
	$ok  = isset( gk_nl_opisy()[ $kod ] ) && gk_nl_wyslij( $k, $kod, true, $u->user_email );
	wp_safe_redirect( admin_url( 'options-general.php?page=gk-newsletter&m=' . ( $ok ? 'test_ok' : 'test_blad' ) ) );
	exit;
} );

function gk_nl_kontakt_testowy( $u ) {
	return array( 'id' => 0, 'email' => $u->user_email, 'imie' => $u->first_name, 'status' => 'subscribed', 'zrodlo' => 'akademia_rejestracja', 'token' => str_repeat( '0', 32 ),
		'user_id' => $u->ID, 'potwierdzono' => time(), 'pref' => 'wszystko', 'wyslane' => array(), 'kolejka' => array(), 'tagi' => array(), 'ip_zapis' => '', 'ip_potw' => '', 'utworzono' => time() );
}

function gk_nl_panel() {
	global $wpdb;
	$o     = gk_nl_opcje();
	$t     = gk_nl_tabela();
	$licz  = $wpdb->get_results( "SELECT status, COUNT(*) n FROM $t GROUP BY status", OBJECT_K ); // phpcs:ignore
	$n     = function ( $s ) use ( $licz ) { return isset( $licz[ $s ] ) ? (int) $licz[ $s ]->n : 0; };
	$listy = (array) get_option( 'gk_nl_listy', array() );
	$m     = isset( $_GET['m'] ) ? sanitize_key( $_GET['m'] ) : ''; // phpcs:ignore
	$kom   = array( 'zapisano' => 'Zapisano.', 'blad_api' => 'Nie udało się połączyć z Mailchimpem. Sprawdź klucz (log poniżej).', 'test_ok' => 'Wysłano test na Twój adres.', 'test_blad' => 'Test się nie wysłał (log poniżej).' );
	echo '<div class="wrap"><h1>Newsletter Guliwera</h1>';
	if ( isset( $kom[ $m ] ) ) { echo '<div class="notice notice-' . ( false !== strpos( $m, 'blad' ) ? 'error' : 'success' ) . '"><p>' . esc_html( $kom[ $m ] ) . '</p></div>'; }
	if ( ! function_exists( 'gk_ml_layout' ) ) { echo '<div class="notice notice-error"><p>Włącz wtyczkę „Guliwera – maile”: newsletter używa jej szablonu.</p></div>'; }
	echo '<p><b>Kontakty:</b> potwierdzone ' . $n( 'subscribed' ) . ' · czekają na potwierdzenie ' . $n( 'pending' ) . ' · wypisane ' . $n( 'unsubscribed' ) . ' &nbsp;|&nbsp; <a href="' . esc_url( home_url( '/?gk_nl_podglad=lista' ) ) . '" target="_blank">Podgląd wszystkich maili</a></p>';
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	wp_nonce_field( 'gk_nl_zapisz' );
	echo '<input type="hidden" name="action" value="gk_nl_zapisz"><table class="form-table" role="presentation">';
	echo '<tr><th>Wysyłka</th><td><label><input type="checkbox" name="aktywny" value="1"' . checked( $o['aktywny'], 1, false ) . '> <b>Newsletter włączony</b>: formularz i pole przy rejestracji są widoczne, wychodzą potwierdzenia, seria powitalna i maile wyzwalane.</label><p class="description">Wyłączony: nic się nie wysyła (poza testami do Ciebie), a formularz pokazuje „Zapisy ruszą wkrótce”.</p></td></tr>';
	$maska = $o['api'] ? '••••••••••••' . substr( $o['api'], -8 ) : '';
	echo '<tr><th><label for="gk-api">Klucz API Mailchimp</label></th><td><input id="gk-api" name="api" type="password" class="regular-text" autocomplete="off" placeholder="' . esc_attr( $maska ? $maska : 'wklej klucz z Mailchimpa' ) . '"> ' . ( $o['api'] ? '<label><input type="checkbox" name="usun_api" value="1"> usuń klucz</label>' : '' ) . '<p class="description">Mailchimp → profil → Account &amp; billing → Extras → API keys → Create A Key. Zostaw puste, żeby nie zmieniać.</p></td></tr>';
	if ( $o['api'] ) {
		echo '<tr><th>Lista (Audience)</th><td>';
		if ( $listy ) {
			echo '<select name="lista">';
			foreach ( $listy as $x ) { echo '<option value="' . esc_attr( $x['id'] ) . '"' . selected( $o['lista'], $x['id'], false ) . '>' . esc_html( $x['name'] . ' (' . ( isset( $x['stats']['member_count'] ) ? (int) $x['stats']['member_count'] : 0 ) . ' kontaktów)' ) . '</option>'; }
			echo '</select>' . ( $o['lista'] ? ' ✅ Połączono z listą „' . esc_html( $o['lista_nazwa'] ) . '”' : '' );
		} else { echo 'Zapisz, żeby pobrać listy.'; }
		echo '<p class="description">Webhook wypisów (Mailchimp → Audience → Settings → Webhooks, zaznacz „Unsubscribes” i „Cleaned address”):<br><code>' . esc_html( rest_url( 'guliwera/v1/mailchimp/' . $o['sekret'] ) ) . '</code></p></td></tr>';
	}
	echo '<tr><th><label for="gk-nad">Nadawca</label></th><td><input id="gk-nad" name="nadawca" class="regular-text" value="' . esc_attr( $o['nadawca'] ) . '"> &lt;' . esc_html( GK_NL_NADAWCA ) . '&gt;</td></tr>';
	foreach ( array( 'plik_kalk' => 'Plik: kalkulator zysku (XLSX)', 'plik_strat' => 'Plik: strategia na 1 stronie, przykład (PDF)', 'plik_przeg' => 'Plik: przegląd miesiąca (PDF)' ) as $f => $et ) {
		echo '<tr><th><label for="gk-' . esc_attr( $f ) . '">' . esc_html( $et ) . '</label></th><td><input id="gk-' . esc_attr( $f ) . '" name="' . esc_attr( $f ) . '" type="url" class="large-text" value="' . esc_attr( $o[ $f ] ) . '"><p class="description">Adres z biblioteki mediów.</p></td></tr>';
	}
	echo '</table>';
	submit_button( 'Zapisz' );
	echo '</form><h2>Test</h2><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	wp_nonce_field( 'gk_nl_test' );
	echo '<input type="hidden" name="action" value="gk_nl_test"><select name="kod">';
	foreach ( gk_nl_opisy() as $k => $op ) { echo '<option value="' . esc_attr( $k ) . '">' . esc_html( $k . ' · ' . $op ) . '</option>'; }
	echo '</select> ';
	submit_button( 'Wyślij test na mój adres', 'secondary', 'submit', false );
	echo '</form>';
	$ost = $wpdb->get_results( "SELECT email, status, zrodlo, utworzono, potwierdzono FROM $t ORDER BY id DESC LIMIT 20", ARRAY_A ); // phpcs:ignore
	echo '<h2>Ostatnie zapisy</h2><table class="widefat striped"><thead><tr><th>E-mail</th><th>Status</th><th>Źródło</th><th>Zapis</th><th>Potwierdzenie</th></tr></thead><tbody>';
	foreach ( $ost as $r ) { echo '<tr><td>' . esc_html( $r['email'] ) . '</td><td>' . esc_html( $r['status'] ) . '</td><td>' . esc_html( $r['zrodlo'] ) . '</td><td>' . esc_html( wp_date( 'j.m.Y H:i', (int) $r['utworzono'] ) ) . '</td><td>' . ( $r['potwierdzono'] ? esc_html( wp_date( 'j.m.Y H:i', (int) $r['potwierdzono'] ) ) : '–' ) . '</td></tr>'; }
	echo '</tbody></table><h2>Log</h2><pre style="background:#fff;padding:12px;max-height:240px;overflow:auto">' . esc_html( implode( "\n", array_reverse( (array) get_option( 'gk_nl_log', array() ) ) ) ) . '</pre></div>';
}
