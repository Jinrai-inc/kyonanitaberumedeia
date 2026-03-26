<?php
/**
 * Template Part: Content Slider (Hero Carousel)
 * 無限ループ自動スクロール + リッチなオーバーレイ
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$slider_count = 10;
$slider_query = new WP_Query( array(
    'posts_per_page' => $slider_count,
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
) );

if ( ! $slider_query->have_posts() ) {
    return;
}

// スライドHTMLを配列に格納（2回出力するため）
$slides_html = array();
while ( $slider_query->have_posts() ) {
    $slider_query->the_post();
    $cats = get_the_category();
    $cat_name = $cats ? $cats[0]->name : '';
    $thumb = has_post_thumbnail()
        ? get_the_post_thumbnail( null, 'knt-hero', array( 'class' => 'content-slider__img', 'alt' => get_the_title() ) )
        : '<div class="content-slider__img content-slider__img--placeholder"></div>';

    $slides_html[] = '<a href="' . esc_url( get_permalink() ) . '" class="content-slider__slide">'
        . $thumb
        . '<div class="content-slider__overlay">'
        . ( $cat_name ? '<span class="content-slider__cat">' . esc_html( $cat_name ) . '</span>' : '' )
        . '<h3 class="content-slider__title">' . esc_html( get_the_title() ) . '</h3>'
        . '</div>'
        . '</a>';
}
wp_reset_postdata();
?>
<section class="content-slider" id="content-slider">
    <div class="content-slider__track" id="slider-track">
        <?php
        // 2回出力してシームレスループ
        echo implode( '', $slides_html );
        echo implode( '', $slides_html );
        ?>
    </div>
</section>
