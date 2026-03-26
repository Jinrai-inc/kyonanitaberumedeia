<?php
/**
 * 記事自動生成 管理画面ページ
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function knt_add_generator_menu() {
    add_menu_page(
        '記事自動生成',
        '記事生成',
        'edit_posts',
        'knt-article-generator',
        'knt_render_generator_page',
        'dashicons-edit-large',
        25
    );
    add_submenu_page(
        'knt-article-generator',
        'API設定',
        'API設定',
        'manage_options',
        'knt-api-settings',
        'knt_render_api_settings_page'
    );
}
add_action( 'admin_menu', 'knt_add_generator_menu' );

function knt_generator_admin_assets( $hook ) {
    if ( strpos( $hook, 'knt-article-generator' ) === false ) {
        return;
    }
    wp_enqueue_style( 'knt-generator', KNT_URI . '/admin/generator.css', array(), KNT_VERSION );
    wp_enqueue_script( 'knt-generator', KNT_URI . '/js/admin-generator.js', array( 'jquery' ), KNT_VERSION, true );
    wp_localize_script( 'knt-generator', 'kntGenerator', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'knt_generate_article' ),
        'scenes'  => knt_scenes_for_js(),
    ) );
}
add_action( 'admin_enqueue_scripts', 'knt_generator_admin_assets' );

function knt_render_generator_page() {
    ?>
    <div class="wrap knt-generator">
        <h1>記事自動生成</h1>
        <p>エリアとジャンルを選択して「記事を生成」ボタンを押すと、<br>
        ホットペッパーAPIから店舗情報を取得し、記事を自動生成して下書き保存します。</p>

        <form id="knt-generator-form" class="knt-generator__form">
            <?php wp_nonce_field( 'knt_generate_article', 'knt_nonce' ); ?>

            <table class="form-table">
                <tr>
                    <th><label for="knt-area">エリア名</label></th>
                    <td>
                        <input type="text" id="knt-area" name="area"
                               placeholder="渋谷、新宿、横浜 等" required
                               class="regular-text">
                        <p class="description">検索キーワードとして使用されます</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="knt-mode">記事タイプ</label></th>
                    <td>
                        <select id="knt-mode" name="mode">
                            <option value="genre" selected>エリア × ジャンル（通常）</option>
                            <option value="scene">エリア × シーン（利用シーン別）</option>
                        </select>
                    </td>
                </tr>
                <tr id="knt-scene-row" style="display:none;">
                    <th><label for="knt-scene">シーン</label></th>
                    <td>
                        <select id="knt-scene" name="scene">
                            <option value="">-- シーンを選択 --</option>
                            <option value="date">デート</option>
                            <option value="nomikai">飲み会・宴会</option>
                            <option value="settai">接待・ビジネス</option>
                            <option value="joshikai">女子会</option>
                            <option value="kinenbi">記念日・誕生日</option>
                            <option value="hitorimeshi">一人飯・ソロ</option>
                            <option value="family">家族・子連れ</option>
                            <option value="goukon">合コン</option>
                            <option value="lunch">ランチ</option>
                            <option value="shinya">深夜メシ・シメ</option>
                            <option value="tabehodai">食べ放題</option>
                        </select>
                        <p class="description" id="knt-scene-description"></p>
                    </td>
                </tr>
                <tr id="knt-scene-info-row" style="display:none;">
                    <th>自動設定される条件</th>
                    <td>
                        <p><strong>検索に含むジャンル：</strong><span id="knt-allowed-genres"></span></p>
                        <p><strong>除外されるジャンル：</strong><span id="knt-excluded-genres"></span></p>
                        <p><strong>設備フィルター：</strong><span id="knt-api-filters"></span></p>
                    </td>
                </tr>
                <tr id="knt-genre-row">
                    <th><label for="knt-genre">ジャンル</label></th>
                    <td>
                        <select id="knt-genre" name="genre_code">
                            <option value="" data-name="グルメ">指定なし（キーワードで検索）</option>
                            <option value="G013" data-name="ラーメン">ラーメン</option>
                            <option value="G008" data-name="焼肉">焼肉・ホルモン</option>
                            <option value="G004" data-name="和食">和食（寿司含む）</option>
                            <option value="G001" data-name="居酒屋">居酒屋</option>
                            <option value="G015" data-name="カフェ">カフェ・スイーツ</option>
                            <option value="G006" data-name="イタリアン">イタリアン・フレンチ</option>
                            <option value="G007" data-name="中華">中華</option>
                            <option value="G012" data-name="カレー">カレー</option>
                            <option value="G009" data-name="韓国料理">韓国料理</option>
                            <option value="G010" data-name="エスニック">アジア・エスニック</option>
                            <option value="G002" data-name="ダイニングバー">ダイニングバー</option>
                            <option value="G014" data-name="お好み焼き">お好み焼き・もんじゃ</option>
                            <option value="G017" data-name="バー">バー・カクテル</option>
                            <option value="G005" data-name="洋食">洋食</option>
                        </select>
                        <input type="text" id="knt-genre-keyword" name="genre_name"
                               placeholder="ジャンル名（表示用）" class="regular-text"
                               value="グルメ">
                    </td>
                </tr>
                <tr>
                    <th><label for="knt-count">表示件数</label></th>
                    <td>
                        <select id="knt-count" name="count">
                            <option value="5" selected>5件</option>
                            <option value="10">10件</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="knt-category-1">カテゴリ</label></th>
                    <td>
                        <?php
                        wp_dropdown_categories( array(
                            'show_option_none'  => '-- カテゴリを選択 --',
                            'option_none_value' => '',
                            'hierarchical'      => true,
                            'id'                => 'knt-category-1',
                            'name'              => 'category_1',
                            'class'             => 'regular-text',
                        ) );
                        ?>
                        <br><br>
                        <?php
                        wp_dropdown_categories( array(
                            'show_option_none'  => '-- サブカテゴリ（任意） --',
                            'option_none_value' => '',
                            'hierarchical'      => true,
                            'id'                => 'knt-category-2',
                            'name'              => 'category_2',
                            'class'             => 'regular-text',
                        ) );
                        ?>
                        <p class="description">例：「東京都」と「渋谷区」のように都道府県と市区町村を選択</p>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <button type="submit" id="knt-generate-btn" class="button button-primary button-hero">
                    記事を生成する
                </button>
            </p>
        </form>

        <div id="knt-generator-result" style="display:none;">
            <div class="notice notice-success">
                <p>記事を下書きとして保存しました！</p>
                <p>
                    <a id="knt-edit-link" href="#" class="button">記事を編集する</a>
                    <a id="knt-preview-link" href="#" class="button" target="_blank">プレビューを見る</a>
                </p>
            </div>
        </div>

        <div id="knt-generator-error" style="display:none;">
            <div class="notice notice-error">
                <p>エラーが発生しました：<span id="knt-error-message"></span></p>
            </div>
        </div>

        <div id="knt-generator-loading" style="display:none;">
            <p>ホットペッパーAPIから店舗情報を取得中...</p>
            <div class="spinner is-active" style="float:none;"></div>
        </div>
    </div>
    <?php
}

function knt_render_api_settings_page() {
    if ( isset( $_POST['knt_api_key'] ) && check_admin_referer( 'knt_save_api_key' ) ) {
        update_option( 'knt_hotpepper_api_key', sanitize_text_field( $_POST['knt_api_key'] ) );
        echo '<div class="notice notice-success"><p>APIキーを保存しました。</p></div>';
    }
    $api_key = get_option( 'knt_hotpepper_api_key', '' );
    ?>
    <div class="wrap">
        <h1>API設定</h1>
        <form method="post">
            <?php wp_nonce_field( 'knt_save_api_key' ); ?>
            <table class="form-table">
                <tr>
                    <th>ホットペッパーグルメ APIキー</th>
                    <td>
                        <input type="text" name="knt_api_key"
                               value="<?php echo esc_attr( $api_key ); ?>"
                               class="regular-text">
                        <p class="description">
                            <a href="https://webservice.recruit.co.jp/register/" target="_blank">
                                リクルートWEBサービスでAPIキーを取得
                            </a>
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button( 'APIキーを保存' ); ?>
        </form>
    </div>
    <?php
}

function knt_ajax_generate_article() {
    check_ajax_referer( 'knt_generate_article', 'nonce' );

    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( '権限がありません。' );
    }

    $area  = sanitize_text_field( $_POST['area'] ?? '' );
    $mode  = sanitize_text_field( $_POST['mode'] ?? 'genre' );
    $count = intval( $_POST['count'] ?? 5 );

    $category_ids = array();
    if ( ! empty( $_POST['category_1'] ) ) $category_ids[] = intval( $_POST['category_1'] );
    if ( ! empty( $_POST['category_2'] ) ) $category_ids[] = intval( $_POST['category_2'] );

    if ( empty( $area ) ) {
        wp_send_json_error( 'エリア名を入力してください。' );
    }

    require_once KNT_DIR . '/inc/article-generator.php';
    $generator = new KNT_Article_Generator();

    if ( $mode === 'scene' ) {
        // シーン別記事生成
        $scene_key = sanitize_text_field( $_POST['scene'] ?? '' );
        if ( empty( $scene_key ) ) {
            wp_send_json_error( 'シーンを選択してください。' );
        }
        $result = $generator->generate_by_scene( $area, $scene_key, $count, $category_ids );
    } else {
        // 通常のエリア×ジャンル記事生成
        $genre_name = sanitize_text_field( $_POST['genre_name'] ?? '' );
        $genre_code = sanitize_text_field( $_POST['genre_code'] ?? '' );
        if ( empty( $genre_name ) ) {
            wp_send_json_error( 'ジャンル名を入力してください。' );
        }
        $result = $generator->generate( $area, $genre_name, $genre_code, $count, $category_ids );
    }

    if ( is_wp_error( $result ) ) {
        wp_send_json_error( $result->get_error_message() );
    }

    wp_send_json_success( array(
        'post_id'     => $result,
        'edit_url'    => get_edit_post_link( $result, '' ),
        'preview_url' => get_preview_post_link( $result ),
    ) );
}
add_action( 'wp_ajax_knt_generate_article', 'knt_ajax_generate_article' );
