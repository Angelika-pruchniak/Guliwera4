<?php
/**
 * Lokalny podgląd maili wtyczki guliwera-newsletter (bez WordPressa).
 * Użycie: php narzedzia/podglad-newslettera.php KATALOG_WYJSCIOWY
 */
define( 'ABSPATH', __DIR__ );
define( 'DAY_IN_SECONDS', 86400 ); define( 'YEAR_IN_SECONDS', 31536000 ); define( 'MINUTE_IN_SECONDS', 60 ); define( 'HOUR_IN_SECONDS', 3600 );
date_default_timezone_set( 'Europe/Warsaw' );
define( 'GK_ML_LOGO', 'file://' . dirname( __DIR__ ) . '/logo/ikona-512.png' );
function add_action() {} function add_filter() {} function register_activation_hook() {} function register_deactivation_hook() {} function add_shortcode() {}
function wp_date( $f, $ts = null ) { return date( $f, $ts ?? time() ); }
function home_url( $p = '' ) { return 'https://guliwera4.pl' . $p; }
function admin_url( $p = '' ) { return 'https://guliwera4.pl/wp-admin/' . $p; }
function wp_strip_all_tags( $s ) { return trim( strip_tags( preg_replace( '/<br\s*\/?>/i', "\n", $s ) ) ); }
function wp_parse_url( $u, $c = -1 ) { return parse_url( $u, $c ); }
function esc_url( $u ) { return htmlspecialchars( $u, ENT_QUOTES ); }
function wc_get_page_permalink() { return 'https://guliwera4.pl/moje-konto/'; }
function wc_lostpassword_url() { return 'https://guliwera4.pl/moje-konto/lost-password/'; }
function add_query_arg( $a, $b = null, $c = null ) {
	if ( is_array( $a ) ) { $url = $b; $q = $a; } else { $url = $c; $q = array( $a => $b ); }
	return $url . ( strpos( $url, '?' ) === false ? '?' : '&' ) . http_build_query( $q );
}
require dirname( __DIR__ ) . '/wtyczki/guliwera-maile/guliwera-maile.php';
require dirname( __DIR__ ) . '/wtyczki/guliwera-newsletter/guliwera-newsletter.php';
$out = $argv[1] ?? __DIR__ . '/podglad-nl';
@mkdir( $out, 0777, true );
$spis = array();
foreach ( gk_nl_podglad_lista() as $k => $m ) {
	file_put_contents( "$out/$k.html", $m[2] );
	file_put_contents( "$out/$k.txt", 'Temat: ' . $m[1] . "\n\n" . $m[3] );
	$spis[] = array( 'klucz' => $k, 'opis' => $m[0], 'temat' => $m[1], 'dl' => mb_strlen( $m[1] ) );
	printf( "%-6s %2d  %s\n", $k, mb_strlen( $m[1] ), $m[1] );
}
file_put_contents( "$out/spis.json", json_encode( $spis, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) );
