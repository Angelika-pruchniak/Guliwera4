<?php
/**
 * Plugin Name: Guliwera – maile
 * Description: Maile systemowe w wyglądzie Guliwera 4: powitanie po rejestracji, reset hasła, potwierdzenie zmiany hasła, alert o logowaniu z nowego urządzenia, zmiana e-maila. Ujednolica też kolory i nadawcę maili WooCommerce (zamówienia). Zostaw włączoną.
 * Version: 1.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'GK_ML_NADAWCA', 'kontakt@guliwera4.pl' );
define( 'GK_ML_NAZWA', 'Guliwera 4' );
define( 'GK_ML_LOGO', 'https://guliwera4.pl/wp-content/uploads/2026/11/guliwera4-ikona.png' );

/* ─────────────────────────── Szablon maila ───────────────────────────
 * Zasady: tabela 600 px, style w linii, czcionki systemowe (Georgia/Arial),
 * jeden główny przycisk („bulletproof”), link zapasowy pod przyciskiem,
 * ukryty preheader, wersja tekstowa, dane firmy i powód wysłania w stopce.
 */
function gk_ml_esc( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); }

/**
 * @param array $a preheader, nadtytul, tytul, akapity (HTML[]), cta [etykieta, url], lista (string[]), po (HTML), uwaga (HTML), cta2 [etykieta, url]
 */
