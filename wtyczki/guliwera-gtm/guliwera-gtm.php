<?php
/**
 * Plugin Name: Guliwera – zgoda na cookies i Google Tag Manager
 * Description: Polski baner zgody na cookies z kategoriami (niezbędne, analityczne, marketingowe) w kolorach strony. Google Tag Manager (GTM-KK3LPNV6) uruchamia się dopiero po zgodzie, z trybem zgody Google (Consent Mode v2). Przekazuje zdarzenia e-commerce WooCommerce oraz rejestrację (sign_up) i formularz kontaktowy (generate_lead). Dodaje linki do polityki prywatności, regulaminu i ustawień cookies w stopce. Musi pozostać włączona.
 * Version: 2.1
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
define( 'GK_GTM_ID', 'GTM-KK3LPNV6' );
define( 'GK_ZGODA_COOKIE', 'gk_zgoda' );

/* ---------- 1. Tryb zgody Google + GTM tylko po zgodzie ----------
 * Cookie gk_zgoda: "v2a1m0" (a = analityczne, m = marketingowe). Stare wartości "tak"/"nie" z wersji 2.0
 * dotyczyły tylko analityki: "tak" dalej włącza analitykę, ale baner pyta ponownie o marketing. */
add_action( 'wp_head', function () {
	if ( is_admin() ) { return; }
	$id = esc_js( GK_GTM_ID );
	$c  = esc_js( GK_ZGODA_COOKIE );
	echo "<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}
gtag('consent','default',{analytics_storage:'denied',ad_storage:'denied',ad_user_data:'denied',ad_personalization:'denied',functionality_storage:'granted',security_storage:'granted',wait_for_update:500});
(function(){var v=(document.cookie.match(/(?:^|; )$c=([^;]*)/)||[])[1]||'';var z={a:false,m:false,znana:false};
if(/^v2a[01]m[01]$/.test(v)){z.a=v.charAt(3)==='1';z.m=v.charAt(5)==='1';z.znana=true;}else if(v==='tak'){z.a=true;}
window.gkZgoda=z;})();
window.gkLadujGTM=function(){var z=window.gkZgoda;var g=function(b){return b?'granted':'denied';};
gtag('consent','update',{analytics_storage:g(z.a),ad_storage:g(z.m),ad_user_data:g(z.m),ad_personalization:g(z.m)});
dataLayer.push({event:'gk_zgoda',zgoda_analityczna:z.a,zgoda_marketingowa:z.m});
if(window.gkGTM||!(z.a||z.m)){return;}window.gkGTM=1;(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s);j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','$id');};
if(window.gkZgoda.a||window.gkZgoda.m){window.gkLadujGTM();}</script>\n";
}, 1 );

/* ---------- 2. Baner z kategoriami ---------- */
add_action( 'wp_footer', function () {
	if ( is_admin() ) { return; }
	$pp = esc_url( home_url( '/polityka-prywatnosci/' ) );
	$c  = esc_js( GK_ZGODA_COOKIE );
	?>
<div id="gk-zgoda" role="dialog" aria-live="polite" aria-label="Zgoda na pliki cookies" hidden>
  <div class="gkz-in">
    <div class="gkz-t"><b>Ciasteczka, ale bez lukru 🍪</b>Używam cookies niezbędnych do działania strony i sklepu. Za Twoją zgodą włączę też cookies <b class="gkz-k">analityczne</b> (Google Analytics: co działa na stronie) i <b class="gkz-k">marketingowe</b> (Google Ads i Meta – Facebook, Instagram: mierzenie skuteczności reklam i pokazywanie reklam osobom, które odwiedziły stronę). Możesz je odrzucić, a strona i zakupy będą działać tak samo. <a href="<?php echo $pp; ?>#cookies">Polityka prywatności i cookies</a>
      <div class="gkz-opcje" hidden>
        <label><input type="checkbox" checked disabled> Niezbędne (zawsze włączone)</label>
        <label><input type="checkbox" id="gkz-a"> Analityczne</label>
        <label><input type="checkbox" id="gkz-m"> Marketingowe</label>
      </div>
    </div>
    <div class="gkz-b">
      <button type="button" class="gkz-nie" data-gkz="nie">Odrzucam wszystkie</button>
      <button type="button" class="gkz-nie gkz-ust" data-gkz="ustawienia">Ustawienia</button>
      <button type="button" class="gkz-nie gkz-zap" data-gkz="zapisz" hidden>Zapisz wybór</button>
      <button type="button" class="gkz-tak" data-gkz="tak">Akceptuję wszystkie</button>
    </div>
  </div>
</div>
<style>#gk-zgoda{position:fixed;left:16px;right:16px;bottom:16px;z-index:99999;font-family:"DM Sans",system-ui,sans-serif;color:#3A2340}#gk-zgoda[hidden],#gk-zgoda [hidden]{display:none!important}#gk-zgoda .gkz-in{max-width:960px;margin:0 auto;background:#fff;border-radius:24px;box-shadow:0 18px 50px rgba(58,35,64,.25);padding:20px 24px;display:flex;gap:20px;align-items:center;border:2px solid #FFD9E4}#gk-zgoda .gkz-t{margin:0;font-size:15px;line-height:1.5}#gk-zgoda .gkz-t>b:first-child{display:block;font-family:"Fraunces",Georgia,serif;font-size:19px;margin-bottom:4px}#gk-zgoda .gkz-k{font-weight:700}#gk-zgoda a{color:#E8512A;font-weight:700}#gk-zgoda .gkz-opcje{display:flex;flex-wrap:wrap;gap:8px 18px;margin-top:10px}#gk-zgoda .gkz-opcje label{display:flex;gap:6px;align-items:center;font-weight:500}#gk-zgoda .gkz-opcje input{width:18px;height:18px;accent-color:#FF6B3D}#gk-zgoda .gkz-b{display:flex;flex-direction:column;gap:8px;flex:none;min-width:210px}#gk-zgoda button{font:700 15px "DM Sans",system-ui,sans-serif;border-radius:999px;padding:11px 20px;cursor:pointer;border:2.5px solid #FF6B3D;line-height:1.2}#gk-zgoda .gkz-tak{background:#FF6B3D;color:#fff}#gk-zgoda .gkz-tak:hover{background:#E8512A;border-color:#E8512A}#gk-zgoda .gkz-nie{background:#fff;color:#E8512A}#gk-zgoda .gkz-nie:hover{background:#FFF6EE}@media(max-width:700px){#gk-zgoda{left:10px;right:10px;bottom:10px}#gk-zgoda .gkz-in{flex-direction:column;align-items:stretch;padding:18px;max-height:85vh;overflow:auto}}</style>
<script>(function(){var b=document.getElementById('gk-zgoda');if(!b){return;}
var op=b.querySelector('.gkz-opcje'),ia=document.getElementById('gkz-a'),im=document.getElementById('gkz-m'),ust=b.querySelector('.gkz-ust'),zap=b.querySelector('.gkz-zap');
function zapisz(a,m){var z=window.gkZgoda||{},bylo=!!window.gkGTM,cofnieto=(z.a&&!a)||(z.m&&!m);var d=new Date();d.setTime(d.getTime()+180*864e5);document.cookie='<?php echo $c; ?>=v2a'+(a?1:0)+'m'+(m?1:0)+';expires='+d.toUTCString()+';path=/;SameSite=Lax'+(location.protocol==='https:'?';Secure':'');window.gkZgoda={a:a,m:m,znana:true};b.hidden=true;
if(bylo&&cofnieto){location.reload();return;}window.gkLadujGTM&&window.gkLadujGTM();}
function pokaz(szczegoly){var z=window.gkZgoda||{};ia.checked=!!z.a;im.checked=!!z.m;op.hidden=!szczegoly;ust.hidden=!!szczegoly;zap.hidden=!szczegoly;b.hidden=false;}
if(!window.gkZgoda||!window.gkZgoda.znana){pokaz(false);}
b.addEventListener('click',function(e){var v=e.target&&e.target.getAttribute('data-gkz');if(!v){return;}
if(v==='tak'){zapisz(true,true);}else if(v==='nie'){zapisz(false,false);}else if(v==='ustawienia'){pokaz(true);}else if(v==='zapisz'){zapisz(ia.checked,im.checked);}});
document.addEventListener('click',function(e){var a=e.target&&e.target.closest&&e.target.closest('a[href="#ustawienia-cookies"]');if(a){e.preventDefault();pokaz(true);}});})();</script>
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
/* Zahashowany e-mail (SHA-256, małe litery) do ulepszonych konwersji Google i zaawansowanego dopasowania Meta.
 * Tagi w GTM wysyłają go tylko przy zgodzie marketingowej (ad_user_data). */
function gk_gtm_user_data( $email ) {
	$email = strtolower( trim( (string) $email ) );
	return is_email( $email ) ? array( 'sha256_email_address' => hash( 'sha256', $email ) ) : null;
}
function gk_gtm_push( $event, $ecommerce, $extra = array() ) {
	$data = array_merge( array( 'event' => $event ), $extra );
	if ( null !== $ecommerce ) { $data['ecommerce'] = $ecommerce; }
	echo '<script>window.dataLayer=window.dataLayer||[];' . ( null !== $ecommerce ? 'dataLayer.push({ecommerce:null});' : '' ) . 'dataLayer.push(' . wp_json_encode( $data ) . ');</script>' . "\n";
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

/* purchase: tylko opłacone zamówienie, raz. Gdy BLIK/P24 wróci jeszcze jako „oczekujące”, zdarzenie
 * wyśle się przy następnym obejrzeniu zamówienia w „Moje konto”. */
function gk_gtm_purchase( $order_id ) {
	$order = wc_get_order( $order_id );
	if ( ! $order || ! $order->is_paid() || $order->get_meta( '_gk_gtm_tracked' ) ) { return; }
	$items = array();
	foreach ( $order->get_items() as $it ) {
		$items[] = array( 'item_id' => (string) $it->get_product_id(), 'item_name' => $it->get_name(), 'price' => (float) $order->get_item_total( $it, true ), 'quantity' => (int) $it->get_quantity() );
	}
	$extra = array( 'event_id' => 'purchase_' . $order->get_id() );
	$ud = gk_gtm_user_data( $order->get_billing_email() );
	if ( $ud ) { $extra['user_data'] = $ud; }
	gk_gtm_push( 'purchase', array( 'transaction_id' => (string) $order->get_order_number(), 'value' => (float) $order->get_total(), 'tax' => (float) $order->get_total_tax(), 'currency' => $order->get_currency(), 'items' => $items ), $extra );
	$order->update_meta_data( '_gk_gtm_tracked', 1 );
	$order->save();
}
add_action( 'woocommerce_thankyou', 'gk_gtm_purchase' );
add_action( 'woocommerce_view_order', 'gk_gtm_purchase' );

/* sign_up: nowe konto (rejestracja w „Moje konto” = wejście do Akademii). Wysyłane na pierwszej stronie po rejestracji. */
add_action( 'user_register', function ( $user_id ) {
	if ( is_admin() && ! wp_doing_ajax() ) { return; } // konta dodane ręcznie w panelu nie są konwersją
	update_user_meta( $user_id, '_gk_gtm_signup', 1 );
} );
add_action( 'wp_footer', function () {
	if ( ! is_user_logged_in() ) { return; }
	$uid = get_current_user_id();
	if ( ! get_user_meta( $uid, '_gk_gtm_signup', true ) ) { return; }
	delete_user_meta( $uid, '_gk_gtm_signup' );
	$extra = array( 'method' => 'konto', 'event_id' => 'signup_' . $uid );
	$ud = gk_gtm_user_data( wp_get_current_user()->user_email );
	if ( $ud ) { $extra['user_data'] = $ud; }
	gk_gtm_push( 'sign_up', null, $extra );
}, 6 );

/* generate_lead: wysłany formularz kontaktowy Jetpack (strona po wysłaniu ma ?contact-form-sent=…). */
add_action( 'wp_footer', function () {
	if ( empty( $_GET['contact-form-sent'] ) ) { return; }
	gk_gtm_push( 'generate_lead', null, array( 'lead_zrodlo' => 'formularz_kontakt', 'event_id' => 'lead_' . absint( $_GET['contact-form-sent'] ) ) );
}, 7 );

/* Po aktualizacji do 2.1: wyczyść cache stron, żeby wszyscy dostali nowy baner. */
add_action( 'init', function () {
	if ( '2.1' === get_option( 'gk_gtm_ver2' ) ) { return; }
	update_option( 'gk_gtm_ver2', '2.1' );
	if ( function_exists( 'w3tc_flush_all' ) ) { w3tc_flush_all(); }
}, 6 );
