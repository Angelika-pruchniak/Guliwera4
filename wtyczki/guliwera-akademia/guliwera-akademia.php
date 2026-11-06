<?php
/**
 * Plugin Name: Guliwera – Akademia (aplikacja)
 * Description: Udostępnia aplikację Akademia Guliwera pod adresem guliwera4.pl/akademia/ (tylko dla zalogowanych), liczy postęp na serwerze, daje rabaty na PDF-y za regularną naukę i dodaje wyróżniony przycisk „Akademia” w menu strony. Zostaw włączoną.
 * Version: 1.4
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'GK_AK_PREFIX', '/akademia/' );

/* ─────────────────────────── Zasady rabatu ───────────────────────────
 * Rabat % = floor( RABAT_MAX × (L ÷ N) × K )
 *   L = ukończone lekcje (unikalne, liczone przez serwer, max 3 nowe dziennie)
 *   N = liczba wszystkich lekcji w kursie
 *   K = mnożnik regularności z liczby aktywnych dni w ostatnich 28 dniach (A):
 *       A < 4 → 0;  4–7 → 0,5;  8–13 → 0,75;  14+ → 1
 * Kupon wydajemy od 5%. Jeden kupon na 30 dni, ważny 7 dni, tylko na PDF-y,
 * tylko dla adresu e-mail konta, jednorazowy, nie łączy się z innymi kuponami.
 */
define( 'GK_AK_RABAT_MAX', 30 );
define( 'GK_AK_RABAT_MIN', 5 );
define( 'GK_AK_LEKCJI_DZIENNIE', 3 );
define( 'GK_AK_OKNO_DNI', 28 );
define( 'GK_AK_KUPON_CO_DNI', 30 );
define( 'GK_AK_KUPON_WAZNY_DNI', 7 );
// Produkty „Kierunek Zysk” (PDF-y), na które działa rabat.
define( 'GK_AK_PRODUKTY', '67,68,69,70,71,72,73' );

/* Akademia Pro: działy 1–2 za darmo, reszta (oraz serca bez limitu i certyfikat) w Pro. */
define( 'GK_AK_DARMOWE_DZIALY', 2 );
define( 'GK_AK_PRO_CENA', '249' );
define( 'GK_AK_PRO_DNI', 365 );

function gk_ak_mnoznik( $aktywne_dni ) {
	if ( $aktywne_dni >= 14 ) { return 1.0; }
	if ( $aktywne_dni >= 8 ) { return 0.75; }
	if ( $aktywne_dni >= 4 ) { return 0.5; }
	return 0.0;
}

function gk_ak_rabat( $lekcje, $wszystkie, $aktywne_dni ) {
	if ( $wszystkie <= 0 ) { return 0; }
	$r = (int) floor( GK_AK_RABAT_MAX * min( 1, $lekcje / $wszystkie ) * gk_ak_mnoznik( $aktywne_dni ) + 1e-9 );
	return $r >= GK_AK_RABAT_MIN ? $r : 0;
}

/** Lista prawidłowych lekcji (generowana przy budowaniu aplikacji). */
function gk_ak_lekcje_kursu() {
	static $ids = null;
	if ( null === $ids ) {
		$f   = __DIR__ . '/app/lekcje.json';
		$ids = is_file( $f ) ? json_decode( (string) file_get_contents( $f ), true ) : array();
		if ( ! is_array( $ids ) ) { $ids = array(); }
	}
	return $ids;
}

function gk_ak_dzis() { return wp_date( 'Y-m-d' ); }

function gk_ak_postep( $uid ) {
	$p = get_user_meta( $uid, 'gk_ak_postep', true );
	if ( ! is_array( $p ) ) { $p = array(); }
	return wp_parse_args( $p, array( 'lekcje' => array(), 'dni' => array(), 'kupon' => null ) );
}

function gk_ak_pro_do( $uid ) { return (int) get_user_meta( $uid, 'gk_ak_pro_do', true ); }
function gk_ak_jest_pro( $uid ) { return gk_ak_pro_do( $uid ) > time(); }
function gk_ak_pro_produkt() { return (int) get_option( 'gk_ak_pro_produkt', 0 ); }
function gk_ak_daj_pro( $uid, $do ) {
	update_user_meta( $uid, 'gk_ak_pro_do', max( gk_ak_pro_do( $uid ), (int) $do ) );
}