function gk_ml_layout( array $a ) {
	$a = array_merge( array(
		'preheader' => '', 'nadtytul' => '', 'tytul' => '', 'akapity' => array(), 'cta' => null,
		'lista' => array(), 'po' => '', 'uwaga' => '', 'cta2' => null,
	), $a );
	$font  = "Arial,'Helvetica Neue',Helvetica,sans-serif";
	$serif = "Georgia,'Times New Roman',serif";
	$ink   = '#3A2340';
	$muted = '#7A6580';

	$h  = '<!DOCTYPE html><html lang="pl" xmlns="http://www.w3.org/1999/xhtml"><head><meta charset="utf-8">';
	$h .= '<meta name="viewport" content="width=device-width,initial-scale=1"><meta name="x-apple-disable-message-reformatting">';
	$h .= '<meta name="color-scheme" content="light"><meta name="supported-color-schemes" content="light">';
	$h .= '<title>' . gk_ml_esc( $a['tytul'] ) . '</title>';
	$h .= '<style>@media (max-width:620px){.gk-k{width:100%!important}.gk-p{padding-left:22px!important;padding-right:22px!important}.gk-t{font-size:26px!important}}</style>';
	$h .= '</head><body style="margin:0;padding:0;background:#FFF6EE;">';
	if ( $a['preheader'] ) {
		$h .= '<div style="display:none;max-height:0;overflow:hidden;mso-hide:all;font-size:1px;line-height:1px;color:#FFF6EE;">' . gk_ml_esc( $a['preheader'] ) . str_repeat( '&#8204;&nbsp;', 60 ) . '</div>';
	}
	$h .= '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#FFF6EE" style="background:#FFF6EE;"><tr><td align="center" style="padding:28px 12px;">';
	$h .= '<table role="presentation" class="gk-k" width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px;max-width:600px;">';

	// Logo
	$h .= '<tr><td style="padding:0 8px 18px;"><table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>';
	$h .= '<td style="padding-right:12px;"><img src="' . gk_ml_esc( GK_ML_LOGO ) . '" width="44" height="44" alt="G4" style="display:block;border:0;border-radius:12px;"></td>';
	$h .= '<td style="font-family:' . $serif . ';font-size:19px;font-weight:bold;color:' . $ink . ';line-height:1.1;">Guliwera 4<br><span style="font-family:' . $font . ';font-size:12px;font-weight:normal;color:' . $muted . ';">strategia, produkty cyfrowe i dobry humor</span></td>';
	$h .= '</tr></table></td></tr>';

	// Karta
	$h .= '<tr><td bgcolor="#FFFFFF" style="background:#FFFFFF;border-radius:24px;overflow:hidden;border:1px solid #F3E3D6;">';
	$h .= '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">';
	// Kolorowy pasek marki
	$h .= '<tr><td height="8" bgcolor="#FF6B3D" style="height:8px;line-height:8px;font-size:0;background:#FF6B3D;background-image:linear-gradient(90deg,#FF6B3D,#F4799F 55%,#B9A5E3);border-radius:24px 24px 0 0;">&nbsp;</td></tr>';
	$h .= '<tr><td class="gk-p" style="padding:34px 40px 10px;font-family:' . $font . ';color:' . $ink . ';">';
	if ( $a['nadtytul'] ) {
		$h .= '<p style="margin:0 0 8px;font-family:' . $font . ';font-size:13px;font-weight:bold;letter-spacing:1.5px;text-transform:uppercase;color:#E8512A;">' . gk_ml_esc( $a['nadtytul'] ) . '</p>';
	}
	$h .= '<h1 class="gk-t" style="margin:0 0 18px;font-family:' . $serif . ';font-size:30px;line-height:1.15;font-weight:bold;color:' . $ink . ';">' . gk_ml_esc( $a['tytul'] ) . '</h1>';
	foreach ( $a['akapity'] as $p ) {
		$h .= '<p style="margin:0 0 16px;font-family:' . $font . ';font-size:16px;line-height:1.6;color:' . $ink . ';">' . $p . '</p>';
	}
	if ( $a['lista'] ) {
		$h .= '<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:4px 0 18px;">';
		foreach ( $a['lista'] as $i => $li ) {
			$kolory = array( '#FFD8C2', '#FFD9E4', '#EDE5FB', '#FFEFC7' );
			$h .= '<tr><td valign="top" style="padding:0 12px 12px 0;"><div style="width:28px;height:28px;line-height:28px;border-radius:14px;text-align:center;background:' . $kolory[ $i % 4 ] . ';font-family:' . $font . ';font-size:14px;font-weight:bold;color:' . $ink . ';">' . ( $i + 1 ) . '</div></td>';
			$h .= '<td valign="top" style="padding:3px 0 12px;font-family:' . $font . ';font-size:15.5px;line-height:1.5;color:' . $ink . ';">' . $li . '</td></tr>';
		}
		$h .= '</table>';
	}
	if ( $a['po'] ) { $h .= $a['po']; }
	foreach ( array( 'cta', 'cta2' ) as $k ) {
		if ( ! $a[ $k ] ) { continue; }
		list( $etykieta, $url ) = $a[ $k ];
		$glowny = 'cta' === $k;
		$bg     = $glowny ? '#E8512A' : '#FFFFFF';
		$kolor  = $glowny ? '#FFFFFF' : '#E8512A';
		$h .= '<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:8px 0 ' . ( $glowny ? '14' : '20' ) . 'px;"><tr>';
		$h .= '<td align="center" bgcolor="' . $bg . '" style="border-radius:999px;background:' . $bg . ';border:2px solid #E8512A;">';
		$h .= '<a href="' . gk_ml_esc( $url ) . '" target="_blank" style="display:inline-block;padding:15px 32px;font-family:' . $font . ';font-size:18px;font-weight:bold;line-height:1.2;color:' . $kolor . ';text-decoration:none;border-radius:999px;">' . gk_ml_esc( $etykieta ) . '</a>';
		$h .= '</td></tr></table>';
		if ( $glowny ) {
			$h .= '<p style="margin:0 0 20px;font-family:' . $font . ';font-size:13px;line-height:1.5;color:' . $muted . ';">Przycisk nie działa? Skopiuj ten link do przeglądarki:<br><a href="' . gk_ml_esc( $url ) . '" style="color:#E8512A;word-break:break-all;">' . gk_ml_esc( $url ) . '</a></p>';
		}
	}
	$h .= '</td></tr>';
	if ( $a['uwaga'] ) {
		$h .= '<tr><td class="gk-p" style="padding:0 40px 30px;"><table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td bgcolor="#FFEFC7" style="background:#FFEFC7;border-radius:14px;padding:14px 16px;font-family:' . $font . ';font-size:14px;line-height:1.5;color:' . $ink . ';">🔒 ' . $a['uwaga'] . '</td></tr></table></td></tr>';
	} else {
		$h .= '<tr><td style="padding:0 0 20px;font-size:0;line-height:0;">&nbsp;</td></tr>';
	}
	$h .= '</table></td></tr>';

	// Stopka
	$h .= '<tr><td class="gk-p" style="padding:22px 24px 0;font-family:' . $font . ';font-size:12.5px;line-height:1.6;color:' . $muted . ';text-align:center;">';
	$h .= 'Masz pytanie? Odpisz na tego maila albo napisz na <a href="mailto:' . GK_ML_NADAWCA . '" style="color:#E8512A;">' . GK_ML_NADAWCA . '</a>.<br>';
	$h .= '<a href="https://guliwera4.pl/" style="color:' . $muted . ';">guliwera4.pl</a> · <a href="https://guliwera4.pl/akademia/" style="color:' . $muted . ';">Akademia</a> · <a href="https://guliwera4.pl/materialy/" style="color:' . $muted . ';">Materiały</a> · <a href="https://guliwera4.pl/polityka-prywatnosci/" style="color:' . $muted . ';">Prywatność</a><br>';
	$h .= 'Angelika Pruchniak-Gąszczyk, Guliwera 4 · ul. Guliwera 4, Lublin · NIP 9462713420<br>';
	$h .= 'To wiadomość systemowa dotycząca Twojego konta na guliwera4.pl, dlatego otrzymujesz ją niezależnie od zgód marketingowych.';
	$h .= '</td></tr>';
	$h .= '</table></td></tr></table></body></html>';
	return $h;
}

