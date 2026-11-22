<?php
defined( 'ABSPATH' ) || exit;
$gk_url = add_query_arg(
	array( 'key' => $reset_key, 'id' => $user_id, 'login' => rawurlencode( $user_login ) ),
	wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) )
);
echo gk_ml_tekst( gk_ml_tresc_reset( $user_login, $gk_url ) ); // phpcs:ignore
