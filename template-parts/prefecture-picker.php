<?php
/**
 * Template Part: Prefecture Picker (47都道府県 + 8地方タブ)
 *
 * 既存の area-XX カテゴリアーカイブにジャンプする静的ピッカー。
 * 地方タブ切替は JS で行うが、JS 無効時も全都道府県が表示される（progressive enhancement）。
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'knt_get_prefectures' ) ) {
    return;
}

$rd_prefectures = knt_get_prefectures();
$rd_regions     = knt_get_regions();

$rd_picker_title    = apply_filters( 'knt_picker_title', '都道府県から探す' );
$rd_picker_subtitle = apply_filters( 'knt_picker_subtitle', '47都道府県のご当地グルメ記事を地方別に一覧。' );

// 地方ごとに都道府県をグループ化
$rd_pref_by_region = array();
foreach ( $rd_regions as $region_key => $region_label ) {
    $rd_pref_by_region[ $region_key ] = array();
}
foreach ( $rd_prefectures as $pref ) {
    if ( isset( $rd_pref_by_region[ $pref['region'] ] ) ) {
        $rd_pref_by_region[ $pref['region'] ][] = $pref;
    }
}

$rd_first_region = 'kanto'; // デフォルト開き先
?>
<section class="rd-pref-picker fadeup" aria-label="都道府県ピッカー" data-rd-pref-picker>
    <div class="rd-pref-picker__header">
        <h2 class="rd-pref-picker__title"><?php echo esc_html( $rd_picker_title ); ?></h2>
        <p class="rd-pref-picker__subtitle"><?php echo esc_html( $rd_picker_subtitle ); ?></p>
    </div>

    <div class="rd-pref-picker__tabs" role="tablist">
        <?php foreach ( $rd_regions as $region_key => $region_label ) :
            $is_active = ( $region_key === $rd_first_region );
        ?>
            <button type="button"
                    class="rd-pref-picker__tab<?php echo $is_active ? ' is-active' : ''; ?>"
                    role="tab"
                    aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
                    data-region="<?php echo esc_attr( $region_key ); ?>">
                <span class="rd-pref-picker__tab-dot" data-region-dot="<?php echo esc_attr( $region_key ); ?>"></span>
                <?php echo esc_html( $region_label ); ?>
            </button>
        <?php endforeach; ?>
    </div>

    <div class="rd-pref-picker__panels">
        <?php foreach ( $rd_regions as $region_key => $region_label ) :
            $prefs = $rd_pref_by_region[ $region_key ];
            if ( empty( $prefs ) ) continue;
            $is_active = ( $region_key === $rd_first_region );
        ?>
            <div class="rd-pref-picker__panel<?php echo $is_active ? ' is-active' : ''; ?>"
                 role="tabpanel"
                 data-region-panel="<?php echo esc_attr( $region_key ); ?>"
                 aria-hidden="<?php echo $is_active ? 'false' : 'true'; ?>">
                <div class="rd-pref-picker__grid">
                    <?php foreach ( $prefs as $pref ) :
                        $url   = knt_prefecture_url( $pref['code'], $pref['name'] );
                        $count = knt_prefecture_count( $pref['code'] );
                    ?>
                        <a class="rd-pref-card rd-pref-card--<?php echo esc_attr( $region_key ); ?>"
                           href="<?php echo esc_url( $url ); ?>"
                           data-pref-code="<?php echo esc_attr( $pref['code'] ); ?>"
                           data-pref-name="<?php echo esc_attr( $pref['name'] ); ?>">
                            <span class="rd-pref-card__code"><?php echo esc_html( $pref['code'] ); ?></span>
                            <span class="rd-pref-card__name"><?php echo esc_html( $pref['name'] ); ?></span>
                            <?php if ( ! empty( $pref['dish'] ) ) : ?>
                                <span class="rd-pref-card__dish"><?php echo esc_html( $pref['dish'] ); ?></span>
                            <?php endif; ?>
                            <?php if ( $count > 0 ) : ?>
                                <span class="rd-pref-card__count"><?php echo esc_html( $count ); ?>記事</span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