/** Wersja tekstowa (dla programów bez HTML i filtrów antyspamowych). */
function gk_ml_tekst( array $a ) {
	$t = mb_strtoupper( (string) ( isset( $a['tytul'] ) ? $a['tytul'] : '' ) ) . "\n\n";
	foreach ( isset( $a['akapity'] ) ? $a['akapity'] : array() as $p ) { $t .= wp_strip_all_tags( $p ) . "\n\n"; }
	foreach ( isset( $a['lista'] ) ? $a['lista'] : array() as $i => $li ) { $t .= ( $i + 1 ) . '. ' . wp_strip_all_tags( $li ) . "\n"; }
	foreach ( array( 'cta', 'cta2' ) as $k ) {
		if ( ! empty( $a[ $k ] ) ) { $t .= "\n" . $a[ $k ][0] . ': ' . $a[ $k ][1] . "\n"; }
	}
	if ( ! empty( $a['uwaga'] ) ) { $t .= "\n" . wp_strip_all_tags( $a['uwaga'] ) . "\n"; }
	$t .= "\n--\nGuliwera 4 · ul. Guliwera 4, Lublin · " . GK_ML_NADAWCA . " · https://guliwera4.pl/\n";
	return $t;
}

/* Wersja tekstowa dołączana do najbliższego wysyłanego maila (multipart/alternative). */
$GLOBALS['gk_ml_alt'] = null;
add_action( 'phpmailer_init', function ( $m ) {
	if ( $GLOBALS['gk_ml_alt'] ) {
		$m->AltBody            = $GLOBALS['gk_ml_alt'];
		$GLOBALS['gk_ml_alt'] = null;
	}
} );

function gk_ml_naglowki( $extra = array() ) {
	return array_merge( array(
		'Content-Type: text/html; charset=UTF-8',
		'From: ' . GK_ML_NAZWA . ' <' . GK_ML_NADAWCA . '>',
		'Reply-To: ' . GK_ML_NAZWA . ' <' . GK_ML_NADAWCA . '>',
	), (array) $extra );
}

function gk_ml_wyslij( $do, $temat, array $a ) {
	$GLOBALS['gk_ml_alt'] = gk_ml_tekst( $a );
	return wp_mail( $do, $temat, gk_ml_layout( $a ), gk_ml_naglowki() );
}

/* ─────────────────────────── Treści maili ─────────────────────────── */
function gk_ml_konto_url() { return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url(); }
function gk_ml_reset_url() { return function_exists( 'wc_lostpassword_url' ) ? wc_lostpassword_url() : wp_lostpassword_url(); }
function gk_ml_imie( $user ) {
	if ( ! $user ) { return ''; }
	$i = trim( (string) $user->first_name );
	return $i ? ', ' . $i : '';
}

