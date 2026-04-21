<?php
/**
 * Template Part: Genre Chips (8 ジャンルクイックフィルタ)
 *
 * 既存の検索システム（home_url('/?s=...')）にそのまま乗るだけで、
 * 新しい PHP ロジックや WP 設定は増えない。
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 8 ジャンル：表示名 / 検索キーワード / 絵文字
$rd_genres = array(
    array( 'label' => 'ラーメン',   'keyword' => 'ラーメン',     'emoji' => '🍜' ),
    array( 'label' => '焼肉',       'keyword' => '焼肉',         'emoji' => '🥩' ),
    array( 'label' => '寿司',       'keyword' => '寿司',         'emoji' => '🍣' ),
    array( 'label' => 'カフェ',     'keyword' => 'カフェ',       'emoji' => '☕' ),
    array( 'label' => '居酒屋',     'keyword' => '居酒屋',       'emoji' => '🏮' ),
    array( 'label' => '中華',       'keyword' => '中華',         'emoji' => '🥟' ),
    array( 'label' => 'イタリアン', 'keyword' => 'イタリアン',   'emoji' => '🍝' ),
    array( 'label' => '和食',       'keyword' => '和食',         'emoji' => '🍱' ),
);

$rd_genres = apply_filters( 'knt_genre_chips', $rd_genres );
?>
<section class="rd-genre-chips fadeup" aria-label="ジャンルで探す">
    <div class="rd-genre-chips__header">
        <h2 class="rd-genre-chips__title">ジャンルで探す</h2>
        <span class="rd-genre-chips__hint">タップで関連記事を表示</span>
    </div>
    <div class="rd-genre-chips__grid">
        <?php foreach ( $rd_genres as $g ) : ?>
            <a class="rd-genre-chip" href="<?php echo esc_url( home_url( '/?s=' . urlencode( $g['keyword'] ) ) ); ?>">
                <span class="rd-genre-chip__emoji" aria-hidden="true"><?php echo esc_html( $g['emoji'] ); ?></span>
                <span class="rd-genre-chip__label"><?php echo esc_html( $g['label'] ); ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</section>