function gk_ak_status( $uid ) {
	$p      = gk_ak_postep( $uid );
	$od     = wp_date( 'Y-m-d', time() - ( GK_AK_OKNO_DNI - 1 ) * DAY_IN_SECONDS );
	$aktyw  = count( array_filter( $p['dni'], function ( $d ) use ( $od ) { return $d >= $od; } ) );
	$n      = count( gk_ak_lekcje_kursu() );
	$l      = count( $p['lekcje'] );
	$rabat  = gk_ak_rabat( $l, $n, $aktyw );
	$kupon  = $p['kupon'];
	$mozna  = ! $kupon || ( time() - (int) $kupon['data'] ) >= GK_AK_KUPON_CO_DNI * DAY_IN_SECONDS;
	return array(
		'lekcje'       => $l,
		'wszystkie'    => $n,
		'aktywneDni'   => $aktyw,
		'okno'         => GK_AK_OKNO_DNI,
		'mnoznik'      => gk_ak_mnoznik( $aktyw ),
		'rabat'        => $rabat,
		'rabatMax'     => GK_AK_RABAT_MAX,
		'dzisLekcji'   => count( array_filter( $p['lekcje'], function ( $d ) { return $d === gk_ak_dzis(); } ) ),
		'limitDzienny' => GK_AK_LEKCJI_DZIENNIE,
		'kupon'        => $kupon ? array(
			'kod'      => $kupon['kod'],
			'procent'  => (int) $kupon['procent'],
			'wazneDo'  => wp_date( 'Y-m-d', (int) $kupon['data'] + GK_AK_KUPON_WAZNY_DNI * DAY_IN_SECONDS ),
			'aktualny' => ( time() - (int) $kupon['data'] ) < GK_AK_KUPON_WAZNY_DNI * DAY_IN_SECONDS,
		) : null,
		'nowyKuponOd'  => $kupon ? wp_date( 'Y-m-d', (int) $kupon['data'] + GK_AK_KUPON_CO_DNI * DAY_IN_SECONDS ) : null,
		'moznaOdebrac' => $mozna && $rabat > 0 && function_exists( 'WC' ),
		'zgoda'        => (bool) get_user_meta( $uid, 'gk_ak_zgoda', true ),
		'pro'          => gk_ak_jest_pro( $uid ),
		'proDo'        => gk_ak_pro_do( $uid ) ? wp_date( 'Y-m-d', gk_ak_pro_do( $uid ) ) : null,
		'proUrl'       => gk_ak_pro_produkt() && function_exists( 'wc_get_checkout_url' ) ? add_query_arg( 'add-to-cart', gk_ak_pro_produkt(), wc_get_checkout_url() ) : null,
		'proCena'      => (int) GK_AK_PRO_CENA,
		'darmoweDzialy' => GK_AK_DARMOWE_DZIALY,
	);
}

/* ─────────────────────────── REST API ─────────────────────────── */
add_action( 'rest_api_init', function () {
	$zalogowany = function () { return is_user_logged_in(); };
	register_rest_route( 'akademia/v1', '/status', array(
		'methods'             => 'GET',
		'permission_callback' => $zalogowany,
		'callback'            => function () { return gk_ak_status( get_current_user_id() ); },
	) );
	register_rest_route( 'akademia/v1', '/lekcja', array(
		'methods'             => 'POST',
		'permission_callback' => $zalogowany,
		'callback'            => 'gk_ak_rest_lekcja',
	) );
	register_rest_route( 'akademia/v1', '/aktywnosc', array(
		'methods'             => 'POST',
		'permission_callback' => $zalogowany,
		'callback'            => function () {
			$uid = get_current_user_id();
			$p   = gk_ak_postep( $uid );
			gk_ak_zapisz_dzien( $uid, $p );
			return gk_ak_status( $uid );
		},
	) );
	register_rest_route( 'akademia/v1', '/kupon', array(
		'methods'             => 'POST',
		'permission_callback' => $zalogowany,
		'callback'            => 'gk_ak_rest_kupon',
	) );
	register_rest_route( 'akademia/v1', '/kod', array(
		'methods'             => 'POST',
		'permission_callback' => $zalogowany,
		'callback'            => 'gk_ak_rest_kod',
	) );
	register_rest_route( 'akademia/v1', '/zgoda', array(
		'methods'             => 'POST',
		'permission_callback' => $zalogowany,
		'callback'            => function ( WP_REST_Request $r ) {
			$uid = get_current_user_id();
			if ( $r->get_param( 'zgoda' ) ) {
				update_user_meta( $uid, 'gk_ak_zgoda', time() );
			} else {
				delete_user_meta( $uid, 'gk_ak_zgoda' );
			}
			return gk_ak_status( $uid );
		},
	) );
} );