function gk_ml_tresc_powitanie( $user, $login, $haslo_url = '' ) {
	$a = array(
		'preheader' => 'Konto działa. Zacznij od 5-minutowej lekcji w Akademii Guliwera.',
		'nadtytul'  => 'Konto gotowe',
		'tytul'     => 'Cześć' . gk_ml_imie( $user ) . '! Witaj w Guliwera 4',
		'akapity'   => array(
			'Twoje konto na guliwera4.pl jest aktywne. Logujesz się adresem e-mail albo loginem <b>' . gk_ml_esc( $login ) . '</b>.',
			'Od czego zacząć:',
		),
		'lista'     => array(
			'<b>Akademia Guliwera</b>: codzienne lekcje na 5 minut ze strategii, finansów i prowadzenia zespołu.',
			'<b>Rabat do 30%</b> na materiały Kierunek Zysk za regularną naukę. Postęp widać w Profilu w aplikacji.',
			'<b>Moje konto</b>: zamówienia, pliki do pobrania i dane konta w jednym miejscu.',
		),
		'cta'       => array( 'Zacznij pierwszą lekcję', home_url( '/akademia/' ) ),
		'uwaga'     => 'Nie zakładasz konta na guliwera4.pl? Napisz do nas na ' . GK_ML_NADAWCA . ', a je usuniemy. Nigdy nie prosimy o hasło w mailu.',
	);
	if ( $haslo_url ) {
		$a['cta2'] = array( 'Ustaw hasło do konta', $haslo_url );
	}
	return $a;
}

function gk_ml_tresc_reset( $login, $url ) {
	return array(
		'preheader' => 'Link do zmiany hasła jest ważny przez 24 godziny.',
		'nadtytul'  => 'Zmiana hasła',
		'tytul'     => 'Ustaw nowe hasło',
		'akapity'   => array(
			'Otrzymaliśmy prośbę o zmianę hasła do konta <b>' . gk_ml_esc( $login ) . '</b> na guliwera4.pl.',
			'Kliknij przycisk i wpisz nowe hasło. Link jest ważny przez 24 godziny i działa tylko raz.',
		),
		'cta'       => array( 'Ustaw nowe hasło', $url ),
		'uwaga'     => 'To nie Ty prosisz o zmianę? Zignoruj tę wiadomość. Twoje obecne hasło się nie zmieni i nikt nie dostanie dostępu do konta.',
	);
}

function gk_ml_tresc_haslo_zmienione( $user ) {
	return array(
		'preheader' => 'Potwierdzenie bezpieczeństwa: hasło do konta jest nowe.',
		'nadtytul'  => 'Bezpieczeństwo konta',
		'tytul'     => 'Hasło zostało zmienione',
		'akapity'   => array(
			'Hasło do Twojego konta <b>' . gk_ml_esc( $user->user_login ) . '</b> zostało zmienione ' . gk_ml_esc( wp_date( 'j.m.Y, \o H:i' ) ) . '.',
			'Od teraz logujesz się nowym hasłem.',
		),
		'cta'       => array( 'Przejdź do konta', gk_ml_konto_url() ),
		'uwaga'     => 'To nie Ty? <a href="' . gk_ml_esc( gk_ml_reset_url() ) . '" style="color:#E8512A;font-weight:bold;">Zresetuj hasło od razu</a> i napisz do nas na ' . GK_ML_NADAWCA . '.',
	);
}

function gk_ml_urzadzenie( $ua ) {
	$p = 'przeglądarka';
	foreach ( array( 'Edg/' => 'Edge', 'OPR/' => 'Opera', 'Firefox/' => 'Firefox', 'Chrome/' => 'Chrome', 'Safari/' => 'Safari' ) as $k => $v ) {
		if ( false !== strpos( $ua, $k ) ) { $p = $v; break; }
	}
	$s = 'nieznany system';
	foreach ( array( 'iPhone' => 'iPhone', 'iPad' => 'iPad', 'Android' => 'Android', 'Windows' => 'Windows', 'Mac OS X' => 'macOS', 'Linux' => 'Linux' ) as $k => $v ) {
		if ( false !== strpos( $ua, $k ) ) { $s = $v; break; }
	}
	return $p . ' · ' . $s;
}

