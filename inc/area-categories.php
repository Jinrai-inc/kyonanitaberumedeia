<?php
/**
 * KNT Area Categories - 都道府県・市区町村カテゴリ一括登録
 *
 * 管理画面の「ツール > エリアカテゴリ登録」から実行。
 * 47都道府県を親カテゴリ、主要市区町村を子カテゴリとして登録。
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 管理メニューに登録
 */
function knt_area_categories_menu() {
    add_management_page(
        'エリアカテゴリ登録',
        'エリアカテゴリ登録',
        'manage_categories',
        'knt-area-categories',
        'knt_area_categories_page'
    );
}
add_action( 'admin_menu', 'knt_area_categories_menu' );

/**
 * 管理画面ページ
 */
function knt_area_categories_page() {
    // 実行ボタンが押されたら登録処理
    if ( isset( $_POST['knt_run_area_setup'] ) && check_admin_referer( 'knt_area_setup' ) ) {
        $results = knt_create_area_categories();
        echo '<div class="wrap">';
        echo '<h1>エリアカテゴリ登録結果</h1>';
        echo '<p>登録が完了しました。以下のカテゴリIDをClaudeに共有してください。</p>';
        echo '<textarea rows="40" cols="100" style="font-family:monospace;font-size:12px;" readonly>';
        echo "# エリアカテゴリ一覧（カテゴリID付き）\n\n";
        echo "| 都道府県 | カテゴリID | slug | 主要市区町村 |\n";
        echo "|---------|----------|------|------------|\n";
        foreach ( $results as $pref ) {
            $cities_str = '';
            foreach ( $pref['cities'] as $city ) {
                $cities_str .= $city['name'] . '(ID:' . $city['id'] . ') ';
            }
            echo '| ' . $pref['name'] . ' | ' . $pref['id'] . ' | ' . $pref['slug'] . ' | ' . trim( $cities_str ) . " |\n";
        }
        echo '</textarea>';
        echo '<h2>JSON形式（コピー用）</h2>';
        echo '<textarea rows="20" cols="100" style="font-family:monospace;font-size:12px;" readonly>';
        echo wp_json_encode( $results, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
        echo '</textarea>';
        echo '</div>';
        return;
    }

    // 確認画面
    echo '<div class="wrap">';
    echo '<h1>エリアカテゴリ一括登録</h1>';
    echo '<p>47都道府県を親カテゴリ、主要市区町村を子カテゴリとして登録します。</p>';
    echo '<p><strong>既に存在するカテゴリはスキップされます（安全）。</strong></p>';

    // 登録予定のカテゴリ数を表示
    $data = knt_get_area_data();
    $total_cities = 0;
    foreach ( $data as $pref ) {
        $total_cities += count( $pref['cities'] );
    }
    echo '<p>登録予定: <strong>' . count( $data ) . '</strong> 都道府県 + <strong>' . $total_cities . '</strong> 市区町村</p>';

    echo '<form method="post">';
    wp_nonce_field( 'knt_area_setup' );
    echo '<p><input type="submit" name="knt_run_area_setup" value="カテゴリを一括登録する" class="button button-primary button-hero" onclick="return confirm(\'エリアカテゴリを一括登録します。よろしいですか？\');"></p>';
    echo '</form>';
    echo '</div>';
}

/**
 * 都道府県 + 市区町村データ
 */
function knt_get_area_data() {
    // municipalities-full.json から読み込み
    $json_path = KNT_DIR . '/data/municipalities-full.json';
    if ( file_exists( $json_path ) ) {
        $raw = file_get_contents( $json_path );
        $data = json_decode( $raw, true );
        if ( is_array( $data ) ) {
            $result = array();
            foreach ( $data as $code => $pref ) {
                $result[] = array(
                    'code'   => $code,
                    'name'   => $pref['name'],
                    'cities' => isset( $pref['majorCities'] ) ? $pref['majorCities'] : ( isset( $pref['municipalities'] ) ? array_slice( $pref['municipalities'], 0, 10 ) : array() ),
                );
            }
            return $result;
        }
    }

    // フォールバック: 最小限のデータ
    return array();
}

/**
 * カテゴリ一括登録
 */
function knt_create_area_categories() {
    $data    = knt_get_area_data();
    $results = array();

    foreach ( $data as $pref ) {
        $pref_name = $pref['name'];
        $pref_slug = 'area-' . $pref['code'];

        // 親カテゴリ（都道府県）
        $existing = get_term_by( 'slug', $pref_slug, 'category' );
        if ( $existing ) {
            $pref_id = $existing->term_id;
        } else {
            $result = wp_insert_term( $pref_name, 'category', array(
                'slug'        => $pref_slug,
                'description' => $pref_name . 'のグルメ情報',
            ) );
            $pref_id = is_wp_error( $result ) ? 0 : $result['term_id'];
        }

        // 子カテゴリ（市区町村）
        $city_results = array();
        if ( $pref_id && is_array( $pref['cities'] ) ) {
            foreach ( $pref['cities'] as $city_name ) {
                $city_slug = $pref_slug . '-' . sanitize_title( $city_name );

                $existing_city = get_term_by( 'slug', $city_slug, 'category' );
                if ( $existing_city ) {
                    $city_id = $existing_city->term_id;
                } else {
                    $city_result = wp_insert_term( $city_name, 'category', array(
                        'slug'        => $city_slug,
                        'parent'      => $pref_id,
                        'description' => $pref_name . ' ' . $city_name . 'のグルメ情報',
                    ) );
                    $city_id = is_wp_error( $city_result ) ? 0 : $city_result['term_id'];
                }

                if ( $city_id ) {
                    $city_results[] = array(
                        'name' => $city_name,
                        'id'   => $city_id,
                        'slug' => $city_slug,
                    );
                }
            }
        }

        $results[] = array(
            'code'   => $pref['code'],
            'name'   => $pref_name,
            'id'     => $pref_id,
            'slug'   => $pref_slug,
            'cities' => $city_results,
        );
    }

    return $results;
}
