<?php
/**
 * Web Push bez zewnętrznych bibliotek: VAPID (RFC 8292, ES256) i szyfrowanie aes128gcm (RFC 8291 + RFC 8188).
 * Wymaga PHP 7.3+ z OpenSSL (openssl_pkey_derive). Klucze VAPID są tylko w bazie WordPressa (opcja), nigdy w repozytorium.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function gk_wp_b64u( $bin ) { return rtrim( strtr( base64_encode( $bin ), '+/', '-_' ), '=' ); }
function gk_wp_b64u_dec( $s ) { return base64_decode( strtr( $s, '-_', '+/' ) . str_repeat( '=', ( 4 - strlen( $s ) % 4 ) % 4 ) ); }

/** Surowy klucz publiczny P-256 (65 bajtów: 0x04 || X || Y) z klucza OpenSSL. */
function gk_wp_pub_raw( $key ) {
	$d = openssl_pkey_get_details( $key );
	return "\x04" . str_pad( $d['ec']['x'], 32, "\0", STR_PAD_LEFT ) . str_pad( $d['ec']['y'], 32, "\0", STR_PAD_LEFT );
}

/** Klucz publiczny z surowego punktu (65 bajtów) jako zasób OpenSSL (SubjectPublicKeyInfo DER → PEM). */
function gk_wp_pub_z_raw( $raw ) {
	$der = hex2bin( '3059301306072a8648ce3d020106082a8648ce3d030107034200' ) . $raw;
	$pem = "-----BEGIN PUBLIC KEY-----\n" . chunk_split( base64_encode( $der ), 64, "\n" ) . "-----END PUBLIC KEY-----\n";
	return openssl_pkey_get_public( $pem );
}

function gk_wp_nowy_klucz() {
	return openssl_pkey_new( array( 'curve_name' => 'prime256v1', 'private_key_type' => OPENSSL_KEYTYPE_EC ) );
}

/** Nowa para VAPID: array( 'prywatny' => PEM, 'publiczny' => base64url(65 bajtów) ). */
function gk_wp_vapid_nowe() {
	$k = gk_wp_nowy_klucz();
	openssl_pkey_export( $k, $pem );
	return array( 'prywatny' => $pem, 'publiczny' => gk_wp_b64u( gk_wp_pub_raw( $k ) ) );
}

/** Podpis DER (ECDSA) → surowe r||s (64 bajty), jak wymaga JWS ES256. */
function gk_wp_der_na_raw( $der ) {
	$o   = 2;
	$len = ord( $der[1] );
	if ( $len & 0x80 ) { $o += $len & 0x7f; }
	$wez = function () use ( $der, &$o ) {
		$o++; // 0x02
		$l = ord( $der[ $o ] );
		$o++;
		$v  = substr( $der, $o, $l );
		$o += $l;
		return str_pad( ltrim( $v, "\0" ), 32, "\0", STR_PAD_LEFT );
	};
	return $wez() . $wez();
}

/** Nagłówek Authorization dla VAPID. */
function gk_wp_vapid_naglowek( $endpoint, array $vapid, $kontakt ) {
	$u   = wp_parse_url( $endpoint );
	$aud = $u['scheme'] . '://' . $u['host'] . ( isset( $u['port'] ) ? ':' . $u['port'] : '' );
	$h   = gk_wp_b64u( wp_json_encode( array( 'typ' => 'JWT', 'alg' => 'ES256' ) ) );
	$p   = gk_wp_b64u( wp_json_encode( array( 'aud' => $aud, 'exp' => time() + 12 * HOUR_IN_SECONDS, 'sub' => 'mailto:' . $kontakt ) ) );
	openssl_sign( $h . '.' . $p, $sig, openssl_pkey_get_private( $vapid['prywatny'] ), OPENSSL_ALGO_SHA256 );
	return 'vapid t=' . $h . '.' . $p . '.' . gk_wp_b64u( gk_wp_der_na_raw( $sig ) ) . ', k=' . $vapid['publiczny'];
}

/**
 * Szyfruje treść dla subskrypcji (aes128gcm, jeden rekord).
 * $p256dh, $auth: base64url z subskrypcji przeglądarki. $test: stałe klucze/sól (tylko do testów).
 */
function gk_wp_szyfruj( $tresc, $p256dh, $auth, $test = null ) {
	$ua_pub  = gk_wp_b64u_dec( $p256dh );
	$auth    = gk_wp_b64u_dec( $auth );
	$as_key  = $test ? $test['klucz'] : gk_wp_nowy_klucz();
	$as_pub  = gk_wp_pub_raw( $as_key );
	$secret  = openssl_pkey_derive( gk_wp_pub_z_raw( $ua_pub ), $as_key, 32 );
	$sol     = $test ? $test['sol'] : random_bytes( 16 );
	$prk_key = hash_hmac( 'sha256', $secret, $auth, true );
	$ikm     = hash_hmac( 'sha256', "WebPush: info\0" . $ua_pub . $as_pub . "\x01", $prk_key, true );
	$prk     = hash_hmac( 'sha256', $ikm, $sol, true );
	$cek     = substr( hash_hmac( 'sha256', "Content-Encoding: aes128gcm\0\x01", $prk, true ), 0, 16 );
	$nonce   = substr( hash_hmac( 'sha256', "Content-Encoding: nonce\0\x01", $prk, true ), 0, 12 );
	$tag     = '';
	$szyfr   = openssl_encrypt( $tresc . "\x02", 'aes-128-gcm', $cek, OPENSSL_RAW_DATA, $nonce, $tag, '', 16 );
	return $sol . pack( 'N', 4096 ) . chr( 65 ) . $as_pub . $szyfr . $tag;
}

/**
 * Wysyła powiadomienie. Zwraca kod HTTP (201 = przyjęte; 404/410 = subskrypcja wygasła) albo 0 przy błędzie sieci.
 * $sub: array( endpoint, p256dh, auth )
 */
function gk_wp_wyslij( array $sub, $dane, array $vapid, $kontakt, $ttl = 43200 ) {
	if ( 0 !== strpos( $sub['endpoint'], 'https://' ) ) { return 0; }
	$body = gk_wp_szyfruj( wp_json_encode( $dane ), $sub['p256dh'], $sub['auth'] );
	$r    = wp_remote_post( $sub['endpoint'], array(
		'timeout' => 10,
		'headers' => array(
			'Authorization'    => gk_wp_vapid_naglowek( $sub['endpoint'], $vapid, $kontakt ),
			'Content-Encoding' => 'aes128gcm',
			'Content-Type'     => 'application/octet-stream',
			'TTL'              => (string) (int) $ttl,
			'Urgency'          => 'normal',
		),
		'body'    => $body,
	) );
	return is_wp_error( $r ) ? 0 : (int) wp_remote_retrieve_response_code( $r );
}
