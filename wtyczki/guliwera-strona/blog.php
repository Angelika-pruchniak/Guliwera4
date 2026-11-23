<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$ch   = gk_ss_chrome();
$home = untrailingslashit( home_url() );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
<?php echo $ch['css']; // phpcs:ignore ?>
<?php echo gk_blog_css(); // phpcs:ignore ?>
</head>
<body <?php body_class( 'gk-blog-body' ); ?> style="margin:0;background:#FFF6EE">
<?php wp_body_open(); ?>
<div class="gk">
<?php echo $ch['head']; // phpcs:ignore ?>
<?php if ( is_singular( 'post' ) ) : while ( have_posts() ) : the_post(); $kat = get_the_category(); ?>
<section class="sekcja" style="padding-top:clamp(30px,5vw,60px)"><div class="wrap"><article class="wpis">
  <p class="wpis-meta"><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>">← Blog</a><?php if ( $kat ) : ?> · <a href="<?php echo esc_url( get_category_link( $kat[0] ) ); ?>"><?php echo esc_html( $kat[0]->name ); ?></a><?php endif; ?></p>
  <h1><?php the_title(); ?></h1>
  <p class="wpis-meta"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?> · Angelika Pruchniak-Gąszczyk · <?php echo (int) max( 1, round( str_word_count( wp_strip_all_tags( get_the_content() ) ) / 200 ) ); ?> min czytania</p>
  <?php if ( has_post_thumbnail() ) : ?><figure class="wpis-fot"><?php the_post_thumbnail( 'large' ); ?></figure><?php endif; ?>
  <div class="wpis-tresc"><?php the_content(); ?></div>
  <aside class="wpis-cta"><span class="pisane">pierwszy krok</span><h3>Strategia i liczby w 5 minut dziennie</h3><p>Darmowe lekcje w Akademii Guliwera: strategia, priorytety i liczby w firmie, na telefonie, w Twoim tempie.</p><a class="g4-cta" href="<?php echo esc_url( home_url( '/aplikacja/' ) ); ?>">Zacznij za darmo</a></aside>
  <nav class="wpis-nav"><?php previous_post_link( '%link', '← %title' ); ?><?php next_post_link( '%link', '%title →' ); ?></nav>
</article></div></section>
<?php endwhile; else : ?>
<section class="bl-hero"><div class="wrap">
  <span class="pisane">styl, biznes i rozwój</span>
  <h1><?php echo is_category() ? esc_html( single_cat_title( '', false ) ) : 'Blog'; ?></h1>
  <p class="bl-lead"><?php echo is_category() && category_description() ? esc_html( wp_strip_all_tags( category_description() ) ) : 'Lookbooki, stylizacje do pracy i artykuły o strategii, karierze i rozwoju. Dla kobiet, które prowadzą firmy, zespoły i własną drogę.'; ?></p>
  <ul class="bl-kat"><li><a<?php echo is_home() ? ' class="on"' : ''; ?> href="<?php echo esc_url( home_url( '/blog/' ) ); ?>">Wszystko</a></li><?php foreach ( get_categories( array( 'hide_empty' => true, 'exclude' => array( (int) get_option( 'default_category' ) ) ) ) as $c ) : ?><li><a<?php echo is_category( $c->term_id ) ? ' class="on"' : ''; ?> href="<?php echo esc_url( get_category_link( $c ) ); ?>"><?php echo esc_html( $c->name ); ?></a></li><?php endforeach; ?></ul>
</div></section>
<section class="sekcja" style="padding-top:10px"><div class="wrap">
<?php if ( have_posts() ) : ?>
  <div class="bl-siatka">
  <?php while ( have_posts() ) : the_post(); $kat = get_the_category(); ?>
    <a class="bl-karta" href="<?php the_permalink(); ?>">
      <div class="fot"><?php
        // Id obrazka prosto z bazy: Jetpack „Opcje treści” potrafi ukrywać obrazki wyróżniające na liście wpisów.
        global $wpdb; $gk_fid = (int) $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = '_thumbnail_id' LIMIT 1", get_the_ID() ) );
        if ( $gk_fid ) { echo wp_get_attachment_image( $gk_fid, 'medium_large', false, array( 'loading' => 'lazy', 'alt' => esc_attr( get_the_title() ) ) ); } // phpcs:ignore
      ?></div>
      <div class="tr"><?php if ( $kat ) : ?><span class="kat"><?php echo esc_html( $kat[0]->name ); ?></span><?php endif; ?><h2><?php the_title(); ?></h2><p><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p><span class="dt"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></span></div>
    </a>
  <?php endwhile; ?>
  </div>
  <div class="bl-pag"><?php echo paginate_links( array( 'prev_text' => '←', 'next_text' => '→' ) ); // phpcs:ignore ?></div>
<?php else : ?>
  <div class="bl-pusto"><h2>Pierwsze wpisy już wkrótce</h2><p style="color:var(--atrament-60);margin-top:10px">W międzyczasie zajrzyj do darmowej Akademii Guliwera.</p><a class="g4-cta" href="<?php echo esc_url( home_url( '/aplikacja/' ) ); ?>">Zacznij za darmo</a></div>
<?php endif; ?>
</div></section>
<?php endif; ?>
<?php echo $ch['foot']; // phpcs:ignore ?>
</div>
<?php wp_footer(); ?>
</body>
</html>