function gk_ak_zapisz_dzien( $uid, &$p ) {
	$d = gk_ak_dzis();
	if ( ! in_array( $d, $p['dni'], true ) ) { $p['dni'][] = $d; }
	// Trzymamy tylko ostatnie 120 dni aktywności.
	$od       = wp_date( 'Y-m-d', time() - 120 * DAY_IN_SECONDS );
	$p['dni'] = array_values( array_filter( $p['dni'], function ( $x ) use ( $od ) { return $x >= $od; } ) );
	update_user_meta( $uid, 'gk_ak_postep', $p );
}

function gk_ak_rest_lekcja( WP_REST_Request $r ) {
	$uid = get_current_user_id();
	$id  = (string) $r->get_param( 'id' );
	if ( ! in_array( $id, gk_ak_lekcje_kursu(), true ) ) {
		return new WP_Error( 'gk_ak_lekcja', 'Nieznana lekcja.', array( 'status' => 400 ) );
	}
	if ( preg_match( '/^u(\d+)l/', $id, $mm ) && (int) $mm[1] > GK_AK_DARMOWE_DZIALY && ! gk_ak_jest_pro( $uid ) ) {
		return array_merge( gk_ak_status( $uid ), array( 'zaliczona' => false, 'powod' => 'pro' ) );
	}
	$p      = gk_ak_postep( $uid );
	$dzis   = gk_ak_dzis();
	$dzisL  = count( array_filter( $p['lekcje'], function ( $d ) use ( $dzis ) { return $d === $dzis; } ) );
	$zalicz = false;
	if ( ! isset( $p['lekcje'][ $id ] ) && $dzisL < GK_AK_LEKCJI_DZIENNIE ) {
		$p['lekcje'][ $id ] = $dzis;
		$zalicz             = true;
	}
	gk_ak_zapisz_dzien( $uid, $p );
	return array_merge( gk_ak_status( $uid ), array( 'zaliczona' => $zalicz ) );
}

/* ─────────────────────────── Kody firmowe (licencje Akademii dla firm) ─────────────────────────── */
function gk_ak_kody() { $k = get_option( 'gk_ak_kody', array() ); return is_array( $k ) ? $k : array(); }

function gk_ak_rest_kod( WP_REST_Request $r ) {
	$uid  = get_current_user_id();
	$kod  = strtoupper( preg_replace( '/[^A-Za-z0-9-]/', '', (string) $r->get_param( 'kod' ) ) );
	$kody = gk_ak_kody();
	if ( ! $kod || empty( $kody[ $kod ] ) ) {
		return new WP_Error( 'gk_ak_kod', 'Nieprawidłowy kod.', array( 'status' => 400 ) );
	}
	$k = $kody[ $kod ];
	if ( (int) $k['do'] < time() ) {
		return new WP_Error( 'gk_ak_kod', 'Ten kod wygasł.', array( 'status' => 400 ) );
	}
	$uzyte = isset( $k['uzyte'] ) ? array_map( 'intval', (array) $k['uzyte'] ) : array();
	if ( ! in_array( $uid, $uzyte, true ) ) {
		if ( count( $uzyte ) >= (int) $k['miejsca'] ) {
			return new WP_Error( 'gk_ak_kod', 'Wszystkie miejsca w tym kodzie są zajęte.', array( 'status' => 400 ) );
		}
		$uzyte[]               = $uid;
		$kody[ $kod ]['uzyte'] = $uzyte;
		update_option( 'gk_ak_kody', $kody, false );
	}
	gk_ak_daj_pro( $uid, (int) $k['do'] );
	update_user_meta( $uid, 'gk_ak_firma', sanitize_text_field( $k['firma'] ) );
	return gk_ak_status( $uid );
}

