<?php
/**
 * Lokalny podgląd maili wtyczki guliwera-maile (bez WordPressa).
 * Użycie: php narzedzia/podglad-maili.php KATALOG_WYJSCIOWY
 * Zapisuje KLUCZ.html, KLUCZ.txt i spis.json (temat, długość tematu, preheader).
 */
define( 'ABSPATH', __DIR__ );
define( 'DAY_IN_SECONDS', 86400 );
define( 'YEAR_IN_SECONDS', 31536000 );
date_default_timezone_set( 'Europe/Warsaw' );
define( 'GK_ML_LOGO', 'file://' . dirname( __DIR__ ) . '/logo/ikona-512.png' );
function add_action() {} function add_filter() {} function register_activation_hook() {}
function wp_date( $f, $ts = null ) { return date( $f, $ts ?? time() ); }
function home_url( $p = '' ) { return 'https://guliwera4.pl' . $p; }
function admin_url( $p = '' ) { return 'https://guliwera4.pl/wp-admin/' . $p; }
function wp_strip_all_tags( $s ) { return trim( strip_tags( preg_replace( '/<br\s*\/?>/i', "\n", $s ) ) ); }
function wp_parse_url( $u, $c = -1 ) { return parse_url( $u, $c ); }
function wc_get_page_permalink() { return 'https://guliwera4.pl/moje-konto/'; }
function wc_lostpassword_url() { return 'https://guliwera4.pl/moje-konto/lost-password/'; }
require dirname( __DIR__ ) . '/wtyczki/guliwera-maile/guliwera-maile.php';

$out = $argv[1] ?? __DIR__ . '/podglad';
@mkdir( $out, 0777, true );
$spis = array();
foreach ( gk_ml_podglad_lista() as $k => $m ) {
	file_put_contents( "$out/$k.html", gk_ml_layout( $m[3] ) );
	file_put_contents( "$out/$k.txt", 'Temat: ' . $m[2] . "\n\n" . gk_ml_tekst( $m[3] ) );
	$spis[] = array( 'klucz' => $k, 'grupa' => $m[0], 'opis' => $m[1], 'temat' => $m[2], 'dl' => mb_strlen( $m[2] ), 'preheader' => $m[3]['preheader'] ?? '' );
}
file_put_contents( "$out/spis.json", json_encode( $spis, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) );
foreach ( $spis as $s ) { printf( "%-18s %2d  %s\n", $s['klucz'], $s['dl'], $s['temat'] ); }
