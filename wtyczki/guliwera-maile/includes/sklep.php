<?php
/**
 * Guliwera 4: integracja maili z WooCommerce.
 * - pola w zamówieniu (blokowy formularz zamówienia): zgoda na natychmiastowe dostarczenie, dane do faktury,
 * - dane zamówienia do treści maili,
 * - nasze szablony, tematy i wersja tekstowa (multipart) dla maili WooCommerce,
 * - mail z kuponem Akademii.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'GK_ML_POLE_ZGODA', 'guliwera/zgoda-cyfrowe' );
define( 'GK_ML_POLE_FAKTURA', 'guliwera/faktura' );
define( 'GK_ML_POLE_NIP', 'guliwera/nip' );
define( 'GK_ML_POLE_DANE', 'guliwera/faktura-dane' );

/* ─────────────────────────── Pola w zamówieniu ─────────────────────────── */
add_action( 'woocommerce_init', function () {
	if ( ! function_exists( 'woocommerce_register_additional_checkout_field' ) ) { return; }
	$tylko_z_faktura = array(
		'type'       => 'object',
		'properties' => array( 'checkout' => array( 'properties' => array( 'additional_fields' => array(
			'properties' => array( GK_ML_POLE_FAKTURA => array( 'not' => array( 'const' => true ) ) ),
		) ) ) ),
	);
	woocommerce_register_additional_checkout_field( array(
		'id'            => GK_ML_POLE_ZGODA,
		'label'         => GK_ML_ZGODA_TEKST,
		'location'      => 'order',
		'type'          => 'checkbox',
		'required'      => true,
		'error_message' => 'Zaznacz zgodę na dostarczenie plików od razu po zapłacie. Bez niej nie możemy wysłać plików.',
	) );
	woocommerce_register_additional_checkout_field( array(
		'id'       => GK_ML_POLE_FAKTURA,
		'label'         => 'Chcę fakturę',
		'optionalLabel' => 'Chcę fakturę',
		'location'      => 'order',
		'type'     => 'checkbox',
	) );
	woocommerce_register_additional_checkout_field( array(
		'id'            => GK_ML_POLE_DANE,
		'label'         => 'Nazwa firmy (albo imię i nazwisko) i adres do faktury',
		'optionalLabel' => 'Nazwa firmy (albo imię i nazwisko) i adres do faktury',
		'location'      => 'order',
		'type'          => 'text',
		'hidden'        => $tylko_z_faktura,
	) );
	woocommerce_register_additional_checkout_field( array(
		'id'            => GK_ML_POLE_NIP,
		'label'         => 'NIP (jeśli faktura na firmę)',
		'optionalLabel' => 'NIP (jeśli faktura na firmę)',
		'location'      => 'order',
		'type'          => 'text',
		'hidden'        => $tylko_z_faktura,
		'attributes'    => array( 'autocomplete' => 'off', 'inputmode' => 'numeric', 'maxLength' => 13 ),
	) );
} );

function gk_ml_nip_poprawny( $nip ) {
	$nip = preg_replace( '/\D/', '', (string) $nip );
	if ( 10 !== strlen( $nip ) ) { return false; }
	$w = array( 6, 5, 7, 2, 3, 4, 5, 6, 7 );
	$s = 0;
	for ( $i = 0; $i < 9; $i++ ) { $s += $w[ $i ] * (int) $nip[ $i ]; }
	return ( $s % 11 ) === (int) $nip[9];
}

// Dane do faktury: wymagane po zaznaczeniu „Chcę fakturę”, NIP z poprawną sumą kontrolną.
add_action( 'woocommerce_blocks_validate_location_order_fields', function ( $errors, $fields ) {
	if ( empty( $fields[ GK_ML_POLE_FAKTURA ] ) ) { return; }
	if ( mb_strlen( trim( (string) ( $fields[ GK_ML_POLE_DANE ] ?? '' ) ) ) < 5 ) {
		$errors->add( 'gk_faktura_dane', 'Podaj nazwę (albo imię i nazwisko) i adres do faktury.' );
	}
	$nip = trim( (string) ( $fields[ GK_ML_POLE_NIP ] ?? '' ) );
	if ( '' !== $nip && ! gk_ml_nip_poprawny( $nip ) ) {
		$errors->add( 'gk_faktura_nip', 'Sprawdź NIP: powinien mieć 10 cyfr (np. 9462713420).' );
	}
}, 10, 2 );

