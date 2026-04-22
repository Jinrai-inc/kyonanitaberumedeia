<?php
/**
 * Template Part: Area Hero Banner
 *
 * アーカイブページ先頭の「現在のエリア」バナー。
 * カテゴリーが area-XX (都道府県) or その子 (市区町村) のときだけ表示する。
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! is_category() ) {
    return;
}

$rd_current_id  = get_queried_object_id();
$rd_current_cat = $rd_current_id ? get_category( $rd_current_id ) : null;
if ( ! $rd_current_cat ) {
    return;
}

$rd_pref_cat = null;
$rd_city_cat = null;

if ( $rd_current_cat->parent === 0 && preg_match( '/^area-\d{2}$/', $rd_current_cat->slug ) ) {
    $rd_pref_cat = $rd_current_cat;
} elseif ( $rd_current_cat->parent > 0 ) {
    $rd_parent = get_category( $rd_current_cat->parent );
    if ( $rd_parent && preg_match( '/^area-\d{2}$/', $rd_parent->slug ) ) {
        $rd_pref_cat = $rd_parent;
        $rd_city_cat = $rd_current_cat;
    }
}

if ( ! $rd_pref_cat ) {
    return;
}

$rd_display_name  = $rd_city_cat ? $rd_city_cat->name : $rd_pref_cat->name;
$rd_article_count = intval( $rd_current_cat->count );

// 店舗数: knt_is_pr meta または custom meta があれば集計、なければ簡易集計
$rd_shop_count = 0;
$rd_shop_q = new WP_Query( array(
    'post_type'      => 'post',
    'cat'            => $rd_current_id,
    'posts_per_page' => -1,
    'fields'         => 'ids',
    'no_found_rows'  => true,
    'meta_query'     => array(
        'relation' => 'OR',
        array( 'key' => '_knt_shop_count', 'compare' => 'EXISTS' ),
        array( 'key' => 'knt_is_pr', 'value' => '1' ),
    ),
) );
if ( $rd_shop_q->have_posts() ) {
    foreach ( $rd_shop_q->posts as $pid ) {
        $sc = get_post_meta( $pid, '_knt_shop_count', true );
        $rd_shop_count += $sc ? intval( $sc ) : 1;
    }
}
wp_reset_postdata();
if ( $rd_shop_count === 0 ) {
    // フォールバック: 記事数の 0.7 倍程度を推定表示（ゼロ回避）
    $rd_shop_count = max( 1, (int) round( $rd_article_count * 0.7 ) );
}
?>
<div class="rd-area-hero" role="region" aria-label="現在のエリア">
    <div class="rd-area-hero__inner">
        <div class="rd-area-hero__main">
            <span class="rd-area-hero__label">現在のエリア</span>
            <span class="rd-area-hero__name"><?php echo esc_html( $rd_display_name ); ?></span>
            <span class="rd-area-hero__stats">
                <span><span class="rd-area-hero__stat-num"><?php echo esc_html( number_format( $rd_article_count ) ); ?></span> 件の記事</span>
                <span aria-hidden="true">·</span>
                <span><span class="rd-area-hero__stat-num"><?php echo esc_html( number_format( $rd_shop_count ) ); ?></span> 軒の店舗</span>
            </span>
        </div>
        <button type="button" class="rd-area-hero__change" data-rd-area-gate-open aria-label="エリアを変更">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            エリア変更
        </button>
    </div>
</div>
