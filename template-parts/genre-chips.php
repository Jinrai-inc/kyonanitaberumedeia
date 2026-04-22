<?php
/**
 * Template Part: Genre Chips (8 ジャンルクイックフィルタ)
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$rd_genres = array(
    array(
        'label'   => 'ラーメン',
        'keyword' => 'ラーメン',
        'svg'     => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 22c0-6 7-10 16-10s16 4 16 10"/><path d="M6 22h36v4a8 8 0 0 1-8 8H14a8 8 0 0 1-8-8v-4z"/><path d="M18 16c-1 2-1 4 0 6"/><path d="M24 14c-1 2-1 5 0 7"/><path d="M30 16c-1 2-1 4 0 6"/></svg>',
    ),
    array(
        'label'   => '焼肉',
        'keyword' => '焼肉',
        'svg'     => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="22" width="36" height="18" rx="4"/><path d="M10 30h28"/><path d="M14 14c2-2 4-2 6 0s4 2 6 0 4-2 6 0 4 2 6 0"/><path d="M14 20h20"/></svg>',
    ),
    array(
        'label'   => '寿司',
        'keyword' => '寿司',
        'svg'     => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="24" cy="30" rx="16" ry="6"/><path d="M8 30v4a4 4 0 0 0 4 4h24a4 4 0 0 0 4-4v-4"/><path d="M10 24c4-4 8-6 14-6s10 2 14 6"/><path d="M18 14v8M24 12v10M30 14v8"/></svg>',
    ),
    array(
        'label'   => 'カフェ',
        'keyword' => 'カフェ',
        'svg'     => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 18h22v12a8 8 0 0 1-8 8h-6a8 8 0 0 1-8-8V18z"/><path d="M32 22h4a4 4 0 0 1 0 8h-4"/><path d="M14 10c-1 2 1 3 0 5M20 10c-1 2 1 3 0 5M26 10c-1 2 1 3 0 5"/></svg>',
    ),
    array(
        'label'   => '居酒屋',
        'keyword' => '居酒屋',
        'svg'     => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="10" y="14" width="28" height="26" rx="4"/><path d="M10 22h28M10 32h28"/><path d="M18 14v-4M30 14v-4"/><path d="M20 26h8"/></svg>',
    ),
    array(
        'label'   => '中華',
        'keyword' => '中華',
        'svg'     => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 26a16 10 0 0 0 32 0"/><path d="M6 26h36"/><path d="M14 20c2-4 6-6 10-6s8 2 10 6"/><circle cx="24" cy="16" r="1.5" fill="currentColor"/></svg>',
    ),
    array(
        'label'   => 'イタリアン',
        'keyword' => 'イタリアン',
        'svg'     => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="24" cy="24" r="16"/><path d="m10 24 3 3M14 20l3 3M20 16l3 3M26 14l3 3M32 16l3 3"/><circle cx="20" cy="22" r="1.5" fill="currentColor"/><circle cx="28" cy="28" r="1.5" fill="currentColor"/><circle cx="32" cy="22" r="1.5" fill="currentColor"/></svg>',
    ),
    array(
        'label'   => '和食',
        'keyword' => '和食',
        'svg'     => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="16" width="32" height="22" rx="3"/><path d="M8 24h32"/><circle cx="16" cy="30" r="3"/><circle cx="32" cy="30" r="3"/><path d="M14 12h20"/></svg>',
    ),
);

$rd_genres = apply_filters( 'knt_genre_chips', $rd_genres );

$rd_gc_regions = function_exists( 'knt_get_regions' ) ? knt_get_regions() : array();
$rd_gc_prefs   = function_exists( 'knt_get_prefectures' ) ? knt_get_prefectures() : array();
$rd_gc_by_reg  = array();
foreach ( $rd_gc_regions as $rk => $rl ) $rd_gc_by_reg[ $rk ] = array();
foreach ( $rd_gc_prefs as $p ) {
    if ( isset( $rd_gc_by_reg[ $p['region'] ] ) ) $rd_gc_by_reg[ $p['region'] ][] = $p;
}
?>
<section class="rd-genre-chips fadeup" aria-label="ジャンルで探す" data-rd-genre-chips>
    <div class="rd-genre-chips__header">
        <h2 class="rd-genre-chips__title">ジャンルで探す</h2>
        <span class="rd-genre-chips__hint">タップで関連記事を表示</span>
    </div>

    <?php if ( ! empty( $rd_gc_prefs ) ) : ?>
    <div class="rd-genre-chips__filter" data-rd-gc-filter>
        <span class="rd-genre-chips__filter-label">エリアで絞る</span>
        <div class="rd-genre-chips__filter-grid">
            <select class="rd-genre-chips__select" data-rd-gc-pref>
                <option value="">都道府県：全国</option>
                <?php foreach ( $rd_gc_regions as $rk => $rl ) : if ( empty( $rd_gc_by_reg[ $rk ] ) ) continue; ?>
                    <optgroup label="<?php echo esc_attr( $rl ); ?>">
                        <?php foreach ( $rd_gc_by_reg[ $rk ] as $p ) : ?>
                            <option value="<?php echo esc_attr( $p['name'] ); ?>"
                                    data-pref-code="<?php echo esc_attr( $p['code'] ); ?>"
                                    data-area-url="<?php echo esc_url( knt_prefecture_url( $p['code'], $p['name'] ) ); ?>">
                                <?php echo esc_html( $p['name'] ); ?>
                            </option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endforeach; ?>
            </select>
            <select class="rd-genre-chips__select" data-rd-gc-city disabled>
                <option value="">市区町村：まず都道府県を選択</option>
            </select>
            <select class="rd-genre-chips__select" data-rd-gc-station disabled>
                <option value="">駅：まず市区町村を選択</option>
            </select>
        </div>
        <button type="button" class="rd-genre-chips__filter-reset" data-rd-gc-reset hidden>リセット</button>
    </div>
    <?php endif; ?>

    <div class="rd-genre-chips__grid">
        <?php foreach ( $rd_genres as $g ) : ?>
            <a class="rd-genre-chip"
               href="<?php echo esc_url( home_url( '/?s=' . urlencode( $g['keyword'] ) ) ); ?>"
               data-genre-keyword="<?php echo esc_attr( $g['keyword'] ); ?>">
                <span class="rd-genre-chip__icon" aria-hidden="true"><?php echo $g['svg']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                <span class="rd-genre-chip__label"><?php echo esc_html( $g['label'] ); ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</section>
