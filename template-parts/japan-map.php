<?php
/**
 * Template Part: Japan Map - First Visual / Interactive Area Search
 * 可愛いファーストビジュアル
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$area_url_pattern = get_theme_mod( 'knt_area_url_pattern', 'search' );
$fv_title = get_theme_mod( 'knt_japan_map_title', '食べたいエリアを選んでね' );
$fv_subtitle = get_theme_mod( 'knt_japan_map_subtitle', '地図をタップ or 下のメニューから選択できるよ' );
?>
<section class="japan-map-section fadeup" data-area-url="<?php echo esc_attr( $area_url_pattern ); ?>">
  <div class="container">
    <div class="section__header">
      <h2 class="section__title"><?php echo esc_html( $fv_title ); ?></h2>
      <p class="section__subtitle"><?php echo esc_html( $fv_subtitle ); ?></p>
    </div>
    <div class="japan-map-container">
      <div class="japan-map-visual">
        <div id="japan-map" class="japan-map-svg-wrapper">
          <?php
          // SVGをインライン埋め込み（CORS回避）
          $is_mobile = wp_is_mobile();
          $svg_file  = $is_mobile ? 'map-mobile.svg' : 'map-full.svg';
          $svg_path  = KNT_DIR . '/svg/' . $svg_file;
          if ( file_exists( $svg_path ) ) {
              echo file_get_contents( $svg_path );
          }
          ?>
        </div>
      </div>
      <div class="japan-map-controls">
        <div class="japan-map-controls-card">
          <p class="japan-map-controls-card-title">どこのグルメが気になる？</p>

          <div class="japan-map-dropdown-group">
            <label for="pref-select">都道府県を選ぶ</label>
            <select id="pref-select" class="japan-map-dropdown">
              <option value="">タップして選んでね</option>
            </select>
          </div>

          <div class="japan-map-dropdown-group">
            <label for="city-select">市区町村を選ぶ</label>
            <select id="city-select" class="japan-map-dropdown" disabled>
              <option value="">まず都道府県を選んでね</option>
            </select>
          </div>

          <div class="japan-map-city-panel" id="city-panel"></div>

          <a href="#" id="area-search-btn" class="japan-map-btn is-disabled">このエリアの記事を見る →</a>
        </div>
      </div>
    </div>
  </div>
</section>