function gk_ml_tresc_logowanie( $user, $ua, $ip ) {
	$ip_mask = preg_replace( '/\.\d+$/', '.xxx', (string) $ip );
	$ramka   = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 20px;"><tr><td bgcolor="#EDE5FB" style="background:#EDE5FB;border-radius:14px;padding:14px 18px;font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.7;color:#3A2340;">'
		. '<b>Kiedy:</b> ' . gk_ml_esc( wp_date( 'j.m.Y, \o H:i' ) ) . '<br><b>Urządzenie:</b> ' . gk_ml_esc( gk_ml_urzadzenie( $ua ) ) . '<br><b>Adres IP:</b> ' . gk_ml_esc( $ip_mask ) . '</td></tr></table>';
	return array(
		'preheader' => 'Ktoś zalogował się na Twoje konto z nowego urządzenia.',
		'nadtytul'  => 'Alert bezpieczeństwa',
		'tytul'     => 'Nowe logowanie na Twoje konto',
		'akapity'   => array( 'Na konto <b>' . gk_ml_esc( $user->user_login ) . '</b> zalogowano się z urządzenia, którego wcześniej nie widzieliśmy.' ),
		'po'        => $ramka . '<p style="margin:0 0 18px;font-family:Arial,Helvetica,sans-serif;font-size:16px;line-height:1.6;color:#3A2340;">To Ty? Super, nic nie musisz robić. Na tym urządzeniu nie będziemy już o tym przypominać.</p>',
		'cta'       => array( 'To nie ja: zmień hasło', gk_ml_reset_url() ),
		'uwaga'     => 'Jeśli to nie Ty, zmień hasło jak najszybciej i napisz do nas na ' . GK_ML_NADAWCA . '. Nigdy nie prosimy o hasło w mailu ani przez telefon.',
	);
}

function gk_ml_tresc_email_zmieniony( $user, $nowy ) {
	return array(
		'preheader' => 'Adres e-mail konta został zmieniony.',
		'nadtytul'  => 'Bezpieczeństwo konta',
		'tytul'     => 'Zmieniono adres e-mail konta',
		'akapity'   => array(
			'Adres e-mail konta <b>' . gk_ml_esc( $user->user_login ) . '</b> został zmieniony na <b>' . gk_ml_esc( $nowy ) . '</b>.',
			'Ten mail wysyłamy na poprzedni adres, żeby mieć pewność, że zmiana jest zamierzona.',
		),
		'cta'       => array( 'Przejdź do konta', gk_ml_konto_url() ),
		'uwaga'     => 'To nie Ty? Napisz od razu na ' . GK_ML_NADAWCA . ', a przywrócimy poprzedni adres.',
	);
}

/* ─────────────────────────── WooCommerce ─────────────────────────── */
// Nasze szablony dla: nowe konto, reset hasła (HTML i tekst).
add_filter( 'woocommerce_locate_template', function ( $template, $name ) {
	$nasze = array( 'emails/customer-new-account.php', 'emails/customer-reset-password.php', 'emails/plain/customer-new-account.php', 'emails/plain/customer-reset-password.php' );
	if ( in_array( $name, $nasze, true ) ) {
		$f = __DIR__ . '/templates/' . $name;
		if ( is_file( $f ) ) { return $f; }
	}
	return $template;
}, 20, 2 );

add_filter( 'woocommerce_email_subject_customer_new_account', function () { return 'Witaj w Guliwera 4! Twoje konto jest gotowe'; } );
add_filter( 'woocommerce_email_heading_customer_new_account', function () { return 'Witaj w Guliwera 4'; } );
add_filter( 'woocommerce_email_subject_customer_reset_password', function () { return 'Ustaw nowe hasło do konta Guliwera 4'; } );
add_filter( 'woocommerce_email_heading_customer_reset_password', function () { return 'Ustaw nowe hasło'; } );