// Zapisujemy datę zgody i dane faktury w zamówieniu pod stałymi kluczami (widać je w panelu zamówienia).
add_action( 'woocommerce_store_api_checkout_order_processed', 'gk_ml_zapisz_pola', 5 );
function gk_ml_zapisz_pola( $order ) {
	if ( ! $order instanceof WC_Order ) { return; }
	$pole = function ( $id ) use ( $order ) { return $order->get_meta( '_wc_other/' . $id ); };
	if ( $pole( GK_ML_POLE_ZGODA ) && ! $order->get_meta( '_gk_zgoda_cyfrowe' ) ) {
		$order->update_meta_data( '_gk_zgoda_cyfrowe', time() );
		$order->update_meta_data( '_gk_zgoda_tekst', GK_ML_ZGODA_TEKST );
	}
	if ( $pole( GK_ML_POLE_FAKTURA ) ) {
		$order->update_meta_data( '_gk_faktura', 1 );
		$order->update_meta_data( '_gk_faktura_nip', preg_replace( '/\D/', '', (string) $pole( GK_ML_POLE_NIP ) ) );
		$order->update_meta_data( '_gk_faktura_dane', sanitize_textarea_field( (string) $pole( GK_ML_POLE_DANE ) ) );
	}
	$order->save();
}

// Panel zamówienia: zgoda i faktura na widoku, pod danymi klienta.
add_action( 'woocommerce_admin_order_data_after_billing_address', function ( $order ) {
	$z = (int) $order->get_meta( '_gk_zgoda_cyfrowe' );
	echo '<p><strong>Zgoda na natychmiastowe dostarczenie:</strong> ' . ( $z ? esc_html( gk_ml_data( $z ) ) : '<span style="color:#b32d2e">brak</span>' ) . '</p>';
	if ( $order->get_meta( '_gk_faktura' ) ) {
		echo '<p><strong>Faktura:</strong> ' . esc_html( $order->get_meta( '_gk_faktura_dane' ) ) . '<br>NIP: ' . esc_html( $order->get_meta( '_gk_faktura_nip' ) ?: 'brak' ) . '</p>';
	}
} );

/* ─────────────────────────── Dane zamówienia do maili ─────────────────────────── */
function gk_ml_kwota( $html ) { return gk_ml_bez_html( $html ); }

