<?php
/**
 * Plugin Name: Guliwera – zgoda na cookies i Google Tag Manager
 * Description: Polski baner zgody na cookies (Akceptuję / Odrzucam) w kolorach strony. Google Tag Manager (GTM-KK3LPNV6) uruchamia się dopiero po zgodzie, z trybem zgody Google (Consent Mode v2). Przekazuje zdarzenia e-commerce WooCommerce. Dodaje linki do polityki prywatności, regulaminu i ustawień cookies w stopce. Musi pozostać włączona.
 * Version: 2.0.1
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
define( 'GK_GTM_ID', 'GTM-KK3LPNV6' );
define( 'GK_ZGODA_COOKIE', 'gk_zgoda' );

/* ---------- 1. Tryb zgody Google + GTM tylko po zgodzie ---------- */
add_action( 'wp_head', function () {
	if ( is_admin() ) { return; }
	$id = esc_js( GK_GTM_ID );
	$c  = esc_js( GK_ZGODA_COOKIE );
	echo "<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}
gtag('consent','default',{analytics_storage:'denied',ad_storage:'denied',ad_user_data:'denied',ad_personalization:'denied',functionality_storage:'granted',security_storage:'granted',wait_for_update:500});
window.gkZgoda=(document.cookie.match(/(?:^|; )$c=([^;]*)/)||[])[1]||'';
window.gkLadujGTM=function(){if(window.gkGTM){return;}window.gkGTM=1;gtag('consent','update',{analytics_storage:'granted'});(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s);j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','$id');};
if(window.gkZgoda==='tak'){window.gkLadujGTM();}</script>\n";
}, 1 );

/* ---------- 2. Baner ---------- */
add_action( 'wp_footer', function () {
	if ( is_admin() ) { return; }
	$pp = esc_url( home_url( '/polityka-prywatnosci/' ) );
	$c  = esc_js( GK_ZGODA_COOKIE );
	?>
<div id="gk-zgoda" role="dialog" aria-live="polite" aria-label="Zgoda na pliki cookies" hidden>
  <div class="gkz-in">
    <p class="gkz-t"><b>Ciasteczka, ale bez lukru 🍪</b>Używam plików cookies niezbędnych do działania strony i sklepu. Za Twoją zgodą włączę też cookies analityczne (Google Analytics), które pomagają mi ulepszać stronę. Możesz je odrzucić, a strona i zakupy będą działać tak samo. <a href="<?php echo $pp; ?>#cookies">Polityka prywatności i cookies</a></p>
    <div class="gkz-b"><button type="button" class="gkz-nie" data-gkz="nie">Odrzucam</button><button type="button" class="gkz-tak" data-gkz="tak">Akceptuję</button></div>
  </div>
</div>
<style>#gk-zgoda{position:fixed;left:16px;right:16px;bottom:16px;z-index:99999;font-family:"DM Sans",system-ui,sans-serif;color:#3A2340}#gk-zgoda[hidden]{display:none}#gk-zgoda .gkz-in{max-width:880px;margin:0 auto;background:#fff;border-radius:24px;box-shadow:0 18px 50px rgba(58,35,64,.25);padding:20px 24px;display:flex;gap:20px;align-items:center;border:2px solid #FFD9E4}#gk-zgoda .gkz-t{margin:0;font-size:15px;line-height:1.5}#gk-zgoda .gkz-t b{display:block;font-family:"Fraunces",Georgia,serif;font-size:19px;margin-bottom:4px}#gk-zgoda a{color:#E8512A;font-weight:700}#gk-zgoda .gkz-b{display:flex;gap:10px;flex:none}#gk-zgoda button{font:700 15px "DM Sans",system-ui,sans-serif;border-radius:999px;padding:12px 22px;cursor:pointer;border:2.5px solid #FF6B3D;line-height:1.2}#gk-zgoda .gkz-tak{background:#FF6B3D;color:#fff}#gk-zgoda .gkz-tak:hover{background:#E8512A;border-color:#E8512A}#gk-zgoda .gkz-nie{background:#fff;color:#E8512A}#gk-zgoda .gkz-nie:hover{background:#FFF6EE}@media(max-width:700px){#gk-zgoda{left:10px;right:10px;bottom:10px}#gk-zgoda .gkz-in{flex-direction:column;align-items:stretch;padding:18px}#gk-zgoda .gkz-b{justify-content:stretch}#gk-zgoda button{flex:1}}</style>
<script>(function(){var b=document.getElementById('gk-zgoda');if(!b){return;}
function zapisz(v){var d=new Date();d.setTime(d.getTime()+180*864e5);document.cookie='<?php echo $c; ?>='+v+';expires='+d.toUTCString()+';path=/;SameSite=Lax'+(location.protocol==='https:'?';Secure':'');window.gkZgoda=v;}
function pokaz(){b.hidden=false;}
if(!window.gkZgoda){pokaz();}
b.addEventListener('click',function(e){var v=e.target&&e.target.getAttribute('data-gkz');if(!v){return;}zapisz(v);b.hidden=true;if(v==='tak'){window.gkLadujGTM&&window.gkLadujGTM();}else if(window.gkGTM){location.reload();}});
document.addEventListener('click',function(e){var a=e.target&&e.target.closest&&e.target.closest('a[href="#ustawienia-cookies"]');if(a){e.preventDefault();pokaz();}});})();</script>
	<?php
}, 99 );

/* ---------- 3. Linki w stopce wszystkich podstron ---------- */
add_action( 'template_redirect', function () {
	if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || is_feed() ) { return; }
	ob_start( function ( $html ) {
		if ( false === strpos( $html, 'class="stopka-dol"' ) ) { return $html; }
		$pp = esc_url( home_url( '/polityka-prywatnosci/' ) );
		$rg = esc_url( home_url( '/regulamin/' ) );
		$links = '<span><a href="' . $pp . '">Polityka prywatności i cookies</a> · <a href="' . $rg . '">Regulamin sklepu</a> · <a href="#ustawienia-cookies">Ustawienia cookies</a></span>';
		// Strony, które mają już linki do polityki: dopisz tylko „Ustawienia cookies”.
		if ( false !== strpos( $html, '>Regulamin sklepu</a></span>' ) ) {
			return str_replace( '>Regulamin sklepu</a></span>', '>Regulamin sklepu</a> · <a href="#ustawienia-cookies">Ustawienia cookies</a></span>', $html );
		}
		return preg_replace( '#(<div class="stopka-dol">.*?)<span>guliwera4\.pl</span>#s', '$1' . $links, $html, 1 );
	} );
}, 0 );

