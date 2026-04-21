<?php
/**
 * Template Part: Hero Band
 *
 * スライダー直後の大型ヒーロー検索バンド。トップページ冒頭の視覚的主役。
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$rd_hero_eyebrow  = apply_filters( 'knt_hero_eyebrow', '今日、どこで何食べる？' );
$rd_hero_title    = apply_filters( 'knt_hero_title', 'ローカル × ご当地グルメ。' );
$rd_hero_lead     = apply_filters( 'knt_hero_lead', 'エリアと気分で、ぴったりの一軒が見つかる日本の食メディア。' );
$rd_hero_quick    = apply_filters( 'knt_hero_quick_picks', array(
    '個室 ランチ', 'コスパ 焼肉', '深夜 ラーメン', '記念日 寿司', 'デート イタリアン',
) );

// 現在の保存エリア（無ければ全国）のラベル用
?>
<section class="rd-hero-band" aria-label="今日何食べる？ ヒーロー">
    <div class="rd-hero-band__bg" aria-hidden="true">
        <div class="rd-hero-band__blob rd-hero-band__blob--1"></div>
        <div class="rd-hero-band__blob rd-hero-band__blob--2"></div>
        <div class="rd-hero-band__blob rd-hero-band__blob--3"></div>
    </div>

    <div class="rd-hero-band__inner">
        <div class="rd-hero-band__eyebrow">
            <span class="rd-hero-band__dot" aria-hidden="true"></span>
            <span><?php echo esc_html( $rd_hero_eyebrow ); ?></span>
        </div>

        <h1 class="rd-hero-band__title">
            <?php echo esc_html( $rd_hero_title ); ?>
        </h1>

        <p class="rd-hero-band__lead"><?php echo esc_html( $rd_hero_lead ); ?></p>

        <form class="rd-hero-band__search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <span class="rd-hero-band__search-pin" aria-hidden="true">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                <span data-rd-area-badge-label class="rd-hero-band__area">全国</span>
                <button type="button" class="rd-hero-band__area-change" data-rd-area-gate-open aria-label="エリアを変更">変更</button>
            </span>
            <span class="rd-hero-band__sep" aria-hidden="true"></span>
            <svg class="rd-hero-band__search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
                <circle cx="11" cy="11" r="7"/>
                <path d="m20 20-3.5-3.5"/>
            </svg>
            <input type="search" name="s" class="rd-hero-band__input"
                   placeholder="ジャンル・シーン・気分で検索…"
                   autocomplete="off" />
            <button type="submit" class="rd-hero-band__submit">
                探す
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                </svg>
            </button>
        </form>

        <?php if ( ! empty( $rd_hero_quick ) ) : ?>
        <div class="rd-hero-band__quicks">
            <span class="rd-hero-band__quicks-label">人気:</span>
            <?php foreach ( $rd_hero_quick as $q ) : ?>
                <a class="rd-hero-band__quick" href="<?php echo esc_url( home_url( '/?s=' . urlencode( $q ) ) ); ?>"><?php echo esc_html( $q ); ?></a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="rd-hero-band__stamp" aria-hidden="true">
            <svg viewBox="0 0 80 80" width="64" height="64">
                <circle cx="40" cy="40" r="36" fill="none" stroke="currentColor" stroke-width="1.5"/>
                <text>
                    <textPath href="#rd-hero-stamp-path" startOffset="0%">
                        KYOU-NANI-TABERU · MEDIA · 今日何食べる ·
                    </textPath>
                </text>
                <path id="rd-hero-stamp-path" d="M40,40 m-28,0 a28,28 0 1,1 56,0 a28,28 0 1,1 -56,0" fill="none"/>
                <g transform="translate(40 40)">
                    <circle r="14" fill="currentColor" opacity="0.1"/>
                    <text text-anchor="middle" dy="5" font-size="11" font-weight="700" fill="currentColor">🍱</text>
                </g>
            </svg>
        </div>
    </div>
</section>