/** Panel: Narzędzia → Akademia: kody firm. */
add_action( 'admin_menu', function () {
	add_management_page( 'Akademia: kody firm', 'Akademia: kody firm', 'manage_options', 'gk-ak-kody', 'gk_ak_panel_kodow' );
} );
function gk_ak_panel_kodow() {
	if ( ! current_user_can( 'manage_options' ) ) { return; }
	$kody = gk_ak_kody();
	if ( isset( $_POST['gk_ak_nowy'] ) && check_admin_referer( 'gk_ak_kody' ) ) {
		$firma   = sanitize_text_field( wp_unslash( $_POST['firma'] ?? '' ) );
		$miejsca = max( 1, (int) ( $_POST['miejsca'] ?? 10 ) );
		$mies    = max( 1, (int) ( $_POST['miesiace'] ?? 12 ) );
		$kod     = 'FIRMA-' . strtoupper( wp_generate_password( 6, false, false ) );
		$kody[ $kod ] = array( 'firma' => $firma, 'miejsca' => $miejsca, 'do' => strtotime( "+$mies months" ), 'uzyte' => array() );
		update_option( 'gk_ak_kody', $kody, false );
		echo '<div class="notice notice-success"><p>Nowy kod: <b>' . esc_html( $kod ) . '</b></p></div>';
	}
	if ( isset( $_POST['gk_ak_usun'] ) && check_admin_referer( 'gk_ak_kody' ) ) {
		unset( $kody[ sanitize_text_field( wp_unslash( $_POST['gk_ak_usun'] ) ) ] );
		update_option( 'gk_ak_kody', $kody, false );
	}
	echo '<div class="wrap"><h1>Akademia Guliwera: kody dla firm</h1>';
	echo '<p>Kod daje pracownikom firmy dostęp do Akademii Pro do daty ważności. Pracownik zakłada konto na guliwera4.pl i wpisuje kod w aplikacji: Profil → Akademia Pro.</p>';
	echo '<form method="post">'; wp_nonce_field( 'gk_ak_kody' );
	echo '<table class="form-table"><tr><th>Firma</th><td><input name="firma" required class="regular-text"></td></tr>';
	echo '<tr><th>Liczba miejsc</th><td><input name="miejsca" type="number" value="10" min="1"></td></tr>';
	echo '<tr><th>Ważność (miesiące)</th><td><input name="miesiace" type="number" value="12" min="1"></td></tr></table>';
	echo '<p><button class="button button-primary" name="gk_ak_nowy" value="1">Utwórz kod</button></p></form>';
	echo '<table class="widefat striped"><thead><tr><th>Kod</th><th>Firma</th><th>Zajęte / miejsca</th><th>Ważny do</th><th></th></tr></thead><tbody>';
	foreach ( $kody as $kod => $k ) {
		echo '<tr><td><code>' . esc_html( $kod ) . '</code></td><td>' . esc_html( $k['firma'] ) . '</td><td>' . count( (array) $k['uzyte'] ) . ' / ' . (int) $k['miejsca'] . '</td><td>' . esc_html( wp_date( 'Y-m-d', (int) $k['do'] ) ) . '</td>';
		echo '<td><form method="post" onsubmit="return confirm(\'Usunąć kod?\')">'; wp_nonce_field( 'gk_ak_kody' );
		echo '<button class="button-link-delete" name="gk_ak_usun" value="' . esc_attr( $kod ) . '">Usuń</button></form></td></tr>';
	}
	if ( ! $kody ) { echo '<tr><td colspan="5">Brak kodów.</td></tr>'; }
	echo '</tbody></table></div>';
}

/* ─────────────────────────── Akademia Pro w sklepie ─────────────────────────── */
// Produkt tworzy się sam (raz), gdy działa WooCommerce.
add_action( 'init', function () {
	if ( ! class_exists( 'WC_Product_Simple' ) ) { return; }
	$id = gk_ak_pro_produkt();
	if ( $id && get_post( $id ) ) { return; }
	if ( get_transient( 'gk_ak_pro_lock' ) ) { return; }
	set_transient( 'gk_ak_pro_lock', 1, MINUTE_IN_SECONDS );
	$p = new WC_Product_Simple();
	$p->set_name( 'Akademia Guliwera Pro – dostęp na 12 miesięcy' );
	$p->set_slug( 'akademia-pro' );
	$p->set_regular_price( GK_AK_PRO_CENA );
	$p->set_virtual( true );
	$p->set_sold_individually( true );
	$p->set_catalog_visibility( 'hidden' );
	$p->set_short_description( 'Pełny dostęp do wszystkich działów Akademii Guliwera na 12 miesięcy: lekcje ze strategii, finansów i prowadzenia zespołu, serca bez limitu i certyfikat ukończenia. Płatność jednorazowa, bez abonamentu.' );
	$p->set_description( 'Akademia Guliwera Pro to roczny dostęp do aplikacji z codziennymi lekcjami dla młodych managerów (guliwera4.pl/akademia/). Działy 1–2 są darmowe; Pro odblokowuje wszystkie pozostałe działy i nowe lekcje dodawane w trakcie roku, usuwa limit serc i daje certyfikat ukończenia kursu. Dostęp aktywuje się automatycznie po opłaceniu zamówienia na koncie, którym logujesz się do Akademii.' );
	$p->set_status( 'publish' );
	update_option( 'gk_ak_pro_produkt', $p->save(), false );
}, 30 );

