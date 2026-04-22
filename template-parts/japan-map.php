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
              $svg_content = file_get_contents( $svg_path );
              // XML宣言を除去（short_open_tag対策）
              $svg_content = preg_replace( '/<\?xml[^?]*\?>/', '', $svg_content );
              echo $svg_content;
          }
          ?>
        </div>
      </div>
      <div class="japan-map-controls">
        <div class="japan-map-controls-card">
          <p class="japan-map-controls-card-title">今日は何食べる？</p>

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

          <div class="japan-map-geo-wrap">
            <button type="button" id="geolocate-btn" class="japan-map-geo-btn">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/></svg>
              現在地で探す
            </button>
            <p id="geo-status" class="japan-map-geo-status"></p>
          </div>

          <div class="japan-map-city-panel" id="city-panel"></div>

          <a href="#" id="area-search-btn" class="japan-map-btn is-disabled">このエリアの記事を見る →</a>
        </div>
      </div>
    </div>
  </div>
</section>