function gk_ml_dane_wc( $order ) {
	$poz = array();
	foreach ( $order->get_items() as $it ) {
		$poz[] = array( $it->get_name(), $it->get_quantity(), gk_ml_kwota( $order->get_formatted_line_subtotal( $it ) ) );
	}
	$pods = array();
	foreach ( $order->get_order_item_totals() as $k => $r ) {
		if ( 'payment_method' === $k ) { continue; }
		$pods[] = array( rtrim( gk_ml_bez_html( $r['label'] ), ':' ), gk_ml_kwota( $r['value'] ) );
	}
	if ( $pods ) { $pods[ count( $pods ) - 1 ][0] = 'Razem'; }

	$pliki = array();
	if ( $order->is_download_permitted() ) {
		foreach ( $order->get_downloadable_items() as $p ) {
			$exp     = $p['access_expires'];
			$pliki[] = array(
				count( $order->get_items() ) > 1 || $p['download_name'] !== $p['product_name'] ? $p['download_name'] : $p['product_name'],
				$p['download_url'],
				'' === $p['downloads_remaining'] ? '' : (int) $p['downloads_remaining'],
				$exp ? wp_date( 'j.m.Y', is_object( $exp ) ? $exp->getTimestamp() : strtotime( $exp ) ) : '',
			);
		}
	}

	$konto = $order->get_customer_id() > 0;
	$ids   = array();
	foreach ( $order->get_items() as $it ) { $ids[] = $it->get_product_id(); }
	$ponownie = 1 === count( $ids ) ? home_url( '/zamowienie/?add-to-cart=' . (int) $ids[0] ) : home_url( '/materialy/' );

	$instr = '';
	if ( $order->has_status( 'on-hold' ) ) {
		ob_start();
		do_action( 'woocommerce_email_before_order_table', $order, false, false, null );
		$instr = trim( ob_get_clean() );
		$instr = $instr ? gk_ml_ramka( $instr, '#FFEFC7' ) : '';
	}

	return array(
		'numer'           => $order->get_order_number(),
		'data'            => wp_date( 'j.m.Y, H:i', $order->get_date_created() ? $order->get_date_created()->getTimestamp() : time() ),
		'imie'            => $order->get_billing_first_name(),
		'imie_nazwisko'   => $order->get_formatted_billing_full_name(),
		'email'           => $order->get_billing_email(),
		'pozycje'         => $poz,
		'podsumowanie'    => $pods,
		'suma'            => gk_ml_kwota( $order->get_formatted_order_total() ),
		'metoda'          => $order->get_payment_method_title() ? $order->get_payment_method_title() : 'płatność online',
		'pliki'           => $pliki,
		'konto'           => $konto,
		'zaplacone'       => $order->is_paid(),
		'url_pliki'       => wc_get_account_endpoint_url( 'downloads' ),
		'url_zamowienia'  => $konto ? $order->get_view_order_url() : $order->get_checkout_order_received_url(),
		'url_platnosci'   => $order->get_checkout_payment_url(),
		'url_ponownie'    => $ponownie,
		'url_admin'       => $order->get_edit_order_url(),
		'zgoda'           => (int) $order->get_meta( '_gk_zgoda_cyfrowe' ),
		'faktura'         => $order->get_meta( '_gk_faktura' ) ? array( (string) $order->get_meta( '_gk_faktura_nip' ), (string) $order->get_meta( '_gk_faktura_dane' ) ) : null,
		'kupon'           => implode( ', ', $order->get_coupon_codes() ),
		'notatka_klienta' => $order->get_customer_note(),
		'instrukcje'      => $instr,
	);
}

/* ─────────────────────────── Maile WooCommerce → nasze treści ─────────────────────────── */
/** ID maila WooCommerce → [nasz typ, szablon]. */
function gk_ml_mapa_wc() {
	return array(
		'customer_on_hold_order'              => array( 'oczekuje', 'customer-on-hold-order' ),
		'customer_processing_order'           => array( 'oplacone', 'customer-processing-order' ),
		'customer_completed_order'            => array( 'pliki', 'customer-completed-order' ),
		'customer_failed_order'               => array( 'nieudane', 'customer-failed-order' ),
		'customer_cancelled_order'            => array( 'anulowane', 'customer-cancelled-order' ),
		'customer_refunded_order'             => array( 'zwrot', 'customer-refunded-order' ),
		'customer_partially_refunded_order'   => array( 'zwrot_czesc', 'customer-refunded-order' ),
		'customer_note'                       => array( 'notatka', 'customer-note' ),
		'customer_invoice'                    => array( 'szczegoly', 'customer-invoice' ),
		'customer_order_withdrawal_requested' => array( 'odstapienie', 'customer-order-withdrawal-requested' ),
		'new_order'                           => array( 'admin_nowe', 'admin-new-order' ),
		'failed_order'                        => array( 'admin_nieudane', 'admin-failed-order' ),
		'cancelled_order'                     => array( 'admin_anulowane', 'admin-cancelled-order' ),
		'order_withdrawal_requested'          => array( 'admin_odstapienie', 'admin-order-withdrawal-requested' ),
	);
}

/**
 * Buduje [temat, treść] dla maila WooCommerce. Wywołują to szablony z templates/emails/ i filtr tematu.
 * Wynik trzymamy w pamięci, żeby temat i treść liczyć raz na wysyłkę.
 */
