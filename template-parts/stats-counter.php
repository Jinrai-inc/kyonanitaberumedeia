<?php
/**
 * Template Part: Stats Counter（実績カウンター）
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// 記事数
$post_count = wp_count_posts();
$article_count = intval( $post_count->publish );

// 店舗数（全記事の_knt_shop_countメタの合計）
global $wpdb;
$total_shops = $wpdb->get_var(
    "SELECT SUM(CAST(meta_value AS UNSIGNED)) FROM {$wpdb->postmeta}
     WHERE meta_key = '_knt_shop_count'
     AND post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_status = 'publish')"
);
$total_shops = intval( $total_shops ) ?: $article_count * 8;

// 都道府県数（area-XXカテゴリの登録数、記事有無問わず）
$pref_count = 0;
$area_cats = get_categories( array( 'parent' => 0, 'hide_empty' => false ) );
foreach ( $area_cats as $c ) {
    if ( preg_match( '/^area-\d{2}$/', $c->slug ) ) {
        $pref_count++;
    }
}
if ( $pref_count === 0 ) $pref_count = 47;
?>
<section class="stats-counter fadeup">
    <div class="stats-counter__inner">
        <div class="stats-counter__item">
            <span class="stats-counter__number" data-target="<?php echo esc_attr( $article_count ); ?>">0</span>
            <span class="stats-counter__label">記事数</span>
        </div>
        <div class="stats-counter__divider"></div>
        <div class="stats-counter__item">
            <span class="stats-counter__number" data-target="<?php echo esc_attr( $total_shops ); ?>">0</span><span class="stats-counter__unit">+</span>
            <span class="stats-counter__label">掲載店舗数</span>
        </div>
        <div class="stats-counter__divider"></div>
        <div class="stats-counter__item">
            <span class="stats-counter__number" data-target="<?php echo esc_attr( $pref_count ); ?>">0</span>
            <span class="stats-counter__label">都道府県</span>
        </div>
    </div>
</section>
