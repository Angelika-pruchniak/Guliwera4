<?php
/**
 * Plugin Name: Guliwera – struktura strony
 * Description: Struktura guliwera4.pl według strategii (drabina produktów): jedno menu na wszystkich podstronach (Start, Materiały, Kurs, Akademia, O mnie, Kontakt), przycisk „Zacznij za darmo”, Lokalizacja i Usługi B2B połączone z Kontaktem (przekierowania 301). Zostaw włączoną.
 * Version: 1.7
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* /lokalizacja/ -> /kontakt/#lokalizacja (301) */
add_action( 'template_redirect', function () {
	$path = trim( (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH ), '/' );
	if ( 'lokalizacja' === $path || is_page( 52 ) ) {
		wp_safe_redirect( home_url( '/kontakt/#lokalizacja' ), 301 );
		exit;
	}
	if ( 'uslugi' === $path || is_page( 32 ) ) {
		wp_safe_redirect( home_url( '/kontakt/#dla-firm' ), 301 );
		exit;
	}
}, 1 );

/* Menu i linki na wszystkich podstronach (także sklep i strony prawne). */
add_action( 'template_redirect', function () {
	if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || is_feed() ) { return; }
	ob_start( 'gk_strona_filtr' );
}, 2 );

function gk_strona_menu() {
	$path = '/' . trim( (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/', PHP_URL_PATH ), '/' ) . '/';
	if ( '//' === $path ) { $path = '/'; }
	$on = '/';
	if ( function_exists( 'is_product' ) && ( is_product() || is_cart() || is_checkout() ) ) { $on = '/materialy/'; }
	foreach ( array( '/materialy/', '/kurs-strategia/', '/aplikacja/', '/blog/', '/o-mnie/', '/kontakt/' ) as $p ) { if ( 0 === strpos( $path, $p ) ) { $on = $p; } }
	if ( gk_blog_widok() ) { $on = '/blog/'; }
	if ( '/' !== $path && '/' === $on && ! ( function_exists( 'is_product' ) && is_product() ) ) { $on = ''; }
	$items = array( '/' => 'Start', '/materialy/' => 'Materiały', '/kurs-strategia/' => 'Kurs', '/aplikacja/' => 'Akademia' );
	if ( gk_blog_jest() ) { $items['/blog/'] = 'Blog'; }
	$items += array( '/o-mnie/' => 'O mnie', '/kontakt/' => 'Kontakt' );
	$h = '<ul class="menu">';
	foreach ( $items as $p => $t ) {
		$h .= "\n    <li><a" . ( $p === $on ? ' class="on"' : '' ) . ' href="' . esc_url( home_url( $p ) ) . '">' . esc_html( $t ) . '</a></li>';
	}
	return $h . "\n  </ul>";
}

function gk_strona_filtr( $html ) {
	if ( false === strpos( $html, 'class="nav"' ) && false === strpos( $html, '/lokalizacja/' ) && false === strpos( $html, '/uslugi/' ) ) { return $html; }
	$home = untrailingslashit( home_url() );
	// Jedno menu i przycisk w nagłówku.
	$html = preg_replace_callback( '#(<header class="nav">.*?</header>)#s', function ( $m ) use ( $home ) {
		$h = preg_replace( '#<ul class="menu">.*?</ul>#s', gk_strona_menu(), $m[1], 1 );
		$h = preg_replace( '#<a class="btn btn-mala" href="[^"]*">[^<]*</a>#', '<a class="btn btn-mala" href="' . $home . '/aplikacja/">Zacznij za darmo</a>', $h, 1 );
		return $h;
	}, $html, 1 );
	// Pasek nad nagłówkiem: CTA nr 1.
	$html = preg_replace( '#<div class="pasek">.*?</div>#s', '<div class="pasek">Zacznij za darmo: 5 minut dziennie strategii w Akademii Guliwera. <a href="' . $home . '/aplikacja/">Sprawdź →</a></div>', $html, 1 );
	// Lokalizacja i Usługi B2B są teraz w Kontakcie.
	$html = str_replace( array( '>Usługi i cennik</a>', '>Usługi B2B</a>', '>usługi B2B</a>' ), array( '>Dla firm</a>', '>Dla firm</a>', '>Dla firm</a>' ), $html );
	$html = str_replace( array( $home . '/lokalizacja/', $home . '/uslugi/' ), array( $home . '/kontakt/#lokalizacja', $home . '/kontakt/#dla-firm' ), $html );
	return $html;
}

/* Jednorazowe zmiany treści (wersja 1.1). */
add_action( 'init', function () {
	$v = get_option( 'gk_strona_ver' );
	if ( '1.1' === $v ) { return; }
	foreach ( array( 52, 32 ) as $id ) {
		if ( 'publish' === get_post_status( $id ) ) { wp_update_post( array( 'ID' => $id, 'post_status' => 'draft' ) ); }
	}
	// Kurs: kurs wideo 697 zł, przedsprzedaż (strategia, drabina produktów).
	global $wpdb;
	$c = (string) $wpdb->get_var( "SELECT post_content FROM {$wpdb->posts} WHERE ID = 157" );
	if ( $c && false !== strpos( $c, '1 990 zł' ) ) {
		$z = array(
			'PIERWSZA EDYCJA · LUTY 2027' => 'PREMIERA · KWIECIEŃ 2027',
			'Kurs strategii firmy online w małej grupie. Przez 6 tygodni krok po kroku przechodzisz od diagnozy firmy do strategii na jednej stronie i planu na 90 dni. Pracujesz na swojej firmie, nie na przykładach z podręcznika.' => 'Kurs wideo, który krok po kroku przeprowadzi Cię od diagnozy firmy do strategii na jednej stronie i planu na 90 dni. Oglądasz w swoim tempie, pracujesz na swojej firmie, a na dwóch sobotnich sesjach Q&amp;A na żywo dopytujesz o swoje przypadki.',
			'<span>6 spotkań na żywo po 90 min</span><span>mała grupa do 20 osób</span><span>online</span><span>nagrania i materiały</span>' => '<span>6 modułów wideo</span><span>w swoim tempie</span><span>2 sobotnie sesje Q&amp;A na żywo</span><span>pakiet PDF w cenie</span>',
			'>Zapisz się na listę oczekujących</a>' => '>Zapisz się na przedsprzedaż</a>',
			'<li>6 spotkań na żywo online po 90 minut, raz w tygodniu</li><li>nagrania spotkań i materiały do pracy</li><li>zadania na Twojej firmie i informacja zwrotna</li>' => '<li>6 modułów wideo: jeden na tydzień albo w swoim tempie</li><li>2 sobotnie sesje Q&amp;A na żywo online</li><li>zadania na Twojej firmie i szablony do pracy</li>',
			'<div class="cena">1 990 zł</div>' => '<div class="cena">697 zł</div>',
			'Dla osób z listy oczekujących cena na start: <b style="color:#FFC84A">1 590 zł</b>. Termin i link do zapisu wyślemy najpierw osobom z listy.' => 'Premiera w kwietniu 2027. Link do przedsprzedaży dostaną najpierw osoby z listy oczekujących.',
			'Firmy mogą zgłosić kilka osób i opłacić kurs na fakturę. Zespoły kierownicze: zobacz' => 'Firmy: licencja kursu i Akademii dla zespołu młodych managerów, zobacz',
			'>Zapisz się na listę</a>' => '>Zapisz się na przedsprzedaż</a>',
			'Zostaw imię i e-mail. Napiszę, gdy otworzę zapisy na pierwszą edycję, z ceną na start dla osób z listy.' => 'Zostaw imię i e-mail. Napiszę, gdy ruszy przedsprzedaż kursu. Osoby z listy dostaną link jako pierwsze.',
			'Warsztaty strategiczne i strateg na abonament dla firm, kurs strategii, materiały do pobrania i Akademia dla młodych managerów.' => 'Strategia i liczby dla małych firm i młodych managerów: darmowa Akademia, gotowe narzędzia i kurs wideo.',
			'[contact-form subject="Lista oczekujących: kurs Strategia w 6 tygodni"]' => '[contact-form subject="Przedsprzedaż: kurs wideo Strategia w 6 tygodni"]',
			'Chcę dostać informację o zapisach na kurs i cenie na start. Zgoda na kontakt mailowy w tej sprawie.' => 'Chcę dostać informację o przedsprzedaży kursu. Zgoda na kontakt mailowy w tej sprawie.',
		);
		$c = str_replace( array_keys( $z ), array_values( $z ), $c );
		$wpdb->update( $wpdb->posts, array( 'post_content' => $c ), array( 'ID' => 157 ) );
		clean_post_cache( 157 );
	}
	update_option( 'gk_strona_ver', '1.1' );
	if ( function_exists( 'w3tc_flush_all' ) ) { w3tc_flush_all(); }
}, 20 );

/* Wersja 1.2: bez nazw firm (EPAM, Grupa Azoty, Aruba IT, CosmoEYE) – decyzja Angeliki 8.11. */
add_action( 'init', function () {
	if ( '1.2' === get_option( 'gk_strona_ver2' ) ) { return; }
	global $wpdb;
	$z = array(
		'<div class="dowody"><h3>PONAD 10 LAT WPROWADZANIA PRODUKTÓW NA RYNEK, M.IN. W</h3><div class="marki"><span>EPAM</span><span>Grupa Azoty</span><span>Aruba IT</span><span>CosmoEYE</span></div></div>' => '<div class="dowody"><h3>PONAD 10 LAT WPROWADZANIA NOWYCH PRODUKTÓW NA RYNEK W KORPORACJACH I FIRMACH TECHNOLOGICZNYCH</h3></div>',
		'dla klientów takich jak Aruba IT, i prowadzę wprowadzanie nowych produktów na rynek dla organizacji wielkości Grupy Azoty i EPAM:' => 'dla klientów z branży IT, i prowadzę wprowadzanie nowych produktów na rynek w dużych organizacjach przemysłowych i technologicznych:',
		'Poza korporacyjnym IT i przemysłem chemicznym prowadzę' => 'Poza korporacyjnym IT i przemysłem prowadzę',
	);
	foreach ( array( 10, 6 ) as $id ) {
		$c = (string) $wpdb->get_var( $wpdb->prepare( "SELECT post_content FROM {$wpdb->posts} WHERE ID = %d", $id ) );
		$n = str_replace( array_keys( $z ), array_values( $z ), $c );
		if ( $n !== $c ) { $wpdb->update( $wpdb->posts, array( 'post_content' => $n ), array( 'ID' => $id ) ); clean_post_cache( $id ); }
	}
	update_option( 'gk_strona_ver2', '1.2' );
	if ( function_exists( 'w3tc_flush_all' ) ) { w3tc_flush_all(); }
}, 21 );

/* Wersja 1.3: portret w jasnym garniturze (media 171) zamiast różowego (media 94) na wszystkich stronach. */
add_action( 'init', function () {
	if ( '1.3' === get_option( 'gk_strona_ver3' ) ) { return; }
	global $wpdb;
	$z = array(
		'angelika-pruchniak-gaszczyk-portret-820x1024.jpg' => 'angelika-pruchniak-gaszczyk-jasny-garnitur-819x1024.jpg',
		'angelika-pruchniak-gaszczyk-portret-768x959.jpg'  => 'angelika-pruchniak-gaszczyk-jasny-garnitur-768x960.jpg',
		'angelika-pruchniak-gaszczyk-portret-600x750.jpg'  => 'angelika-pruchniak-gaszczyk-jasny-garnitur-600x750.jpg',
		'angelika-pruchniak-gaszczyk-portret.jpg'          => 'angelika-pruchniak-gaszczyk-jasny-garnitur.jpg',
		'w różowym garniturze'                             => 'w jasnym garniturze',
	);
	$ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_type IN ('page','post','product') AND post_content LIKE '%angelika-pruchniak-gaszczyk-portret%'" );
	foreach ( $ids as $id ) {
		$c = (string) $wpdb->get_var( $wpdb->prepare( "SELECT post_content FROM {$wpdb->posts} WHERE ID = %d", $id ) );
		$n = str_replace( array_keys( $z ), array_values( $z ), $c );
		if ( $n !== $c ) { $wpdb->update( $wpdb->posts, array( 'post_content' => $n ), array( 'ID' => $id ) ); clean_post_cache( $id ); }
	}
	update_option( 'gk_strona_ver3', '1.3' );
	if ( function_exists( 'w3tc_flush_all' ) ) { w3tc_flush_all(); }
}, 22 );

/* ---------- Wersja 1.4: BLOG (lista wpisów /blog/, wpis, kategorie) w wyglądzie Guliwera 4 ---------- */
function gk_blog_jest() {
	$c = wp_count_posts( 'post' );
	return $c && (int) $c->publish > 0;
}
function gk_blog_widok() {
	return is_home() || is_singular( 'post' ) || is_category() || is_tag() || ( is_archive() && 'post' === get_post_type() && ! ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) );
}
add_action( 'init', function () {
	if ( '1.4' === get_option( 'gk_strona_ver4' ) ) { return; }
	$blog = get_page_by_path( 'blog' );
	$id   = $blog ? $blog->ID : wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Blog', 'post_name' => 'blog', 'post_content' => '' ) );
	if ( $id && ! is_wp_error( $id ) ) {
		if ( 'page' !== get_option( 'show_on_front' ) ) { update_option( 'show_on_front', 'page' ); update_option( 'page_on_front', 10 ); }
		update_option( 'page_for_posts', (int) $id );
		update_option( 'posts_per_page', 9 );
		flush_rewrite_rules( false );
	}
	update_option( 'gk_strona_ver4', '1.4' );
	if ( function_exists( 'w3tc_flush_all' ) ) { w3tc_flush_all(); }
}, 23 );
add_filter( 'template_include', function ( $t ) {
	if ( gk_blog_widok() && function_exists( 'gk_ss_chrome' ) ) { return __DIR__ . '/blog.php'; }
	return $t;
}, 98 );
add_filter( 'excerpt_length', function ( $n ) { return gk_blog_widok() ? 26 : $n; } );
add_filter( 'excerpt_more', function ( $m ) { return gk_blog_widok() ? '…' : $m; } );

function gk_blog_css() {
	return '<style id="gk-blog">
.gk .bl-hero{padding:clamp(36px,6vw,70px) 0 clamp(20px,3vw,34px);position:relative;overflow:hidden}.gk .bl-hero::before{content:"";position:absolute;width:420px;height:420px;border-radius:50%;background:var(--liliowy-jasny);right:-160px;top:-170px}.gk .bl-hero .wrap{position:relative;z-index:2}
.gk .bl-hero h1{font-size:clamp(36px,5.4vw,62px);margin-top:8px;max-width:18ch}.gk .bl-lead{font-size:clamp(17px,1.8vw,20px);color:var(--atrament-60);max-width:56ch;margin-top:16px}
.gk .bl-kat{display:flex;flex-wrap:wrap;gap:8px;margin-top:22px;padding:0;list-style:none}.gk .bl-kat a{display:inline-block;padding:8px 16px;border-radius:999px;background:#fff;text-decoration:none;font-weight:700;font-size:14.5px;color:var(--atrament)}.gk .bl-kat a.on,.gk .bl-kat a:hover{background:var(--granat);color:#fff}
.gk .bl-siatka{display:grid;grid-template-columns:repeat(3,1fr);gap:clamp(18px,2.6vw,28px)}@media(max-width:960px){.gk .bl-siatka{grid-template-columns:1fr 1fr}}@media(max-width:620px){.gk .bl-siatka{grid-template-columns:1fr}}
.gk .bl-karta{background:#fff;border-radius:24px;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 14px 36px rgba(58,35,64,.07);text-decoration:none;color:var(--atrament);transition:transform .15s}.gk .bl-karta:hover{transform:translateY(-4px)}
.gk .bl-karta .fot{aspect-ratio:4/3;background:linear-gradient(160deg,var(--brzoskwinia),var(--roz-jasny) 60%,var(--liliowy-jasny));overflow:hidden}.gk .bl-karta .fot img{width:100%;height:100%;object-fit:cover;display:block}
.gk .bl-karta .tr{padding:20px 22px 24px;display:flex;flex-direction:column;gap:8px;flex:1}.gk .bl-karta .kat{font-size:12.5px;font-weight:700;letter-spacing:.1em;color:var(--koral-ciemny);text-transform:uppercase}.gk .bl-karta h2{font-size:clamp(20px,2vw,24px);line-height:1.15}.gk .bl-karta p{font-size:15.5px;color:var(--atrament-60);margin:0}.gk .bl-karta .dt{font-size:13.5px;color:var(--atrament-60);margin-top:auto}
.gk .bl-pag{display:flex;justify-content:center;gap:8px;margin-top:40px;flex-wrap:wrap}.gk .bl-pag a,.gk .bl-pag span{padding:9px 15px;border-radius:999px;background:#fff;text-decoration:none;font-weight:700}.gk .bl-pag .current{background:var(--koral);color:#fff}
.gk .bl-pusto{background:#fff;border-radius:24px;padding:40px;text-align:center}
.gk .wpis{max-width:760px;margin:0 auto}.gk .wpis-meta{font-size:14.5px;color:var(--atrament-60);margin-top:14px}.gk .wpis-meta a{color:var(--koral-ciemny);font-weight:700;text-decoration:none}
.gk .wpis h1{font-size:clamp(34px,5vw,56px);margin-top:10px}.gk .wpis-fot{margin:28px 0 0;border-radius:24px;overflow:hidden}.gk .wpis-fot img{width:100%;height:auto;display:block}
.gk .wpis-tresc{font-size:18.5px;line-height:1.72;margin-top:30px}.gk .wpis-tresc>*{max-width:720px}.gk .wpis-tresc h2{font-size:clamp(26px,3vw,34px);margin:44px 0 14px}.gk .wpis-tresc h3{font-size:clamp(21px,2.3vw,25px);margin:32px 0 10px}
.gk .wpis-tresc p{margin:0 0 1.15em}.gk .wpis-tresc ul,.gk .wpis-tresc ol{padding-left:24px;margin:0 0 1.2em}.gk .wpis-tresc li{margin-bottom:6px}.gk .wpis-tresc a{color:var(--koral-ciemny);font-weight:600}.gk .wpis-tresc img{max-width:100%;height:auto;border-radius:18px}
.gk .wpis-tresc blockquote{margin:28px 0;padding:18px 24px;border-left:4px solid var(--roz);background:#fff;border-radius:0 18px 18px 0;font-family:"Fraunces",serif;font-size:21px}
.gk .wpis-tresc figure{margin:26px 0}.gk .wpis-tresc figcaption{font-size:14px;color:var(--atrament-60);margin-top:8px;text-align:center}
.gk .wpis-tresc table{width:100%;border-collapse:collapse;font-size:16px;margin:18px 0}.gk .wpis-tresc th,.gk .wpis-tresc td{padding:10px 12px;border-bottom:1px solid rgba(58,35,64,.1);text-align:left}.gk .wpis-tresc th{background:#fff}
.gk .g4-skrot{background:var(--krem);border:2px solid var(--liliowy);border-radius:16px;padding:18px 22px;margin:0 0 28px}.gk .g4-skrot>:last-child{margin-bottom:0}.gk .g4-skrot strong:first-child{display:block;font-family:"Fraunces",serif;font-size:20px;margin-bottom:6px}
.gk .g4-odrecznie{font-family:"Caveat",cursive;color:var(--koral);font-size:1.6rem;line-height:1.25}
.gk .g4-cta,.gk a.g4-cta,.gk .g4-cta a,.gk .wp-block-button.g4-cta .wp-block-button__link{display:inline-block;background:var(--koral);color:#fff !important;border-radius:999px;padding:14px 26px;font-weight:700;text-decoration:none;font-family:"DM Sans",sans-serif;border:0}.gk .g4-cta:hover,.gk a.g4-cta:hover{background:var(--koral-ciemny)}
.gk .wpis-cta{margin:46px 0 0;background:var(--granat);color:#fff;border-radius:28px;padding:clamp(24px,4vw,40px)}.gk .wpis-cta h3{color:#fff;font-size:clamp(24px,3vw,32px);margin:6px 0 10px}.gk .wpis-cta p{color:rgba(255,255,255,.9)}.gk .wpis-cta .pisane{color:var(--zolty)}
.gk .wpis-nav{display:flex;justify-content:space-between;gap:16px;margin-top:34px;flex-wrap:wrap}.gk .wpis-nav a{color:var(--koral-ciemny);font-weight:700;text-decoration:none;max-width:48%}
</style>';
}

/* Wersja 1.5: Regulamin – informacja, że materiały powstają z pomocą AI (prośba Angeliki 18.11.2026). */
add_action( 'init', function () {
	if ( '1.5' === get_option( 'gk_strona_ver5' ) ) { return; }
	global $wpdb;
	$c = (string) $wpdb->get_var( "SELECT post_content FROM {$wpdb->posts} WHERE ID = 66" );
	if ( $c && false === strpos( $c, 'sztucznej inteligencji' ) ) {
		$pkt = '<li>Materiały są przygotowywane przy pomocy narzędzi sztucznej inteligencji (AI). Treści, ćwiczenia i grafiki powstają pod nadzorem autorki, która je opracowuje, sprawdza i odpowiada za ich zawartość. Materiały mają charakter edukacyjny i nie stanowią indywidualnej porady prawnej, podatkowej ani finansowej.</li>';
		$n = preg_replace( '#(<h2 id="s8">.*?)(</ol>)#s', '$1' . $pkt . '$2', $c, 1, $ile );
		if ( ! $ile ) { $n = str_replace( '<h2 id="s9">', '<h2 id="s8a">Materiały przygotowywane z pomocą AI</h2><p>' . wp_strip_all_tags( $pkt ) . '</p><h2 id="s9">', $c ); }
		$n = str_replace( '<p class="data">Obowiązuje od 5 listopada 2026 r.</p>', '<p class="data">Obowiązuje od 5 listopada 2026 r. · aktualizacja: 18 listopada 2026 r.</p>', $n );
		if ( $n !== $c ) { $wpdb->update( $wpdb->posts, array( 'post_content' => $n, 'post_modified' => current_time( 'mysql' ), 'post_modified_gmt' => current_time( 'mysql', 1 ) ), array( 'ID' => 66 ) ); clean_post_cache( 66 ); }
	}
	update_option( 'gk_strona_ver5', '1.5' );
	if ( function_exists( 'w3tc_flush_all' ) ) { w3tc_flush_all(); }
}, 24 );

/* Wersja 1.6: Polityka prywatności – cookies marketingowe (Meta, Google Ads), Mailchimp, newsletter (18.11.2026). */
add_action( 'init', function () {
	if ( '1.6' === get_option( 'gk_strona_ver6' ) ) { return; }
	global $wpdb;
	$c = (string) $wpdb->get_var( "SELECT post_content FROM {$wpdb->posts} WHERE ID = 3" );
	if ( $c && false === strpos( $c, 'Meta Platforms Ireland' ) ) {
		$z = array(
			'<tr><td>Statystyki i analityka</td>' => '<tr><td>Newsletter i Pakiet startowy</td><td>imię, e-mail, data i adres IP zapisu oraz potwierdzenia, historia otwarć i kliknięć</td><td>wysyłka newslettera, materiałów i powiązanych ofert</td><td>art. 6 ust. 1 lit. a (zgoda, wycofasz ją linkiem w każdym mailu)</td></tr>' . "\n" . '<tr><td>Reklama i mierzenie kampanii</td><td>identyfikatory cookies i urządzenia, informacje o odwiedzonych stronach i zakupach</td><td>wyświetlanie reklam osobom zainteresowanym, mierzenie skuteczności reklam (Meta, Google Ads)</td><td>art. 6 ust. 1 lit. a (zgoda w banerze cookies)</td></tr>' . "\n" . '<tr><td>Statystyki i analityka</td>',
			'<li><b>Google Ireland Ltd.</b>: Google Tag Manager i Google Analytics (tylko po Twojej zgodzie) oraz czcionki Google Fonts,</li>' => '<li><b>Google Ireland Ltd.</b>: Google Tag Manager, Google Analytics i Google Ads (tylko po Twojej zgodzie) oraz czcionki Google Fonts,</li>' . "\n" . '<li><b>Meta Platforms Ireland Ltd.</b>: Facebook, Instagram, Messenger oraz piksel Meta do mierzenia reklam (piksel tylko po Twojej zgodzie),</li>' . "\n" . '<li><b>Intuit Inc. (Mailchimp)</b>: obsługa listy newslettera dla osób, które się zapisały,</li>',
			'<p>Część dostawców (Google, Automattic, Stripe)' => '<p>Część dostawców (Google, Meta, Mailchimp, Automattic, Stripe)',
			'<tr><td><b>Analityczne</b></td>' => '<tr><td><b>Marketingowe</b></td><td>_fbp (Meta), _gcl_au i inne cookies Google Ads</td><td>mierzenie skuteczności reklam i wyświetlanie reklam osobom, które odwiedziły stronę</td><td><b>tylko po Twojej zgodzie</b></td></tr>' . "\n" . '<tr><td><b>Analityczne</b></td>',
			'Używam ich w trzech grupach:' => 'Używam ich w czterech grupach:',
			'<li>dane przetwarzane na podstawie zgody: do jej wycofania.</li>' => '<li>newsletter: do wypisania się (link w każdym mailu); dane potwierdzenia zgody do 3 lat po wypisaniu na potrzeby wykazania zgody,</li>' . "\n" . '<li>dane przetwarzane na podstawie zgody: do jej wycofania.</li>',
			'<p class="data">Obowiązuje od 5 listopada 2026 r.</p>' => '<p class="data">Obowiązuje od 5 listopada 2026 r. · aktualizacja: 18 listopada 2026 r.</p>',
		);
		$n = str_replace( array_keys( $z ), array_values( $z ), $c );
		if ( $n !== $c ) { $wpdb->update( $wpdb->posts, array( 'post_content' => $n, 'post_modified' => current_time( 'mysql' ), 'post_modified_gmt' => current_time( 'mysql', 1 ) ), array( 'ID' => 3 ) ); clean_post_cache( 3 ); }
	}
	update_option( 'gk_strona_ver6', '1.6' );
	if ( function_exists( 'w3tc_flush_all' ) ) { w3tc_flush_all(); }
}, 25 );

/* Wersja 1.7: Wafel zamiast Lilka w całej treści strony (decyzja Angeliki 16.11, prośba 19.11) + raport. */
add_action( 'init', function () {
	if ( '1.7' === get_option( 'gk_strona_ver7' ) ) { return; }
	update_option( 'gk_strona_ver7', '1.7' );
	global $wpdb;
	$svg   = (string) @file_get_contents( __DIR__ . '/wafel.svg' );
	$png   = home_url( '/wp-content/uploads/2026/11/wafel-macha.png' );
	$slowa = array( 'Lilkiem' => 'Waflem', 'Lilkowi' => 'Waflowi', 'Lilka' => 'Wafla', 'Lilku' => 'Waflu', 'Lilek' => 'Wafel' );
	$raport = array();
	$ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_type NOT IN ('revision','attachment') AND post_status IN ('publish','draft','private','future','pending') AND ( post_content LIKE '%Lil%' OR post_title LIKE '%Lil%' OR post_excerpt LIKE '%Lil%' )" );
	foreach ( $ids as $id ) {
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT post_content, post_title, post_excerpt, post_type FROM {$wpdb->posts} WHERE ID = %d", $id ) );
		$c = $row->post_content; $t = $row->post_title; $e = $row->post_excerpt;
		if ( ! preg_match( '/Lil(ek|ka|kiem|kowi|ku)\b|lilek[-_.a-z0-9]*\.(png|svg|jpe?g|webp)/iu', $c . $t . $e ) ) { continue; }
		if ( $svg ) { $c = preg_replace( '#<svg class="lilek"[^>]*>.*?</svg>#s', $svg, $c ); }
		$c = str_replace( 'Cześć! Jestem Lilek. Mały, ale łebski.', 'Cześć! Jestem Wafel. Pomogę Ci w nauce!', $c );
		$c = preg_replace( '#(https?://[^"\'\s)]*?)lilek[-_a-z0-9]*\.(png|svg|jpe?g|webp)#i', $png, $c );
		foreach ( $slowa as $a => $b ) {
			$c = preg_replace( '/\b' . $a . '\b/u', $b, $c ); $t = preg_replace( '/\b' . $a . '\b/u', $b, $t ); $e = preg_replace( '/\b' . $a . '\b/u', $b, $e );
		}
		$wpdb->update( $wpdb->posts, array( 'post_content' => $c, 'post_title' => $t, 'post_excerpt' => $e ), array( 'ID' => $id ) );
		clean_post_cache( $id );
		$raport[] = $row->post_type . ' ' . $id . ' (' . $t . '): podmienione';
	}
	$media = $wpdb->get_results( "SELECT ID, post_title, guid FROM {$wpdb->posts} WHERE post_type = 'attachment' AND ( guid LIKE '%lilek%' OR post_title LIKE '%Lilek%' OR post_title LIKE '%lilek%' )" );
	foreach ( $media as $m ) { $raport[] = 'MEDIUM ' . $m->ID . ' (' . $m->post_title . ') ' . $m->guid . ' – do usunięcia/zastąpienia'; }
	$meta = $wpdb->get_results( "SELECT post_id, meta_key FROM {$wpdb->postmeta} WHERE meta_value LIKE '%Lilek%' OR meta_value LIKE '%lilek.%' LIMIT 50" );
	foreach ( $meta as $m ) { $raport[] = 'META post ' . $m->post_id . ' klucz ' . $m->meta_key . ' – zawiera Lilka (np. Yoast/og:image)'; }
	$opt = $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_value LIKE '%Lilek%' AND option_name NOT LIKE '\\_%' LIMIT 30" );
	foreach ( $opt as $o ) { $raport[] = 'OPCJA ' . $o . ' – zawiera Lilka'; }
	update_option( 'gk_raport_lilek', $raport, false );
	wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'draft', 'post_title' => 'Raport Lilek → Wafel', 'post_content' => '<!-- wp:html --><pre>' . esc_html( implode( "\n", $raport ? $raport : array( 'Nie znaleziono Lilka.' ) ) ) . '</pre><!-- /wp:html -->' ) );
	if ( function_exists( 'w3tc_flush_all' ) ) { w3tc_flush_all(); }
}, 26 );
