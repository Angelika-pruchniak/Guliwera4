<?php
/**
 * Plugin Name: Guliwera – struktura strony
 * Description: Struktura guliwera4.pl według strategii (drabina produktów): jedno menu na wszystkich podstronach (Start, Materiały, Kurs, Akademia, O mnie, Kontakt), przycisk „Zacznij za darmo”, Lokalizacja i Usługi B2B połączone z Kontaktem (przekierowania 301). Zostaw włączoną.
 * Version: 1.1
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* /lokalizacja/ -> /kontakt/#lokalizacja (301) */
add_action( 'template_redirect', function () {
	$path = trim( (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH ), '/' );
	if ( 'lokalizacja' === $path || is_page( 52 ) ) {
		wp_safe_redirect( home_url( '/kontakt/#lokalizacja' ), 301 );
		exit;
	}
	if ( 'uslugi' === $path || is_page( 32 ) ) {
		wp_safe_redirect( home_url( '/kontakt/#dla-firm' ), 301 );
		exit;
	}
}, 1 );

/* Menu i linki na wszystkich podstronach (także sklep i strony prawne). */
add_action( 'template_redirect', function () {
	if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || is_feed() ) { return; }
	ob_start( 'gk_strona_filtr' );
}, 2 );

function gk_strona_menu() {
	$path = '/' . trim( (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/', PHP_URL_PATH ), '/' ) . '/';
	if ( '//' === $path ) { $path = '/'; }
	$on = '/';
	if ( function_exists( 'is_product' ) && ( is_product() || is_cart() || is_checkout() ) ) { $on = '/materialy/'; }
	foreach ( array( '/materialy/', '/kurs-strategia/', '/aplikacja/', '/o-mnie/', '/kontakt/' ) as $p ) { if ( 0 === strpos( $path, $p ) ) { $on = $p; } }
	if ( '/' !== $path && '/' === $on && ! ( function_exists( 'is_product' ) && is_product() ) ) { $on = ''; }
	$items = array( '/' => 'Start', '/materialy/' => 'Materiały', '/kurs-strategia/' => 'Kurs', '/aplikacja/' => 'Akademia', '/o-mnie/' => 'O mnie', '/kontakt/' => 'Kontakt' );
	$h = '<ul class="menu">';
	foreach ( $items as $p => $t ) {
		$h .= "\n    <li><a" . ( $p === $on ? ' class="on"' : '' ) . ' href="' . esc_url( home_url( $p ) ) . '">' . esc_html( $t ) . '</a></li>';
	}
	return $h . "\n  </ul>";
}

function gk_strona_filtr( $html ) {
	if ( false === strpos( $html, 'class="nav"' ) && false === strpos( $html, '/lokalizacja/' ) && false === strpos( $html, '/uslugi/' ) ) { return $html; }
	$home = untrailingslashit( home_url() );
	// Jedno menu i przycisk w nagłówku.
	$html = preg_replace_callback( '#(<header class="nav">.*?</header>)#s', function ( $m ) use ( $home ) {
		$h = preg_replace( '#<ul class="menu">.*?</ul>#s', gk_strona_menu(), $m[1], 1 );
		$h = preg_replace( '#<a class="btn btn-mala" href="[^"]*">[^<]*</a>#', '<a class="btn btn-mala" href="' . $home . '/aplikacja/">Zacznij za darmo</a>', $h, 1 );
		return $h;
	}, $html, 1 );
	// Pasek nad nagłówkiem: CTA nr 1.
	$html = preg_replace( '#<div class="pasek">.*?</div>#s', '<div class="pasek">Zacznij za darmo: 5 minut dziennie strategii w Akademii Guliwera. <a href="' . $home . '/aplikacja/">Sprawdź →</a></div>', $html, 1 );
	// Lokalizacja i Usługi B2B są teraz w Kontakcie.
	$html = str_replace( array( '>Usługi i cennik</a>', '>Usługi B2B</a>', '>usługi B2B</a>' ), array( '>Dla firm</a>', '>Dla firm</a>', '>Dla firm</a>' ), $html );
	$html = str_replace( array( $home . '/lokalizacja/', $home . '/uslugi/' ), array( $home . '/kontakt/#lokalizacja', $home . '/kontakt/#dla-firm' ), $html );
	return $html;
}

/* Jednorazowe zmiany treści (wersja 1.1). */
add_action( 'init', function () {
	$v = get_option( 'gk_strona_ver' );
	if ( '1.1' === $v ) { return; }
	foreach ( array( 52, 32 ) as $id ) {
		if ( 'publish' === get_post_status( $id ) ) { wp_update_post( array( 'ID' => $id, 'post_status' => 'draft' ) ); }
	}
	// Kurs: kurs wideo 697 zł, przedsprzedaż (strategia, drabina produktów).
	global $wpdb;
	$c = (string) $wpdb->get_var( "SELECT post_content FROM {$wpdb->posts} WHERE ID = 157" );
	if ( $c && false !== strpos( $c, '1 990 zł' ) ) {
		$z = array(
			'PIERWSZA EDYCJA · LUTY 2027' => 'PREMIERA · KWIECIEŃ 2027',
			'Kurs strategii firmy online w małej grupie. Przez 6 tygodni krok po kroku przechodzisz od diagnozy firmy do strategii na jednej stronie i planu na 90 dni. Pracujesz na swojej firmie, nie na przykładach z podręcznika.' => 'Kurs wideo, który krok po kroku przeprowadzi Cię od diagnozy firmy do strategii na jednej stronie i planu na 90 dni. Oglądasz w swoim tempie, pracujesz na swojej firmie, a na dwóch sobotnich sesjach Q&amp;A na żywo dopytujesz o swoje przypadki.',
			'<span>6 spotkań na żywo po 90 min</span><span>mała grupa do 20 osób</span><span>online</span><span>nagrania i materiały</span>' => '<span>6 modułów wideo</span><span>w swoim tempie</span><span>2 sobotnie sesje Q&amp;A na żywo</span><span>pakiet PDF w cenie</span>',
			'>Zapisz się na listę oczekujących</a>' => '>Zapisz się na przedsprzedaż</a>',
			'<li>6 spotkań na żywo online po 90 minut, raz w tygodniu</li><li>nagrania spotkań i materiały do pracy</li><li>zadania na Twojej firmie i informacja zwrotna</li>' => '<li>6 modułów wideo: jeden na tydzień albo w swoim tempie</li><li>2 sobotnie sesje Q&amp;A na żywo online</li><li>zadania na Twojej firmie i szablony do pracy</li>',
			'<div class="cena">1 990 zł</div>' => '<div class="cena">697 zł</div>',
			'Dla osób z listy oczekujących cena na start: <b style="color:#FFC84A">1 590 zł</b>. Termin i link do zapisu wyślemy najpierw osobom z listy.' => 'Premiera w kwietniu 2027. Link do przedsprzedaży dostaną najpierw osoby z listy oczekujących.',
			'Firmy mogą zgłosić kilka osób i opłacić kurs na fakturę. Zespoły kierownicze: zobacz' => 'Firmy: licencja kursu i Akademii dla zespołu młodych managerów, zobacz',
			'>Zapisz się na listę</a>' => '>Zapisz się na przedsprzedaż</a>',
			'Zostaw imię i e-mail. Napiszę, gdy otworzę zapisy na pierwszą edycję, z ceną na start dla osób z listy.' => 'Zostaw imię i e-mail. Napiszę, gdy ruszy przedsprzedaż kursu. Osoby z listy dostaną link jako pierwsze.',
			'Warsztaty strategiczne i strateg na abonament dla firm, kurs strategii, materiały do pobrania i Akademia dla młodych managerów.' => 'Strategia i liczby dla małych firm i młodych managerów: darmowa Akademia, gotowe narzędzia i kurs wideo.',
			'[contact-form subject="Lista oczekujących: kurs Strategia w 6 tygodni"]' => '[contact-form subject="Przedsprzedaż: kurs wideo Strategia w 6 tygodni"]',
			'Chcę dostać informację o zapisach na kurs i cenie na start. Zgoda na kontakt mailowy w tej sprawie.' => 'Chcę dostać informację o przedsprzedaży kursu. Zgoda na kontakt mailowy w tej sprawie.',
		);
		$c = str_replace( array_keys( $z ), array_values( $z ), $c );
		$wpdb->update( $wpdb->posts, array( 'post_content' => $c ), array( 'ID' => 157 ) );
		clean_post_cache( 157 );
	}
	update_option( 'gk_strona_ver', '1.1' );
	if ( function_exists( 'w3tc_flush_all' ) ) { w3tc_flush_all(); }
}, 20 );