/* ---------- 4. Jednorazowe ustawienia (wersja 2.0) ---------- */
add_action( 'init', function () {
	if ( '2.0' === get_option( 'gk_gtm_ver' ) ) { return; }
	update_option( 'gk_gtm_ver', '2.0' );
	update_option( 'users_can_register', 0 );
	update_option( 'wp_page_for_privacy_policy', 3 );
	update_option( 'woocommerce_terms_page_id', 66 );
	// Polityka prywatności: nazwa cookie zgody z nowego banera.
	global $wpdb;
	$c = $wpdb->get_var( "SELECT post_content FROM {$wpdb->posts} WHERE ID = 3" );
	if ( $c && false !== strpos( $c, 'cookie_notice_accepted' ) ) {
		$c = str_replace( 'cookie_notice_accepted', 'gk_zgoda', $c );
		$c = str_replace( 'Decyzję zmienisz w każdej chwili, usuwając cookies w przeglądarce. Wtedy baner pojawi się ponownie.', 'Decyzję zmienisz w każdej chwili, klikając „Ustawienia cookies” w stopce strony.', $c );
		$wpdb->update( $wpdb->posts, array( 'post_content' => $c ), array( 'ID' => 3 ) );
		clean_post_cache( 3 );
	}
	// Stary baner Cookie Notice nie jest już potrzebny.
	if ( ! function_exists( 'deactivate_plugins' ) ) { require_once ABSPATH . 'wp-admin/includes/plugin.php'; }
	if ( is_plugin_active( 'cookie-notice/cookie-notice.php' ) ) { deactivate_plugins( 'cookie-notice/cookie-notice.php', true ); }
	if ( function_exists( 'w3tc_flush_all' ) ) { w3tc_flush_all(); }
}, 5 );