function gk_ml_wc_mail( $email, array $vars = array() ) {
	static $pamiec = array();
	$mapa = gk_ml_mapa_wc();
	$id   = $email && isset( $email->id ) ? $email->id : '';
	if ( ! isset( $mapa[ $id ] ) ) { return array( '', array() ); }
	$typ   = $mapa[ $id ][0];
	$order = isset( $vars['order'] ) ? $vars['order'] : ( $email->object instanceof WC_Order ? $email->object : null );
	$x     = array();

	if ( in_array( $typ, array( 'odstapienie', 'admin_odstapienie' ), true ) ) {
		$wd = isset( $vars['withdrawal_data'] ) ? (array) $vars['withdrawal_data'] : (array) $email->object;
		$x['wiersze'] = isset( $vars['detail_rows'] ) ? $vars['detail_rows'] : array();
		$numer        = '';
		foreach ( $wd as $k => $v ) { if ( false !== strpos( (string) $k, 'order_number' ) ) { $numer = (string) $v; } }
		if ( ! $order && $numer && function_exists( 'wc_get_order' ) ) { $order = wc_get_order( absint( $numer ) ); }
		if ( ! $order ) {
			$d = gk_ml_dane_puste( $numer );
			return 'odstapienie' === $typ ? gk_ml_mail_zamowienie( $typ, $d, $x ) : gk_ml_mail_admin( $typ, $d, $x );
		}
	}
	if ( ! $order instanceof WC_Order ) { return array( '', array() ); }

	$notatka = isset( $vars['customer_note'] ) ? $vars['customer_note'] : ( isset( $email->customer_note ) ? (string) $email->customer_note : '' );
	$refund  = ! empty( $vars['refund'] ) ? $vars['refund'] : ( ! empty( $email->refund ) ? $email->refund : null );
	$klucz   = $id . ':' . $order->get_id() . ':' . $order->get_status() . ':' . md5( $notatka ) . ':' . ( $refund ? $refund->get_id() : 0 );
	if ( isset( $pamiec[ $klucz ] ) ) { return $pamiec[ $klucz ]; }

	if ( 'notatka' === $typ ) { $x['notatka'] = $notatka; }
	if ( 0 === strpos( $typ, 'zwrot' ) ) {
		if ( $refund ) {
			$x['kwota'] = gk_ml_kwota( wc_price( $refund->get_amount(), array( 'currency' => $order->get_currency() ) ) );
			$x['powod'] = $refund->get_reason();
		} else {
			$x['kwota'] = gk_ml_kwota( wc_price( $order->get_total_refunded(), array( 'currency' => $order->get_currency() ) ) );
		}
	}
	$d   = gk_ml_dane_wc( $order );
	$wyn = 0 === strpos( $typ, 'admin_' ) ? gk_ml_mail_admin( $typ, $d, $x ) : gk_ml_mail_zamowienie( $typ, $d, $x );
	return $pamiec[ $klucz ] = $wyn;
}

function gk_ml_dane_puste( $numer ) {
	return array(
		'numer' => $numer, 'data' => '', 'imie' => '', 'imie_nazwisko' => '', 'email' => '', 'pozycje' => array(), 'podsumowanie' => array(),
		'suma' => '', 'metoda' => '', 'pliki' => array(), 'konto' => false, 'zaplacone' => false, 'url_pliki' => '', 'url_zamowienia' => '',
		'url_platnosci' => '', 'url_ponownie' => home_url( '/materialy/' ), 'url_admin' => admin_url( 'admin.php?page=wc-orders' ), 'zgoda' => 0,
		'faktura' => null, 'kupon' => '', 'notatka_klienta' => '', 'instrukcje' => '',
	);
}

/** Wywoływane z szablonów: HTML albo tekst. */
function gk_ml_wc_render( $vars, $tekst = false ) {
	$email = isset( $vars['email'] ) ? $vars['email'] : null;
	list( , $a ) = gk_ml_wc_mail( $email, $vars );
	if ( ! $a ) { return ''; }
	return $tekst ? gk_ml_tekst( $a ) : gk_ml_layout( $a );
}