// Kolory i nadawca pozostałych maili WooCommerce (zamówienia, faktury) w barwach marki.
$gk_ml_wc = array(
	'woocommerce_email_from_name'              => GK_ML_NAZWA,
	'woocommerce_email_from_address'           => GK_ML_NADAWCA,
	'woocommerce_email_base_color'             => '#E8512A',
	'woocommerce_email_background_color'       => '#FFF6EE',
	'woocommerce_email_body_background_color'  => '#FFFFFF',
	'woocommerce_email_text_color'             => '#3A2340',
	'woocommerce_email_footer_text_color'      => '#7A6580',
	'woocommerce_email_footer_text'            => 'Guliwera 4 · ul. Guliwera 4, Lublin · NIP 9462713420 · ' . GK_ML_NADAWCA,
);
foreach ( $gk_ml_wc as $opcja => $wartosc ) {
	add_filter( 'pre_option_' . $opcja, function () use ( $wartosc ) { return $wartosc; } );
}

// Po resecie hasła przez „Moje konto”: potwierdzenie dla klienta (zamiast zbędnego maila do administratora).
add_filter( 'woocommerce_disable_password_change_notification', '__return_true' );
add_action( 'woocommerce_customer_reset_password', function ( $user ) {
	if ( $user instanceof WP_User ) { gk_ml_wyslij( $user->user_email, 'Twoje hasło do Guliwera 4 zostało zmienione', gk_ml_tresc_haslo_zmienione( $user ) ); }
} );

/* ─────────────────────────── Maile WordPressa ─────────────────────────── */
// Reset hasła z wp-login.php.
add_filter( 'retrieve_password_notification_email', function ( $mail, $key, $login ) {
	$url                  = network_site_url( 'wp-login.php?action=rp&key=' . $key . '&login=' . rawurlencode( $login ), 'login' );
	$a                    = gk_ml_tresc_reset( $login, $url );
	$GLOBALS['gk_ml_alt'] = gk_ml_tekst( $a );
	$mail['subject']      = 'Ustaw nowe hasło do konta Guliwera 4';
	$mail['message']      = gk_ml_layout( $a );
	$mail['headers']      = gk_ml_naglowki();
	return $mail;
}, 20, 3 );

// Reset hasła z wp-login.php zakończony: potwierdzenie.
add_action( 'after_password_reset', function ( $user ) {
	gk_ml_wyslij( $user->user_email, 'Twoje hasło do Guliwera 4 zostało zmienione', gk_ml_tresc_haslo_zmienione( $user ) );
} );

// Zmiana hasła w profilu / na koncie.
add_filter( 'password_change_email', function ( $mail, $user ) {
	$u                    = get_userdata( $user['ID'] );
	$a                    = gk_ml_tresc_haslo_zmienione( $u );
	$GLOBALS['gk_ml_alt'] = gk_ml_tekst( $a );
	$mail['subject']      = 'Twoje hasło do Guliwera 4 zostało zmienione';
	$mail['message']      = gk_ml_layout( $a );
	$mail['headers']      = gk_ml_naglowki();
	return $mail;
}, 20, 2 );

// Zmiana adresu e-mail.
add_filter( 'email_change_email', function ( $mail, $user, $userdata ) {
	$u                    = get_userdata( $user['ID'] );
	$a                    = gk_ml_tresc_email_zmieniony( $u, $userdata['user_email'] );
	$GLOBALS['gk_ml_alt'] = gk_ml_tekst( $a );
	$mail['subject']      = 'Zmieniono adres e-mail konta Guliwera 4';
	$mail['message']      = gk_ml_layout( $a );
	$mail['headers']      = gk_ml_naglowki();
	return $mail;
}, 20, 3 );

