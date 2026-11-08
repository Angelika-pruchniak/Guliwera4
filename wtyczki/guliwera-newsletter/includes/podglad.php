<?php
/**
 * Guliwera 4 – newsletter: podgląd maili na przykładowych danych.
 * guliwera4.pl/?gk_nl_podglad=lista (tylko administrator). Lokalnie: narzedzia/podglad-newslettera.php.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function gk_nl_przyklad() {
	$k = array( 'id' => 0, 'email' => 'ola@przyklad.pl', 'imie' => 'Ola', 'status' => 'subscribed', 'zrodlo' => 'akademia_rejestracja', 'token' => str_repeat( 'a', 32 ),
		'user_id' => 1, 'potwierdzono' => strtotime( '2026-11-17 09:12' ), 'pref' => 'wszystko' );
	$d = array( 'lekcje' => 4, 'seria' => 3, 'darmowe_ok' => false, 'darmowe_zostalo' => 2, 'pro' => false, 'kupon_kod' => 'AKADEMIA-7-K7PQ2X', 'kupon_proc' => 7,
		'kupon_do' => strtotime( '2026-11-30 14:03' ), 'kupil_strategia' => false, 'kupil_planer' => false, 'kupil_pakiet' => false, 'ma_konto' => true,
		'url_pro' => home_url( '/zamowienie/?add-to-cart=120' ), 'url_potwierdz' => home_url( '/?gk_nl_potwierdz=' . str_repeat( 'a', 32 ) ) );
	return array( $k, $d );
}

/** kod => [opis, temat, html, tekst] */
function gk_nl_podglad_lista() {
	list( $k, $d ) = gk_nl_przyklad();
	$l = array();
	foreach ( gk_nl_opisy() as $kod => $opis ) {
		$dd = $d;
		if ( in_array( $kod, array( 'Z1', 'Z1b', 'W7' ), true ) ) { $dd['darmowe_ok'] = true; $dd['lekcje'] = 6; }
		if ( 'Z2_7' === $kod ) { $dd['seria'] = 7; }
		$m = gk_nl_zbuduj( $kod, $k, $dd );
		if ( $m ) { $l[ $kod ] = array( $opis, $m[0], $m[1], $m[2] ); }
	}
	return $l;
}

add_action( 'template_redirect', function () {
	if ( empty( $_GET['gk_nl_podglad'] ) || ! current_user_can( 'manage_options' ) ) { return; }
	$l   = gk_nl_podglad_lista();
	$kod = sanitize_text_field( wp_unslash( $_GET['gk_nl_podglad'] ) );
	nocache_headers();
	if ( ! isset( $l[ $kod ] ) ) {
		header( 'Content-Type: text/html; charset=utf-8' );
		echo '<!doctype html><meta charset="utf-8"><title>Newsletter: podgląd</title><body style="font:16px/1.6 Arial,sans-serif;max-width:820px;margin:30px auto;color:#3A2340"><h1>Newsletter Guliwera: maile</h1><table cellpadding="6">';
		foreach ( $l as $k => $m ) { echo '<tr><td><b>' . esc_html( $k ) . '</b></td><td><a href="' . esc_url( add_query_arg( 'gk_nl_podglad', $k, home_url( '/' ) ) ) . '">' . esc_html( $m[0] ) . '</a></td><td>' . esc_html( $m[1] ) . '</td></tr>'; }
		echo '</table></body>';
		exit;
	}
	header( 'Content-Type: ' . ( empty( $_GET['tekst'] ) ? 'text/html' : 'text/plain' ) . '; charset=utf-8' );
	echo empty( $_GET['tekst'] ) ? $l[ $kod ][2] : 'Temat: ' . $l[ $kod ][1] . "\n\n" . $l[ $kod ][3]; // phpcs:ignore
	exit;
} );
