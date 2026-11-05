<?php
defined( 'ABSPATH' ) || exit;
$gk_user = get_user_by( 'login', $user_login );
echo gk_ml_tekst( gk_ml_tresc_powitanie( $gk_user, $user_login, ! empty( $password_generated ) && ! empty( $set_password_url ) ? $set_password_url : '' ) ); // phpcs:ignore