// Konto założone przez administratora (wp-admin → Użytkownicy → Dodaj).
add_filter( 'wp_new_user_notification_email', function ( $mail, $user ) {
	$key = get_password_reset_key( $user );
	if ( is_wp_error( $key ) ) { return $mail; }
	$url                  = network_site_url( 'wp-login.php?action=rp&key=' . $key . '&login=' . rawurlencode( $user->user_login ), 'login' );
	$a                    = gk_ml_tresc_powitanie( $user, $user->user_login, $url );
	$a['cta']             = array( 'Ustaw hasło do konta', $url );
	$a['cta2']            = array( 'Zobacz Akademię', home_url( '/akademia/' ) );
	$GLOBALS['gk_ml_alt'] = gk_ml_tekst( $a );
	$mail['subject']      = 'Witaj w Guliwera 4! Ustaw hasło do konta';
	$mail['message']      = gk_ml_layout( $a );
	$mail['headers']      = gk_ml_naglowki();
	return $mail;
}, 20, 2 );

/* ─────────────────────────── Logowanie z nowego urządzenia ───────────────────────────
 * Dobra praktyka: nie wysyłamy maila przy każdym logowaniu (to spam), tylko gdy
 * logowanie przychodzi z nieznanego urządzenia. Urządzenie = losowy token w ciasteczku
 * (w bazie trzymamy tylko jego skrót). Pierwsze urządzenie zapisujemy bez alertu.
 */
function gk_ml_token() {
	if ( ! empty( $_COOKIE['gk_urz'] ) && preg_match( '/^[a-f0-9]{32}$/', $_COOKIE['gk_urz'] ) ) { return $_COOKIE['gk_urz']; }
	$t = bin2hex( random_bytes( 16 ) );
	setcookie( 'gk_urz', $t, time() + YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
	$_COOKIE['gk_urz'] = $t;
	return $t;
}

function gk_ml_zapamietaj( $uid, $hash ) {
	$lista          = get_user_meta( $uid, 'gk_urzadzenia', true );
	$lista          = is_array( $lista ) ? $lista : array();
	$lista[ $hash ] = time();
	arsort( $lista );
	update_user_meta( $uid, 'gk_urzadzenia', array_slice( $lista, 0, 10, true ) );
	return $lista;
}

add_action( 'wp_login', function ( $login, $user ) {
	if ( ! $user instanceof WP_User ) { return; }
	$hash  = hash( 'sha256', gk_ml_token() );
	$lista = get_user_meta( $user->ID, 'gk_urzadzenia', true );
	$znane = is_array( $lista ) ? $lista : array();
	gk_ml_zapamietaj( $user->ID, $hash );
	if ( ! $znane || isset( $znane[ $hash ] ) ) { return; }
	$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? substr( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ), 0, 300 ) : '';
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? wp_unslash( $_SERVER['REMOTE_ADDR'] ) : '';
	gk_ml_wyslij( $user->user_email, 'Nowe logowanie na Twoje konto Guliwera 4', gk_ml_tresc_logowanie( $user, $ua, $ip ) );
}, 20, 2 );

// Rejestracja w sklepie: to urządzenie od razu uznajemy za znane.
add_action( 'woocommerce_created_customer', function ( $uid ) {
	gk_ml_zapamietaj( $uid, hash( 'sha256', gk_ml_token() ) );
} );

/* ─────────────────────────── Podgląd dla administratora ───────────────────────────
 * guliwera4.pl/?gk_mail_podglad=powitanie|reset|haslo|logowanie|email (tylko zalogowany administrator).
 */
add_action( 'template_redirect', function () {
	if ( empty( $_GET['gk_mail_podglad'] ) || ! current_user_can( 'manage_options' ) ) { return; }
	$u = wp_get_current_user();
	switch ( sanitize_key( $_GET['gk_mail_podglad'] ) ) {
		case 'reset':     $a = gk_ml_tresc_reset( $u->user_login, gk_ml_reset_url() . '?key=PRZYKLAD' ); break;
		case 'haslo':     $a = gk_ml_tresc_haslo_zmienione( $u ); break;
		case 'logowanie': $a = gk_ml_tresc_logowanie( $u, isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '', '31.61.238.144' ); break;
		case 'email':     $a = gk_ml_tresc_email_zmieniony( $u, 'nowy@przyklad.pl' ); break;
		default:          $a = gk_ml_tresc_powitanie( $u, $u->user_login );
	}
	nocache_headers();
	header( 'Content-Type: text/html; charset=utf-8' );
	echo gk_ml_layout( $a ); // phpcs:ignore
	exit;
} );
