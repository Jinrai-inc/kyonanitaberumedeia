<?php
/**
 * Template Part: Redesign Header Search
 *
 * 簡易サジェスト付きヘッダー検索バー。
 * サジェストはジャンル + 人気シーンから。JS が開閉とキー操作を制御。
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="rd-header-search" data-rd-header-search>
    <button type="button" class="rd-header-search__trigger" data-rd-header-search-open aria-label="検索を開く">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
            <circle cx="11" cy="11" r="7"/>
            <path d="m20 20-3.5-3.5"/>
        </svg>
        <span class="rd-header-search__trigger-label">キーワードで探す</span>
    </button>

    <div class="rd-header-search__panel" data-rd-header-search-panel hidden aria-hidden="true">
        <form class="rd-header-search__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <svg class="rd-header-search__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                <circle cx="11" cy="11" r="7"/>
                <path d="m20 20-3.5-3.5"/>
            </svg>
            <input type="search"
                   class="rd-header-search__input"
                   name="s"
                   placeholder="店名・ジャンル・シーンで検索…"
                   autocomplete="off"
                   data-rd-header-search-input />
            <button type="button" class="rd-header-search__close" data-rd-header-search-close aria-label="閉じる">&times;</button>
        </form>

        <div class="rd-header-search__suggest" data-rd-header-search-suggest>
            <div class="rd-header-search__group">
                <span class="rd-header-search__group-label">人気のジャンル</span>
                <div class="rd-header-search__chips">
                    <?php
                    $sugg_genres = array( 'ラーメン', '焼肉', '寿司', 'カフェ', '居酒屋', '中華', 'イタリアン', '和食' );
                    foreach ( $sugg_genres as $g ) :
                    ?>
                        <a class="rd-header-search__chip" href="<?php echo esc_url( home_url( '/?s=' . urlencode( $g ) ) ); ?>"><?php echo esc_html( $g ); ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="rd-header-search__group">
                <span class="rd-header-search__group-label">人気のシーン</span>
                <div class="rd-header-search__chips">
                    <?php
                    $sugg_scenes = array( 'デート', '女子会', 'ランチ', '一人飲み', '子連れ', '接待', '個室', '深夜' );
                    foreach ( $sugg_scenes as $s ) :
                    ?>
                        <a class="rd-header-search__chip" href="<?php echo esc_url( home_url( '/?s=' . urlencode( $s ) ) ); ?>">#<?php echo esc_html( $s ); ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
