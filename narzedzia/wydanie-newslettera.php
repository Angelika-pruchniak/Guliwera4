<?php
/**
 * Render wydania newslettera tygodniowego do HTML dla Mailchimpa (ten sam wygląd co maile sklepu).
 * Użycie: php narzedzia/wydanie-newslettera.php RRRR-MM-DD [KATALOG]
 */
define( 'ABSPATH', __DIR__ );
define( 'DAY_IN_SECONDS', 86400 ); define( 'YEAR_IN_SECONDS', 31536000 );
date_default_timezone_set( 'Europe/Warsaw' );
function add_action() {} function add_filter() {} function register_activation_hook() {}
function wp_date( $f, $ts = null ) { return date( $f, $ts ?? time() ); }
function home_url( $p = '' ) { return 'https://guliwera4.pl' . $p; }
function admin_url( $p = '' ) { return 'https://guliwera4.pl/wp-admin/' . $p; }
function wp_strip_all_tags( $s ) { return trim( strip_tags( preg_replace( '/<br\s*\/?>/i', "\n", $s ) ) ); }
function wp_parse_url( $u, $c = -1 ) { return parse_url( $u, $c ); }
function wc_get_page_permalink() { return 'https://guliwera4.pl/moje-konto/'; }
function wc_lostpassword_url() { return 'https://guliwera4.pl/moje-konto/lost-password/'; }
require dirname( __DIR__ ) . '/wtyczki/guliwera-maile/guliwera-maile.php';

$data = $argv[1] ?? '';
$wyd  = ( require dirname( __DIR__ ) . '/marketing/newsletter/wydania.php' );
if ( ! isset( $wyd[ $data ] ) ) { fwrite( STDERR, "Brak wydania $data\n" ); exit( 1 ); }
$w   = $wyd[ $data ];
$utm = function ( $u ) use ( $w ) { return $u . ( strpos( $u, '?' ) === false ? '?' : '&' ) . 'utm_source=newsletter&utm_medium=email&utm_campaign=' . $w['kampania']; };
$sek = function ( $etyk, $tyt ) { return '<p style="margin:26px 0 6px;font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:bold;letter-spacing:1.5px;text-transform:uppercase;color:#E8512A;">' . $etyk . '</p><h2 style="margin:0 0 12px;font-family:Georgia,\'Times New Roman\',serif;font-size:21px;line-height:1.25;color:#3A2340;">' . gk_ml_esc( $tyt ) . '</h2>'; };
$p   = function ( $h ) { return '<p style="margin:0 0 14px;font-family:Arial,Helvetica,sans-serif;font-size:16px;line-height:1.6;color:#3A2340;">' . $h . '</p>'; };

$po  = $sek( '1 · Wpis tygodnia', $w['wpis']['tytul'] );
foreach ( $w['wpis']['tresc'] as $t ) { $po .= $p( $t ); }
if ( $w['produkt']['url'] !== $w['wpis']['url'] ) { $po .= $p( '<a href="' . gk_ml_esc( $utm( $w['wpis']['url'] ) ) . '" style="color:#E8512A;font-weight:bold;">Przeczytaj cały wpis na blogu →</a>' ); }
$po .= $sek( '2 · Ćwiczenie', $w['cwiczenie']['tytul'] );
$c   = '';
foreach ( $w['cwiczenie']['kroki'] as $i => $k ) { $c .= '<b>' . ( $i + 1 ) . '.</b> ' . $k . '<br>'; }
$po .= gk_ml_ramka( $c, '#EDE5FB' );
$po .= $sek( '3 · Produkt tygodnia (oferta)', $w['produkt']['nazwa'] . ' · ' . $w['produkt']['cena'] );
$po .= $p( $w['produkt']['opis'] );
if ( $w['produkt']['url'] === $w['wpis']['url'] ) { $po .= $sek( 'Cały wpis tygodnia', $w['wpis']['tytul'] ); }

$a = array(
	'preheader'   => $w['preheader'],
	'nadtytul'    => 'Newsletter Guliwera 4 · ' . date( 'j.m.Y', strtotime( $data ) ),
	'tytul'       => $w['tytul'],
	'akapity'     => array( $w['wstep'] ),
	'po'          => $po,
	'cta'         => array( $w['produkt']['cta'], $utm( $w['produkt']['url'] ) ),
	'pod'         => '<p style="margin:6px 0 18px;font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.6;color:#7A6580;"><b>P.S.</b> ' . $w['ps'] . '</p>'
		. gk_ml_ramka( '🎓 <b>Akademia Guliwera:</b> 5 minut nauki dziennie, za darmo. <a href="' . gk_ml_esc( $utm( 'https://guliwera4.pl/aplikacja/' ) ) . '" style="color:#E8512A;font-weight:bold;">Otwórz Akademię</a>', '#FFF6EE' ),
	'uwaga_ikona' => '',
	'powod'       => 'Dostajesz tego maila, bo zapisano ten adres na newsletter Guliwera 4. [[MC]]',
	'tekst_po'    => '',
);
$html = gk_ml_layout( $a );
// Stopka Mailchimpa: wypis, ustawienia i adres (wymagane przez Mailchimp).
$html = str_replace( '[[MC]]', '<a href="*|UNSUB|*" style="color:#7A6580;">Wypisz się jednym kliknięciem</a> · <a href="*|UPDATE_PROFILE|*" style="color:#7A6580;">Ustawienia</a><br>*|LIST:ADDRESSLINE|*', $html );
$a['powod'] = 'Dostajesz tego maila, bo zapisano ten adres na newsletter Guliwera 4. Wypis: *|UNSUB|*';
$tekst = gk_ml_tekst( array_merge( $a, array( 'tekst_po' => gk_ml_bez_html( str_replace( array( '</p>', '<br>', '</h2>' ), "\n", $po ) ) ) ) );

$out = $argv[2] ?? dirname( __DIR__ ) . '/marketing/newsletter';
file_put_contents( "$out/$data.html", $html );
file_put_contents( "$out/$data.txt", "Temat A: {$w['temat_a']}\nTemat B: {$w['temat_b']}\nPreheader: {$w['preheader']}\n\n" . $tekst );
echo "ok: $out/$data.html (temat A " . mb_strlen( $w['temat_a'] ) . " zn., B " . mb_strlen( $w['temat_b'] ) . " zn.)\n";