function gk_ak_zamowienie_pro( $order_id ) {
	$o = wc_get_order( $order_id );
	if ( ! $o || $o->get_meta( '_gk_ak_pro' ) || ! $o->get_user_id() ) { return; }
	foreach ( $o->get_items() as $it ) {
		if ( (int) $it->get_product_id() === gk_ak_pro_produkt() ) {
			$uid = $o->get_user_id();
			gk_ak_daj_pro( $uid, max( time(), gk_ak_pro_do( $uid ) ) + GK_AK_PRO_DNI * DAY_IN_SECONDS );
			$o->update_meta_data( '_gk_ak_pro', time() );
			$o->add_order_note( 'Akademia Pro aktywna do ' . wp_date( 'Y-m-d', gk_ak_pro_do( $uid ) ) . '.' );
			$o->save();
			return;
		}
	}
}
add_action( 'woocommerce_order_status_processing', 'gk_ak_zamowienie_pro' );
add_action( 'woocommerce_order_status_completed', 'gk_ak_zamowienie_pro' );

// Zamówienie Pro wymaga konta (dostęp przypisujemy do konta).
add_filter( 'woocommerce_checkout_registration_required', function ( $wymagane ) {
	if ( function_exists( 'WC' ) && WC()->cart && gk_ak_pro_produkt() ) {
		foreach ( WC()->cart->get_cart() as $poz ) {
			if ( (int) $poz['product_id'] === gk_ak_pro_produkt() ) { return true; }
		}
	}
	return $wymagane;
} );

function gk_ak_rest_kupon() {
	$uid = get_current_user_id();
	$s   = gk_ak_status( $uid );
	if ( ! $s['moznaOdebrac'] || ! class_exists( 'WC_Coupon' ) ) {
		return new WP_Error( 'gk_ak_kupon', 'Kupon nie jest jeszcze dostępny.', array( 'status' => 400 ) );
	}
	$user = wp_get_current_user();
	$kod  = 'AKADEMIA-' . $s['rabat'] . '-' . strtoupper( wp_generate_password( 6, false, false ) );
	$c    = new WC_Coupon();
	$c->set_code( $kod );
	$c->set_discount_type( 'percent' );
	$c->set_amount( $s['rabat'] );
	$c->set_individual_use( true );
	$c->set_usage_limit( 1 );
	$c->set_usage_limit_per_user( 1 );
	$c->set_email_restrictions( array( $user->user_email ) );
	$c->set_product_ids( array_map( 'intval', explode( ',', GK_AK_PRODUKTY ) ) );
	$c->set_date_expires( time() + GK_AK_KUPON_WAZNY_DNI * DAY_IN_SECONDS );
	$c->set_description( 'Akademia Guliwera: rabat za regularną naukę (użytkownik ' . $uid . ')' );
	$c->save();

	$p          = gk_ak_postep( $uid );
	$p['kupon'] = array( 'kod' => $kod, 'procent' => $s['rabat'], 'data' => time() );
	update_user_meta( $uid, 'gk_ak_postep', $p );
	return gk_ak_status( $uid );
}

/** Link /koszyk/?gk_kupon=KOD od razu dodaje kupon do koszyka. */
add_action( 'wp_loaded', function () {
	if ( empty( $_GET['gk_kupon'] ) || ! function_exists( 'WC' ) || ! WC()->cart ) { return; }
	$kod = wc_format_coupon_code( wp_unslash( $_GET['gk_kupon'] ) );
	if ( $kod && ! WC()->cart->has_discount( $kod ) ) { WC()->cart->apply_coupon( $kod ); }
} );

/* ─────────────────────────── Logowanie ───────────────────────────
 * Akademia jest tylko dla zalogowanych. Niezalogowanych odsyłamy do „Moje konto”
 * (logowanie, rejestracja, „Zaloguj przez Google”), a po zalogowaniu wracają do aplikacji.
 */