// Nasze szablony zamiast szablonów WooCommerce (HTML i tekst).
add_filter( 'woocommerce_locate_template', function ( $template, $name ) {
	$nasze = array( 'customer-new-account', 'customer-reset-password' );
	foreach ( gk_ml_mapa_wc() as $m ) { $nasze[] = $m[1]; }
	foreach ( array_unique( $nasze ) as $s ) {
		if ( 'emails/' . $s . '.php' === $name || 'emails/plain/' . $s . '.php' === $name ) {
			$f = GK_ML_KATALOG . '/templates/' . $name;
			if ( is_file( $f ) ) { return $f; }
		}
	}
	return $template;
}, 20, 2 );

// Tematy (≤ 50 znaków) liczone z tych samych danych co treść.
foreach ( array_merge( array_keys( gk_ml_mapa_wc() ), array( 'customer_invoice_paid' ) ) as $gk_id ) {
	add_filter( 'woocommerce_email_subject_' . $gk_id, function ( $subject, $object, $email = null ) {
		if ( ! $email ) { return $subject; }
		list( $t ) = gk_ml_wc_mail( $email, $object instanceof WC_Order ? array( 'order' => $object ) : array() );
		return $t ? $t : $subject;
	}, 20, 3 );
}

// Wersja tekstowa (multipart) dla wszystkich naszych maili i włączenie maili klienta o nieudanej i anulowanej płatności.
$gk_ml_ustawienia = array_merge( array_keys( gk_ml_mapa_wc() ), array( 'customer_new_account', 'customer_reset_password' ) );
foreach ( $gk_ml_ustawienia as $gk_id ) {
	$gk_f = function ( $v ) use ( $gk_id ) {
		$v                = is_array( $v ) ? $v : array();
		$v['email_type']  = 'multipart';
		if ( in_array( $gk_id, array( 'customer_failed_order', 'customer_cancelled_order' ), true ) ) { $v['enabled'] = 'yes'; }
		return $v;
	};
	add_filter( 'option_woocommerce_' . $gk_id . '_settings', $gk_f );
	add_filter( 'default_option_woocommerce_' . $gk_id . '_settings', $gk_f );
}

// Prośba o opinię to marketing (sekcja B, Mailchimp, tylko za zgodą): mail WooCommerce zostaje wyłączony.
add_filter( 'woocommerce_email_enabled_customer_review_request', '__return_false' );

/* ─────────────────────────── Akademia: mail z kuponem ─────────────────────────── */
// Kupon tworzy wtyczka guliwera-akademia (kod AKADEMIA-…, ograniczony do e-maila konta). Wysyłamy potwierdzenie z kodem.
add_action( 'woocommerce_new_coupon', function ( $id, $coupon = null ) {
	$c = $coupon instanceof WC_Coupon ? $coupon : new WC_Coupon( $id );
	if ( 0 !== stripos( $c->get_code(), 'AKADEMIA-' ) ) { return; }
	$emails = $c->get_email_restrictions();
	$user   = $emails ? get_user_by( 'email', $emails[0] ) : null;
	if ( ! $user ) { return; }
	list( $temat, $a ) = gk_ml_mail_kupon( gk_ml_dane_kuponu( $c, $user ) );
	gk_ml_wyslij( $user->user_email, $temat, $a );
}, 20, 2 );

function gk_ml_dane_kuponu( $c, $user ) {
	$proc = (float) $c->get_amount();
	$prod = array();
	foreach ( array( 68, 70, 67 ) as $pid ) {
		$p = in_array( $pid, $c->get_product_ids(), true ) ? wc_get_product( $pid ) : null;
		if ( $p ) {
			$cena   = (float) $p->get_price();
			$prod[] = array( $p->get_name(), gk_ml_kwota( wc_price( $cena ) ), gk_ml_kwota( wc_price( round( $cena * ( 100 - $proc ) / 100, 2 ) ) ) );
		}
	}
	$exp = $c->get_date_expires();
	return array(
		'kod'      => $c->get_code(),
		'procent'  => $proc,
		'wazne_do' => $exp ? $exp->getTimestamp() : time() + 7 * DAY_IN_SECONDS,
		'imie'     => $user->first_name,
		'url'      => home_url( '/koszyk/?gk_kupon=' . rawurlencode( $c->get_code() ) ),
		'produkty' => $prod,
	);
}