/* ---------- 5. Zdarzenia e-commerce (trafiają do GTM tylko po zgodzie) ---------- */
function gk_gtm_item( $product, $qty = 1 ) {
	return array(
		'item_id'   => (string) $product->get_id(),
		'item_name' => $product->get_name(),
		'price'     => (float) wc_get_price_to_display( $product ),
		'quantity'  => (int) $qty,
	);
}
function gk_gtm_push( $event, $ecommerce ) {
	echo '<script>window.dataLayer=window.dataLayer||[];dataLayer.push({ecommerce:null});dataLayer.push(' . wp_json_encode( array( 'event' => $event, 'ecommerce' => $ecommerce ) ) . ');</script>' . "\n";
}
add_action( 'wp_footer', function () {
	if ( ! function_exists( 'is_product' ) ) { return; }
	if ( is_product() ) {
		$p = wc_get_product( get_the_ID() );
		if ( $p ) { gk_gtm_push( 'view_item', array( 'currency' => get_woocommerce_currency(), 'value' => (float) wc_get_price_to_display( $p ), 'items' => array( gk_gtm_item( $p ) ) ) ); }
	}
	if ( is_checkout() && ! is_order_received_page() && WC()->cart ) {
		$items = array();
		foreach ( WC()->cart->get_cart() as $ci ) { if ( ! empty( $ci['data'] ) ) { $items[] = gk_gtm_item( $ci['data'], $ci['quantity'] ); } }
		if ( $items ) { gk_gtm_push( 'begin_checkout', array( 'currency' => get_woocommerce_currency(), 'value' => (float) WC()->cart->get_total( 'edit' ), 'items' => $items ) ); }
	}
} );
add_action( 'woocommerce_add_to_cart', function ( $key, $product_id, $qty ) {
	if ( WC()->session ) {
		$q = (array) WC()->session->get( 'gk_gtm_atc', array() );
		$q[] = array( $product_id, $qty );
		WC()->session->set( 'gk_gtm_atc', $q );
	}
}, 10, 3 );
add_action( 'wp_footer', function () {
	if ( ! function_exists( 'WC' ) || ! WC()->session ) { return; }
	$q = (array) WC()->session->get( 'gk_gtm_atc', array() );
	if ( ! $q ) { return; }
	WC()->session->set( 'gk_gtm_atc', array() );
	$items = array(); $value = 0;
	foreach ( $q as $row ) { $p = wc_get_product( $row[0] ); if ( $p ) { $it = gk_gtm_item( $p, $row[1] ); $items[] = $it; $value += $it['price'] * $it['quantity']; } }
	if ( $items ) { gk_gtm_push( 'add_to_cart', array( 'currency' => get_woocommerce_currency(), 'value' => $value, 'items' => $items ) ); }
}, 5 );
add_action( 'woocommerce_thankyou', function ( $order_id ) {
	$order = wc_get_order( $order_id );
	if ( ! $order || $order->get_meta( '_gk_gtm_tracked' ) ) { return; }
	$items = array();
	foreach ( $order->get_items() as $it ) {
		$items[] = array( 'item_id' => (string) $it->get_product_id(), 'item_name' => $it->get_name(), 'price' => (float) $order->get_item_total( $it, true ), 'quantity' => (int) $it->get_quantity() );
	}
	gk_gtm_push( 'purchase', array( 'transaction_id' => (string) $order->get_order_number(), 'value' => (float) $order->get_total(), 'tax' => (float) $order->get_total_tax(), 'currency' => $order->get_currency(), 'items' => $items ) );
	$order->update_meta_data( '_gk_gtm_tracked', 1 );
	$order->save();
} );