function gk_ak_link_logowania() {
	$konto = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
	return add_query_arg( array( 'akademia' => 1, 'redirect_to' => rawurlencode( home_url( GK_AK_PREFIX ) ) ), $konto );
}

function gk_ak_po_logowaniu( $redirect ) {
	if ( ! empty( $_COOKIE['gk_ak_wroc'] ) ) {
		setcookie( 'gk_ak_wroc', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
		return home_url( GK_AK_PREFIX );
	}
	return $redirect;
}
add_filter( 'woocommerce_login_redirect', 'gk_ak_po_logowaniu', 20 );
add_filter( 'woocommerce_registration_redirect', 'gk_ak_po_logowaniu', 20 );
add_filter( 'login_redirect', 'gk_ak_po_logowaniu', 20 );

/**
 * Rejestracja: zakładanie konta na stronie „Moje konto” jest zawsze włączone
 * (niezależnie od ustawień WooCommerce). Login tworzy się z adresu e-mail,
 * a hasło wpisuje sam użytkownik, więc od razu po rejestracji jest zalogowany.
 */
add_filter( 'pre_option_woocommerce_enable_myaccount_registration', function () { return 'yes'; } );
add_filter( 'pre_option_woocommerce_registration_generate_username', function () { return 'yes'; } );
add_filter( 'pre_option_woocommerce_registration_generate_password', function () { return 'no'; } );

/** Na stronie „Moje konto” po przyjściu z Akademii: krótka informacja nad formularzem. */
add_action( 'woocommerce_before_customer_login_form', function () {
	if ( empty( $_GET['akademia'] ) ) { return; }
	echo '<div class="woocommerce-info">Masz już konto? Zaloguj się. Pierwszy raz? Załóż darmowe konto w formularzu „Zarejestruj się” (wystarczy e-mail i hasło), żeby korzystać z Akademii Guliwera i zbierać rabaty na materiały.</div>';
} );

/* ─────────────────────────── Serwowanie aplikacji ─────────────────────────── */
function gk_ak_sciezka() {
	$path = wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH );
	return is_string( $path ) ? $path : '';
}

function gk_ak_plik( $rel ) {
	$base = realpath( __DIR__ . '/app' );
	$file = ( false !== strpos( $rel, '..' ) || ! preg_match( '#^[A-Za-z0-9._/-]+$#', $rel ) )
		? false
		: realpath( $base . '/' . $rel );
	if ( ! $file || 0 !== strpos( $file, $base . DIRECTORY_SEPARATOR ) || ! is_file( $file ) ) { return false; }
	return $file;
}

function gk_ak_naglowki( $ext, $rel ) {
	foreach ( array( 'DONOTCACHEPAGE', 'DONOTMINIFY', 'DONOTCDN', 'DONOTCACHEOBJECT' ) as $c ) {
		if ( ! defined( $c ) ) { define( $c, true ); }
	}
	while ( ob_get_level() > 0 ) { ob_end_clean(); }
	$types = array(
		'html'        => 'text/html; charset=utf-8',
		'js'          => 'text/javascript; charset=utf-8',
		'css'         => 'text/css; charset=utf-8',
		'svg'         => 'image/svg+xml',
		'png'         => 'image/png',
		'json'        => 'application/json',
		'webmanifest' => 'application/manifest+json',
	);
	http_response_code( 200 );
	header( 'Content-Type: ' . ( isset( $types[ $ext ] ) ? $types[ $ext ] : 'application/octet-stream' ) );
	header( 'X-Content-Type-Options: nosniff' );
	if ( 0 === strpos( $rel, 'assets/' ) ) {
		header( 'Cache-Control: public, max-age=31536000, immutable' );
	} else {
		header( 'Cache-Control: no-cache' );
	}
	if ( 'sw.js' === $rel ) {
		header( 'Service-Worker-Allowed: ' . GK_AK_PREFIX );
	}
}

