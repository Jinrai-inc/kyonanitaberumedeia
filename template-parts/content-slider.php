<?php
/**
 * Template Part: Content Slider (Hero Carousel)
 *
 * トップページの地図セクションの上に表示するコンテンツスライダー。
 * 最新記事またはピックアップ記事を自動スライドで表示。
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$slider_count = get_theme_mod( 'knt_slider_count', 5 );
$slider_query = new WP_Query( array(
    'posts_per_page' => $slider_count,
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'meta_key'       => 'knt_views',
    'orderby'        => 'meta_value_num',
    'order'          => 'DESC',
) );

if ( ! $slider_query->have_posts() ) {
    // フォールバック: ビュー数がなければ最新記事
    $slider_query = new WP_Query( array(
        'posts_per_page' => $slider_count,
        'post_type'      => 'post',
        'post_status'    => 'publish',
    ) );
}

if ( ! $slider_query->have_posts() ) {
    return;
}
?>
<section class="content-slider" id="content-slider">
    <div class="content-slider__track" id="slider-track">
        <?php while ( $slider_query->have_posts() ) : $slider_query->the_post(); ?>
        <a href="<?php the_permalink(); ?>" class="content-slider__slide">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'knt-hero', array( 'class' => 'content-slider__img', 'alt' => get_the_title() ) ); ?>
            <?php else : ?>
                <div class="content-slider__img content-slider__img--placeholder"></div>
            <?php endif; ?>
            <div class="content-slider__overlay">
                <?php
                $cats = get_the_category();
                if ( $cats ) :
                ?>
                    <span class="content-slider__cat"><?php echo esc_html( $cats[0]->name ); ?></span>
                <?php endif; ?>
                <h3 class="content-slider__title"><?php the_title(); ?></h3>
            </div>
        </a>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
    <div class="content-slider__dots" id="slider-dots"></div>
</section>
