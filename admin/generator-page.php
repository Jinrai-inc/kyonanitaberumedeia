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
    // 駅データをカテゴリIDキーに変換（slug + name の両方で検索）
    $station_slug_data = knt_get_all_station_data();
    $station_id_data = array();
    foreach ( $station_slug_data as $slug => $stations ) {
        // slugからprefコードと市区町村名を抽出（例: area-13-千代田区 → area-13, 千代田区）
        if ( preg_match( '/^(area-\d{2})-(.+)$/', $slug, $m ) ) {
            $pref_slug = $m[1];
            $city_name = $m[2];
            // 都道府県カテゴリを取得
            $pref_term = get_term_by( 'slug', $pref_slug, 'category' );
            if ( $pref_term ) {
                // 子カテゴリから名前一致で検索
                $children = get_categories( array( 'parent' => $pref_term->term_id, 'hide_empty' => false ) );
                foreach ( $children as $child ) {
                    if ( $child->name === $city_name ) {
                        $station_id_data[ $child->term_id ] = $stations;
                        break;
                    }
                }
            }
        }
    }
    // 地方別駅グループを生成
    $region_groups = knt_get_region_station_groups();

    wp_localize_script( 'knt-generator', 'kntGenerator', array(
        'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
        'stationData'   => $station_id_data,
        'regionGroups'  => $region_groups,
        'nonce'         => wp_create_nonce( 'knt_generate_article' ),
        'scenes'        => knt_scenes_for_js(),
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
                    <th><label for="knt-area-pref">エリア</label></th>
                    <td>
                        <?php
                        // area-XX（2桁コード）の都道府県カテゴリを取得し、slug順にソート
                        $pref_cats = array();
                        foreach ( get_categories( array( 'hide_empty' => false, 'parent' => 0 ) ) as $c ) {
                            if ( preg_match( '/^area-\d{2}$/', $c->slug ) ) {
                                $pref_cats[] = $c;
                            }
                        }
                        usort( $pref_cats, function( $a, $b ) {
                            return strcmp( $a->slug, $b->slug );
                        } );
                        ?>
                        <select id="knt-area-pref" name="area_pref" class="regular-text" style="max-width:300px;">
                            <option value="">-- 都道府県を選択 --</option>
                            <?php foreach ( $pref_cats as $pc ) : ?>
                                <option value="<?php echo esc_attr( $pc->term_id ); ?>" data-name="<?php echo esc_attr( $pc->name ); ?>">
                                    <?php echo esc_html( $pc->name ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <br><br>
                        <select id="knt-area-city" name="area_city" class="regular-text" style="max-width:300px;" disabled>
                            <option value="">-- 市区町村を選択（都道府県を先に選択） --</option>
                        </select>
                        <input type="hidden" id="knt-area" name="area" value="">
                        <p class="description">選択した市区町村名がAPIの検索キーワードに使用されます</p>

                        <?php
                        // 都道府県→市区町村のJSONデータを生成
                        $city_data = array();
                        foreach ( $pref_cats as $pc ) {
                            $children = get_categories( array( 'hide_empty' => false, 'parent' => $pc->term_id ) );
                            $cities = array();
                            foreach ( $children as $ch ) {
                                $cities[] = array( 'id' => $ch->term_id, 'name' => $ch->name );
                            }
                            $city_data[ $pc->term_id ] = $cities;
                        }
                        ?>
                        <script>
                        var kntAreaCities = <?php echo wp_json_encode( $city_data ); ?>;
                        jQuery(function($) {
                            $('#knt-area-pref').on('change', function() {
                                var prefId = $(this).val();
                                var prefName = $(this).find(':selected').data('name') || '';
                                var $city = $('#knt-area-city');
                                $city.html('<option value="">-- 市区町村を選択 --</option>');

                                if (prefId && kntAreaCities[prefId]) {
                                    kntAreaCities[prefId].forEach(function(c) {
                                        $city.append('<option value="' + c.id + '" data-name="' + c.name + '">' + c.name + '</option>');
                                    });
                                    $city.prop('disabled', false);
                                } else {
                                    $city.prop('disabled', true);
                                }
                                // エリア名をhiddenに設定（都道府県名）
                                $('#knt-area').val(prefName);
                            });
                            $('#knt-area-city').on('change', function() {
                                var cityName = $(this).find(':selected').data('name') || '';
                                if (cityName) {
                                    $('#knt-area').val(cityName);
                                } else {
                                    // 市区町村未選択時は都道府県名
                                    var prefName = $('#knt-area-pref').find(':selected').data('name') || '';
                                    $('#knt-area').val(prefName);
                                }
                            });
                        });
                        </script>
                    </td>
                </tr>
                <tr>
                    <th><label for="knt-station">駅（任意）</label></th>
                    <td>
                        <select id="knt-station" name="station" class="regular-text" style="max-width:300px;">
                            <option value="">駅を選択しない（市区町村全体で検索）</option>
                        </select>
                        <input type="hidden" id="knt-station-lat" name="station_lat" value="">
                        <input type="hidden" id="knt-station-lng" name="station_lng" value="">
                        <p class="description">駅を選択すると半径1km以内で店舗を検索します</p>
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
                            <option value="5">5件</option>
                            <option value="10" selected>10件</option>
                            <option value="15">15件</option>
                            <option value="20">20件</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>カテゴリ</th>
                    <td>
                        <p class="description">上のエリア選択から自動設定されます</p>
                        <input type="hidden" id="knt-category-1" name="category_1" value="">
                        <input type="hidden" id="knt-category-2" name="category_2" value="">
                        <span id="knt-category-preview" style="color: var(--color-text-sub); font-size: 13px;"></span>
                        <script>
                        jQuery(function($) {
                            function updateCategoryPreview() {
                                var prefId = $('#knt-area-pref').val();
                                var cityId = $('#knt-area-city').val();
                                var prefName = $('#knt-area-pref').find(':selected').data('name') || '';
                                var cityName = $('#knt-area-city').find(':selected').data('name') || '';

                                $('#knt-category-1').val(prefId || '');
                                $('#knt-category-2').val(cityId || '');

                                var preview = '';
                                if (prefName) preview += prefName;
                                if (cityName) preview += ' > ' + cityName;
                                $('#knt-category-preview').text(preview ? preview : '未選択');
                            }
                            $('#knt-area-pref, #knt-area-city').on('change', updateCategoryPreview);
                        });
                        </script>
                    </td>
                </tr>
            </table>

            <p style="margin-bottom: 12px;">
                <label>
                    <input type="checkbox" id="knt-allow-update" name="allow_update" value="1">
                    <strong>既存記事を差し替え更新する</strong>
                    <span style="color:#646970; font-size:12px;">（同じスラッグ/タイトルの記事が存在する場合、内容を上書き）</span>
                </label>
            </p>
            <p class="submit">
                <button type="submit" id="knt-generate-btn" class="button button-primary button-hero">
                    記事を生成する
                </button>
                <button type="button" id="knt-bulk-btn" class="button button-secondary button-hero" style="margin-left: 12px;">
                    全市区町村で一括生成
                </button>
                <button type="button" id="knt-bulk-station-btn" class="button button-secondary button-hero" style="margin-left: 12px;">
                    選択中の市区町村の全駅で一括生成
                </button>
            </p>

            <div style="margin-top: 20px; padding: 16px; background: #f9f9f9; border: 1px solid #dcdcde; border-radius: 6px;">
                <h3 style="margin: 0 0 12px; font-size: 14px;">全国主要駅 地方別一括生成</h3>
                <p style="font-size: 12px; color: #646970; margin: 0 0 12px;">選択中のジャンル/シーンで、各地方の全駅の記事を一括生成します。</p>
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    <button type="button" class="button knt-region-bulk-btn" data-region="hokkaido_tohoku">北海道・東北</button>
                    <button type="button" class="button knt-region-bulk-btn" data-region="kanto">関東（東京以外）</button>
                    <button type="button" class="button knt-region-bulk-btn" data-region="tokyo23">東京23区</button>
                    <button type="button" class="button knt-region-bulk-btn" data-region="tokyo_tama">東京多摩</button>
                    <button type="button" class="button knt-region-bulk-btn" data-region="chubu">中部・北陸</button>
                    <button type="button" class="button knt-region-bulk-btn" data-region="kansai">関西</button>
                    <button type="button" class="button knt-region-bulk-btn" data-region="chugoku_shikoku">中国・四国</button>
                    <button type="button" class="button knt-region-bulk-btn" data-region="kyushu">九州・沖縄</button>
                </div>
            </div>
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

        <?php knt_auto_refresh_admin_section(); ?>
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
    if ( empty( $area ) ) {
        wp_send_json_error( 'エリアを選択してください。' );
    }

    require_once KNT_DIR . '/inc/article-generator.php';
    $generator = new KNT_Article_Generator();

    $result = $generator->generate_smart( array(
        'area'           => $area,
        'city_id'        => intval( $_POST['category_2'] ?? $_POST['city_id'] ?? 0 ),
        'prefecture_id'  => intval( $_POST['category_1'] ?? $_POST['pref_id'] ?? 0 ),
        'station_name'   => sanitize_text_field( $_POST['station_name'] ?? $_POST['station'] ?? '' ),
        'station_lat'    => sanitize_text_field( $_POST['station_lat'] ?? '' ),
        'station_lng'    => sanitize_text_field( $_POST['station_lng'] ?? '' ),
        'mode'           => sanitize_text_field( $_POST['mode'] ?? 'genre' ),
        'genre_name'     => sanitize_text_field( $_POST['genre_name'] ?? 'グルメ' ),
        'genre_code'     => sanitize_text_field( $_POST['genre_code'] ?? '' ),
        'scene'          => sanitize_text_field( $_POST['scene'] ?? '' ),
        'count'          => intval( $_POST['count'] ?? 5 ),
        'allow_update'   => ! empty( $_POST['allow_update'] ),
    ) );

    if ( is_wp_error( $result ) ) {
        wp_send_json_error( $result->get_error_message() );
    }

    // プレビューURL: 下書きは ?p=ID&preview=true で直接アクセス
    $preview_url = add_query_arg( array( 'p' => $result, 'preview' => 'true' ), home_url( '/' ) );

    wp_send_json_success( array(
        'post_id'     => $result,
        'edit_url'    => admin_url( 'post.php?post=' . $result . '&action=edit' ),
        'preview_url' => $preview_url,
        'title'       => get_the_title( $result ),
        'area'        => $area,
    ) );
}
add_action( 'wp_ajax_knt_generate_article', 'knt_ajax_generate_article' );

/**
 * AJAX: 一括記事生成（1市区町村ずつ呼ばれる）
 */
function knt_ajax_generate_bulk() {
    check_ajax_referer( 'knt_generate_article', 'nonce' );
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( '権限がありません。' );
    }

    $area       = sanitize_text_field( $_POST['area'] ?? '' );
    $city_id    = intval( $_POST['city_id'] ?? 0 );
    $pref_id    = intval( $_POST['pref_id'] ?? 0 );
    $mode       = sanitize_text_field( $_POST['mode'] ?? 'genre' );
    $genre_name = sanitize_text_field( $_POST['genre_name'] ?? 'グルメ' );
    $genre_code = sanitize_text_field( $_POST['genre_code'] ?? '' );
    $scene_key  = sanitize_text_field( $_POST['scene'] ?? '' );
    $count      = intval( $_POST['count'] ?? 5 );

    if ( empty( $area ) ) {
        wp_send_json_error( 'エリア名が空です。' );
    }

    $category_ids = array();
    if ( $pref_id ) $category_ids[] = $pref_id;
    if ( $city_id ) $category_ids[] = $city_id;

    require_once KNT_DIR . '/inc/article-generator.php';
    $generator = new KNT_Article_Generator();

    $result = $generator->generate_smart( array(
        'area'           => $area,
        'city_id'        => $city_id,
        'prefecture_id'  => $pref_id,
        'station_name'   => sanitize_text_field( $_POST['station_name'] ?? '' ),
        'station_lat'    => sanitize_text_field( $_POST['station_lat'] ?? '' ),
        'station_lng'    => sanitize_text_field( $_POST['station_lng'] ?? '' ),
        'mode'           => $mode,
        'genre_name'     => $genre_name,
        'genre_code'     => $genre_code,
        'scene'          => $scene_key,
        'count'          => $count,
        'allow_update'   => ! empty( $_POST['allow_update'] ),
    ) );

    if ( is_wp_error( $result ) ) {
        wp_send_json_error( $area . ': ' . $result->get_error_message() );
    }

    wp_send_json_success( array(
        'post_id' => $result,
        'area'    => $area,
        'title'   => get_the_title( $result ),
    ) );
}
add_action( 'wp_ajax_knt_generate_bulk', 'knt_ajax_generate_bulk' );

/**
 * 地方別駅グループ定義
 */
function knt_get_region_station_groups() {
    $all = knt_get_all_station_data();
    $regions = array(
        'hokkaido_tohoku' => array( 'area-01','area-02','area-03','area-04','area-05','area-06','area-07' ),
        'kanto'           => array( 'area-08','area-09','area-10','area-11','area-12','area-14' ),
        'tokyo23'         => array( 'area-13' ),
        'tokyo_tama'      => array( 'area-13' ),
        'chubu'           => array( 'area-15','area-16','area-17','area-18','area-19','area-20','area-21','area-22','area-23','area-24' ),
        'kansai'          => array( 'area-25','area-26','area-27','area-28','area-29','area-30' ),
        'chugoku_shikoku' => array( 'area-31','area-32','area-33','area-34','area-35','area-36','area-37','area-38','area-39' ),
        'kyushu'          => array( 'area-40','area-41','area-42','area-43','area-44','area-45','area-46','area-47' ),
    );

    // 東京23区の区名リスト
    $tokyo23_names = array('千代田区','中央区','港区','新宿区','文京区','台東区','墨田区','江東区','品川区','目黒区','大田区','世田谷区','渋谷区','中野区','杉並区','豊島区','北区','荒川区','板橋区','練馬区','足立区','葛飾区','江戸川区');

    $result = array();
    foreach ( $regions as $region_key => $pref_codes ) {
        $stations = array();
        foreach ( $all as $slug => $station_list ) {
            // slugからprefコードを抽出
            if ( preg_match( '/^(area-\d{2})-(.+)$/', $slug, $m ) ) {
                $pref = $m[1];
                $city = $m[2];
                if ( ! in_array( $pref, $pref_codes ) ) continue;

                // 東京の場合、23区と多摩を分離
                if ( $pref === 'area-13' ) {
                    $is_23ku = in_array( $city, $tokyo23_names );
                    if ( $region_key === 'tokyo23' && ! $is_23ku ) continue;
                    if ( $region_key === 'tokyo_tama' && $is_23ku ) continue;
                    if ( $region_key !== 'tokyo23' && $region_key !== 'tokyo_tama' ) continue;
                }

                // カテゴリIDを取得
                $pref_term = get_term_by( 'slug', $pref, 'category' );
                $city_term_id = 0;
                $pref_term_id = $pref_term ? $pref_term->term_id : 0;
                if ( $pref_term ) {
                    $children = get_categories( array( 'parent' => $pref_term->term_id, 'hide_empty' => false ) );
                    foreach ( $children as $ch ) {
                        if ( $ch->name === $city ) { $city_term_id = $ch->term_id; break; }
                    }
                }

                foreach ( $station_list as $st ) {
                    $stations[] = array(
                        'name'     => $st['name'],
                        'lat'      => $st['lat'],
                        'lng'      => $st['lng'],
                        'city'     => $city,
                        'city_id'  => $city_term_id,
                        'pref_id'  => $pref_term_id,
                    );
                }
            }
        }
        $result[ $region_key ] = $stations;
    }
    return $result;
}
