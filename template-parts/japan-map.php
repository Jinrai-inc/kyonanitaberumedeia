<?php
/**
 * Template Part: Japan Map - Interactive Area Search
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$area_url_pattern = get_theme_mod( 'knt_area_url_pattern', 'search' );
// Options: 'search' → /?s=エリア名+グルメ
//          'taxonomy' → /area/{prefecture}/{city}/
?>
<section class="japan-map-section section fadeup" data-area-url="<?php echo esc_attr( $area_url_pattern ); ?>">
  <div class="container">
    <div class="section__header">
      <h2 class="section__title">エリアからお店を探す</h2>
    </div>
    <div class="japan-map-container">
      <div class="japan-map-visual">
        <div id="japan-map" class="japan-map-svg-wrapper"></div>
      </div>
      <div class="japan-map-controls">
        <div class="japan-map-controls-card">
          <p class="japan-map-controls-card-title">エリアを選んでください</p>

          <div class="japan-map-dropdown-group">
            <label for="pref-select">都道府県を選ぶ</label>
            <select id="pref-select" class="japan-map-dropdown">
              <option value="">都道府県を選択</option>
            </select>
          </div>

          <div class="japan-map-dropdown-group">
            <label for="city-select">市区町村を選ぶ</label>
            <select id="city-select" class="japan-map-dropdown" disabled>
              <option value="">先に都道府県を選択してください</option>
            </select>
          </div>

          <div class="japan-map-city-panel" id="city-panel"></div>

          <a href="#" id="area-search-btn" class="japan-map-btn is-disabled">このエリアの記事を見る →</a>
        </div>
      </div>
    </div>
  </div>
</section>
