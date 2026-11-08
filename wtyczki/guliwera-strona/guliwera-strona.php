<?php
/**
 * Plugin Name: Guliwera – struktura strony
 * Description: Porządkuje strukturę guliwera4.pl: Lokalizacja jest częścią strony Kontakt (przekierowanie 301 z /lokalizacja/, usunięcie pozycji z menu, linki prowadzą do mapy w Kontakcie). Zostaw włączoną.
 * Version: 1.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* /lokalizacja/ -> /kontakt/#lokalizacja (301) */
add_action( 'template_redirect', function () {
	$path = trim( (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH ), '/' );
	if ( 'lokalizacja' === $path || is_page( 52 ) ) {
		wp_safe_redirect( home_url( '/kontakt/#lokalizacja' ), 301 );
		exit;
	}
}, 1 );

/* Menu i linki na wszystkich podstronach (także sklep i strony prawne). */
add_action( 'template_redirect', function () {
	if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || is_feed() ) { return; }
	ob_start( 'gk_strona_filtr' );
}, 2 );

function gk_strona_filtr( $html ) {
	if ( false === strpos( $html, '/lokalizacja/' ) ) { return $html; }
	$base = preg_quote( untrailingslashit( home_url() ), '#' );
	// Pozycja „Lokalizacja” w menu.
	$html = preg_replace( '#\s*<li><a(?: class="on")? href="' . $base . '/lokalizacja/">Lokalizacja</a></li>#', '', $html );
	// Pozostałe linki (stopka, kafelki, kanały kontaktu) -> mapa w Kontakcie.
	$html = str_replace( untrailingslashit( home_url() ) . '/lokalizacja/', untrailingslashit( home_url() ) . '/kontakt/#lokalizacja', $html );
	return $html;
}

/* Strona Lokalizacja przestaje być osobną podstroną (szkic, poza mapą witryny). */
add_action( 'init', function () {
	if ( '1.0' === get_option( 'gk_strona_ver' ) ) { return; }
	update_option( 'gk_strona_ver', '1.0' );
	if ( 'publish' === get_post_status( 52 ) ) {
		wp_update_post( array( 'ID' => 52, 'post_status' => 'draft' ) );
	}
	if ( function_exists( 'w3tc_flush_all' ) ) { w3tc_flush_all(); }
}, 20 );