/** Pliki statyczne (skrypty, style, ikony) wysyłamy od razu, zanim WordPress zbuduje stronę. */
function gk_ak_serve() {
	$path = gk_ak_sciezka();
	if ( '/akademia' === $path ) {
		wp_redirect( home_url( GK_AK_PREFIX ), 301 );
		exit;
	}
	if ( 0 !== strpos( $path, GK_AK_PREFIX ) ) { return; }
	$rel  = rawurldecode( substr( $path, strlen( GK_AK_PREFIX ) ) );
	$file = '' === $rel ? false : gk_ak_plik( $rel );
	if ( ! $file || 'index.html' === $rel ) {
		// Ekran aplikacji: po załadowaniu użytkownika (sprawdzamy logowanie).
		add_action( 'init', 'gk_ak_serve_index', 1 );
		return;
	}
	gk_ak_naglowki( strtolower( pathinfo( $file, PATHINFO_EXTENSION ) ), $rel );
	header( 'Content-Length: ' . filesize( $file ) );
	readfile( $file );
	exit;
}
add_action( 'plugins_loaded', 'gk_ak_serve', 0 );

function gk_ak_serve_index() {
	if ( ! is_user_logged_in() ) {
		setcookie( 'gk_ak_wroc', '1', time() + HOUR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
		nocache_headers();
		wp_safe_redirect( gk_ak_link_logowania() );
		exit;
	}
	$file = gk_ak_plik( 'index.html' );
	if ( ! $file ) { return; }
	$user = wp_get_current_user();
	$cfg  = array(
		'rest'     => esc_url_raw( rest_url( 'akademia/v1/' ) ),
		'nonce'    => wp_create_nonce( 'wp_rest' ),
		'imie'     => $user->first_name ? $user->first_name : $user->display_name,
		'sklep'    => home_url( '/materialy/' ),
		'koszyk'   => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/koszyk/' ),
		'wyloguj'  => wp_logout_url( home_url( '/' ) ),
	);
	$html = (string) file_get_contents( $file );
	$html = str_replace( '</head>', '<script>window.GK_AK=' . wp_json_encode( $cfg ) . ';</script></head>', $html );
	gk_ak_naglowki( 'html', 'index.html' );
	header( 'Cache-Control: no-store, private' );
	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput
	exit;
}

/* ─────────────────────────── Przycisk „Akademia” na stronie ───────────────────────────
 * Dodaje wyróżniony, kolorowy przycisk do menu każdej podstrony (nagłówek .gk .menu).
 * Gdy strona nie ma takiego menu, pokazuje pływający przycisk w rogu.
 */
add_action( 'wp_footer', function () {
	if ( is_admin() || 0 === strpos( gk_ak_sciezka(), GK_AK_PREFIX ) ) { return; }
	$url = esc_url( home_url( GK_AK_PREFIX ) );
	?>
<style>
.gk-akad{display:inline-flex!important;align-items:center;gap:7px;padding:7px 15px 7px 13px!important;border-radius:999px;border:0!important;background:linear-gradient(100deg,#FF6B3D 0%,#F4799F 55%,#B9A5E3 100%);color:#fff!important;font-weight:700;text-decoration:none!important;box-shadow:0 6px 16px rgba(255,107,61,.32);animation:gkAkPuls 2.8s ease-in-out infinite;white-space:nowrap;line-height:1.2}
.gk-akad:hover{transform:translateY(-2px);box-shadow:0 10px 22px rgba(244,121,159,.42)}
.gk-akad i{font-style:normal;font-size:10.5px;letter-spacing:.08em;background:#FFC84A;color:#3A2340;padding:2px 7px;border-radius:999px;text-transform:uppercase}
.gk-akad-plywa{position:fixed;left:16px;bottom:16px;z-index:9998;font-family:"DM Sans",system-ui,sans-serif;font-size:15px}
@keyframes gkAkPuls{0%,100%{box-shadow:0 6px 16px rgba(255,107,61,.32)}50%{box-shadow:0 6px 24px rgba(244,121,159,.6)}}
@media (prefers-reduced-motion:reduce){.gk-akad{animation:none}}
</style>
<script>
(function(){
  if (document.querySelector('.gk-akad')) return;
  var url = <?php echo wp_json_encode( $url ); ?>;
  var a = document.createElement('a');
  a.className = 'gk-akad'; a.href = url;
  a.innerHTML = '🎓 Akademia <i>nowość</i>';
  a.setAttribute('aria-label', 'Akademia Guliwera: aplikacja do nauki dla managerów');
  var menu = document.querySelector('.gk .menu');
  if (menu) {
    var li = document.createElement('li'); li.appendChild(a); menu.insertBefore(li, menu.firstChild);
  } else {
    a.classList.add('gk-akad-plywa'); document.body.appendChild(a);
  }
})();
</script>
	<?php
}, 50 );
