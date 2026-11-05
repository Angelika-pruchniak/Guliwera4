<?php
/**
 * Plugin Name: Guliwera – favicon
 * Description: Ustawia ikonę strony (favicon) G4 i usuwa stary plik favicon.ico z katalogu głównego. Zostaje włączona i dopisuje znaczniki ikony w nagłówku strony.
 * Version: 1.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
define( 'GK_ICON_ID', 96 );
register_activation_hook( __FILE__, function () {
	update_option( 'site_icon', GK_ICON_ID );
	$ico = ABSPATH . 'favicon.ico';
	if ( file_exists( $ico ) ) { @rename( $ico, ABSPATH . 'favicon-stary.ico' ); }
	if ( function_exists( 'w3tc_flush_all' ) ) { w3tc_flush_all(); }
	if ( function_exists( 'wp_cache_flush' ) ) { wp_cache_flush(); }
} );
// Pewność, że ikona jest w nagłówku nawet gdy motyw/Elementor jej nie wypisze.
add_action( 'wp_head', function () {
	if ( has_action( 'wp_head', 'wp_site_icon' ) && get_option( 'site_icon' ) ) { return; }
	$u = wp_get_attachment_image_url( GK_ICON_ID, 'full' );
	if ( $u ) { echo '<link rel="icon" href="' . esc_url( $u ) . '" sizes="512x512">' . "\n" . '<link rel="apple-touch-icon" href="' . esc_url( $u ) . '">' . "\n"; }
}, 99 );
