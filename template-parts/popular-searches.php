<?php
/**
 * Template Part: Popular Searches (人気ワードチップ)
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 人気ワード：表示ラベル → 検索キーワード
$rd_popular_searches = array(
    'デート'         => 'デート',
    '女子会'         => '女子会',
    '家族'           => '家族',
    '子連れ'         => '子連れ',
    '一人飲み'       => '一人飲み',
    'ランチ'         => 'ランチ',
    '個室'           => '個室',
    '深夜'           => '深夜',
    'テラス'         => 'テラス',
    'コスパ'         => 'コスパ',
);

$rd_popular_searches = apply_filters( 'knt_popular_searches', $rd_popular_searches );
?>
<section class="rd-popular-searches fadeup" aria-label="人気のシーン・ワード">
    <div class="rd-popular-searches__label">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
            <path d="M13.5 2L11 13h6L9 22l2.5-9H5z"/>
        </svg>
        <span>人気のシーンで探す</span>
    </div>
    <div class="rd-popular-searches__chips">
        <?php foreach ( $rd_popular_searches as $label => $keyword ) : ?>
            <a class="rd-popular-searches__chip" href="<?php echo esc_url( home_url( '/?s=' . urlencode( $keyword ) ) ); ?>">
                #<?php echo esc_html( $label ); ?>
            </a>
        <?php endforeach; ?>
    </div>
</section>
