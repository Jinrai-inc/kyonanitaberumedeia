<?php
/**
 * Template Part: Area Gate (first-visit overlay)
 *
 * localStorage ('knt-area') に値が無い場合のみ表示。
 * redesign JS が開閉・保存を担当。PHP は静的マークアップのみ出力。
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'knt_get_prefectures' ) ) {
    return;
}

$rd_regions = knt_get_regions();
$rd_prefs   = knt_get_prefectures();

// 地方ごとにグループ化
$rd_by_region = array();
foreach ( $rd_regions as $rk => $rl ) $rd_by_region[ $rk ] = array();
foreach ( $rd_prefs as $p ) {
    if ( isset( $rd_by_region[ $p['region'] ] ) ) {
        $rd_by_region[ $p['region'] ][] = $p;
    }
}
?>
<div class="rd-area-gate" data-rd-area-gate hidden aria-hidden="true" role="dialog" aria-labelledby="rd-area-gate-title" aria-modal="true">
    <div class="rd-area-gate__backdrop" data-rd-area-gate-close></div>
    <div class="rd-area-gate__dialog" role="document">
        <button type="button" class="rd-area-gate__close" data-rd-area-gate-close aria-label="閉じる">&times;</button>

        <div class="rd-area-gate__head">
            <span class="rd-area-gate__eyebrow">はじめまして 🍱</span>
            <h2 class="rd-area-gate__title" id="rd-area-gate-title">お住まい・よく行くエリアは？</h2>
            <p class="rd-area-gate__lead">選ぶと、次回からそのエリアのおすすめを優先表示します。<br>あとから右上のボタンで変更できます。</p>
        </div>

        <div class="rd-area-gate__tabs" role="tablist">
            <?php $first = true; foreach ( $rd_regions as $rk => $rl ) : ?>
                <button type="button"
                        class="rd-area-gate__tab<?php echo $first ? ' is-active' : ''; ?>"
                        role="tab"
                        data-region="<?php echo esc_attr( $rk ); ?>"
                        aria-selected="<?php echo $first ? 'true' : 'false'; ?>"><?php echo esc_html( $rl ); ?></button>
            <?php $first = false; endforeach; ?>
        </div>

        <div class="rd-area-gate__panels">
            <?php $first = true; foreach ( $rd_regions as $rk => $rl ) :
                if ( empty( $rd_by_region[ $rk ] ) ) continue;
            ?>
                <div class="rd-area-gate__panel<?php echo $first ? ' is-active' : ''; ?>"
                     data-region-panel="<?php echo esc_attr( $rk ); ?>"
                     aria-hidden="<?php echo $first ? 'false' : 'true'; ?>">
                    <div class="rd-area-gate__grid">
                        <?php foreach ( $rd_by_region[ $rk ] as $pref ) : ?>
                            <button type="button"
                                    class="rd-area-gate__pref"
                                    data-pref-code="<?php echo esc_attr( $pref['code'] ); ?>"
                                    data-pref-name="<?php echo esc_attr( $pref['name'] ); ?>"
                                    data-pref-region="<?php echo esc_attr( $pref['region'] ); ?>"
                                    data-pref-url="<?php echo esc_url( knt_prefecture_url( $pref['code'], $pref['name'] ) ); ?>">
                                <?php echo esc_html( $pref['name'] ); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php $first = false; endforeach; ?>
        </div>

        <div class="rd-area-gate__foot">
            <button type="button" class="rd-area-gate__skip" data-rd-area-gate-skip>あとで選ぶ</button>
        </div>
    </div>
</div>
