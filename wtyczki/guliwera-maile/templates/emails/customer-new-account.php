<?php
/**
 * Guliwera 4: mail powitalny po rejestracji (nadpisuje szablon WooCommerce).
 * Zmienne WooCommerce: $user_login, $password_generated, $set_password_url, $additional_content.
 */
defined( 'ABSPATH' ) || exit;
$gk_user = get_user_by( 'login', $user_login );
$gk_a    = gk_ml_tresc_powitanie( $gk_user, $user_login, ! empty( $password_generated ) && ! empty( $set_password_url ) ? $set_password_url : '' );
if ( ! empty( $additional_content ) ) {
	$gk_a['akapity'][] = wp_kses_post( wptexturize( $additional_content ) );
}
echo gk_ml_layout( $gk_a ); // phpcs:ignore WordPress.Security.EscapeOutput
