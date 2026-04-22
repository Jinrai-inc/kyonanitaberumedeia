<?php
/**
 * 記事自動更新（月1回WP-Cron）
 * 自動生成記事を最新のホットペッパーAPIデータで差し替え
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * カスタムCronスケジュール（月1回）を追加
 */
function knt_cron_schedules( $schedules ) {
    $schedules['monthly'] = array(
        'interval' => 30 * DAY_IN_SECONDS,
        'display'  => '月1回',
    );
    return $schedules;
}
add_filter( 'cron_schedules', 'knt_cron_schedules' );

/**
 * テーマ有効化時にCronをスケジュール
 */
function knt_schedule_auto_refresh() {
    if ( ! wp_next_scheduled( 'knt_auto_refresh_articles' ) ) {
        wp_schedule_event( time(), 'monthly', 'knt_auto_refresh_articles' );
    }
}
add_action( 'after_switch_theme', 'knt_schedule_auto_refresh' );
add_action( 'init', 'knt_schedule_auto_refresh' );

/**
 * テーマ無効化時にCronを解除
 */
function knt_unschedule_auto_refresh() {
    wp_clear_scheduled_hook( 'knt_auto_refresh_articles' );
}
add_action( 'switch_theme', 'knt_unschedule_auto_refresh' );

/**
 * 自動更新メイン処理
 */
function knt_run_auto_refresh() {
    // ON/OFFチェック
    $settings = get_option( 'knt_settings', array() );
    $enabled = isset( $settings['auto_refresh_enabled'] ) ? $settings['auto_refresh_enabled'] : '1';
    if ( $enabled === '0' || $enabled === false ) {
        return;
    }

    // 自動生成記事を取得（_knt_generated メタが true の記事）
    $posts = get_posts( array(
        'post_type'   => 'post',
        'post_status' => 'publish',
        'meta_key'    => '_knt_generated',
        'meta_value'  => '1',
        'numberposts' => -1,
        'fields'      => 'ids',
    ) );

    if ( empty( $posts ) ) return;

    require_once KNT_DIR . '/inc/article-generator.php';
    $generator = new KNT_Article_Generator();
    $updated = 0;
    $errors  = 0;

    foreach ( $posts as $post_id ) {
        // 更新間隔チェック（最終更新から25日以上経過した記事のみ）
        $last_refresh = get_post_meta( $post_id, '_knt_refreshed_at', true );
        if ( $last_refresh && strtotime( $last_refresh ) > strtotime( '-25 days' ) ) {
            continue;
        }

        // 記事のメタデータから検索条件を復元
        $area       = get_post_meta( $post_id, '_knt_area', true );
        $genre      = get_post_meta( $post_id, '_knt_genre', true );
        $scene_key  = get_post_meta( $post_id, '_knt_scene', true );
        $station    = get_post_meta( $post_id, '_knt_station', true );
        $shop_count = intval( get_post_meta( $post_id, '_knt_shop_count', true ) ) ?: 10;

        if ( ! $area ) continue;

        // カテゴリIDを取得
        $cats = wp_get_post_categories( $post_id );
        $pref_id = 0;
        $city_id = 0;
        foreach ( $cats as $cat_id ) {
            $cat = get_category( $cat_id );
            if ( $cat->parent === 0 ) {
                $pref_id = $cat_id;
            } else {
                $city_id = $cat_id;
            }
        }

        // 駅の緯度経度を取得
        $lat = '';
        $lng = '';
        if ( $station && $city_id ) {
            $stations = knt_get_stations_by_category( $city_id );
            foreach ( $stations as $st ) {
                if ( $st['name'] === $station ) {
                    $lat = $st['lat'];
                    $lng = $st['lng'];
                    break;
                }
            }
        }

        // generate_smart で再生成（allow_update = true）
        $result = $generator->generate_smart( array(
            'area'           => $area,
            'city_id'        => $city_id,
            'prefecture_id'  => $pref_id,
            'station_name'   => $station ?: '',
            'station_lat'    => $lat,
            'station_lng'    => $lng,
            'mode'           => $scene_key ? 'scene' : 'genre',
            'genre_name'     => $genre ?: 'グルメ',
            'genre_code'     => '',
            'scene'          => $scene_key ?: '',
            'count'          => $shop_count,
            'allow_update'   => true,
        ) );

        if ( is_wp_error( $result ) ) {
            $errors++;
        } else {
            $updated++;
        }

        // API負荷軽減（3秒待機）
        sleep( 3 );
    }

    // 実行ログを保存
    update_option( 'knt_last_auto_refresh', array(
        'date'    => current_time( 'mysql' ),
        'total'   => count( $posts ),
        'updated' => $updated,
        'errors'  => $errors,
    ) );
}
add_action( 'knt_auto_refresh_articles', 'knt_run_auto_refresh' );

/**
 * 管理画面に手動実行ボタン + ステータス表示を追加
 */
function knt_auto_refresh_admin_section() {
    // 手動実行
    if ( isset( $_POST['knt_manual_refresh'] ) && check_admin_referer( 'knt_manual_refresh' ) ) {
        knt_run_auto_refresh();
        echo '<div class="notice notice-success"><p>記事の自動更新を実行しました。</p></div>';
    }

    $last = get_option( 'knt_last_auto_refresh', null );
    $next = wp_next_scheduled( 'knt_auto_refresh_articles' );
    ?>
    <div style="margin-top: 24px; padding: 16px; background: #f9f9f9; border: 1px solid #dcdcde; border-radius: 6px;">
        <h3 style="margin: 0 0 12px; font-size: 14px;">記事自動更新（月1回）</h3>
        <?php if ( $last ) : ?>
            <p style="font-size: 13px; margin: 4px 0;">
                前回実行: <strong><?php echo esc_html( $last['date'] ); ?></strong>
                （対象: <?php echo esc_html( $last['total'] ); ?>件 / 更新: <?php echo esc_html( $last['updated'] ); ?>件 / エラー: <?php echo esc_html( $last['errors'] ); ?>件）
            </p>
        <?php else : ?>
            <p style="font-size: 13px; color: #646970;">まだ実行されていません</p>
        <?php endif; ?>
        <?php if ( $next ) : ?>
            <p style="font-size: 13px; margin: 4px 0;">
                次回予定: <strong><?php echo esc_html( date( 'Y-m-d H:i', $next ) ); ?></strong>
            </p>
        <?php endif; ?>
        <form method="post" style="margin-top: 12px;">
            <?php wp_nonce_field( 'knt_manual_refresh' ); ?>
            <button type="submit" name="knt_manual_refresh" class="button" onclick="return confirm('全記事の自動更新を今すぐ実行しますか？\n記事数によっては時間がかかります。');">
                今すぐ手動で更新を実行
            </button>
        </form>
    </div>
    <?php
}
