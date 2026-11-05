<?php
/**
 * Guliwera 4: nadpisuje szablon WooCommerce emails/customer-cancelled-order.php.
 * Treść buduje gk_ml_wc_mail() (includes/sklep.php + includes/tresci-sklep.php).
 */
defined( 'ABSPATH' ) || exit;
echo gk_ml_wc_render( get_defined_vars() ); // phpcs:ignore WordPress.Security.EscapeOutput
