<?php
/**
 * 記事自動生成クラス
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class KNT_Article_Generator {

    private $api;

    public function __construct() {
        require_once KNT_DIR . '/inc/hotpepper-api.php';
        $this->api = new KNT_HotPepper_API();
    }

    public function generate( $area, $genre_name, $genre_code = '', $count = 5, $category_ids = array() ) {
        $search_args = array(
            'keyword' => $area . ' ' . $genre_name,
            'count'   => $count,
            'order'   => 4,
        );
        if ( $genre_code ) {
            $search_args['genre'] = $genre_code;
        }

        $shops = $this->api->search_shops( $search_args );

        if ( is_wp_error( $shops ) ) {
            return $shops;
        }
        if ( empty( $shops ) ) {
            return new WP_Error( 'no_shops', '該当する店舗が見つかりませんでした。' );
        }

        $year  = date( 'Y' );
        $title = sprintf(
            '【%s年最新】%sで人気の%sおすすめ%d選｜実力店を厳選',
            $year, $area, $genre_name, count( $shops )
        );

        $slug    = $this->generate_slug( $area, $genre_name );
        $content = $this->build_content( $shops, $area, $genre_name );

        $excerpt = sprintf(
            '%sで%sを食べるならここ！ホットペッパー掲載の人気店から厳選した%d店舗を紹介。写真・アクセス・予算・営業時間つき。予約リンクあり。',
            $area, $genre_name, count( $shops )
        );

        $post_data = array(
            'post_title'   => $title,
            'post_content' => $content,
            'post_status'  => 'draft',
            'post_type'    => 'post',
            'post_name'    => $slug,
            'post_excerpt' => $excerpt,
        );
        if ( ! empty( $category_ids ) ) {
            $post_data['post_category'] = $category_ids;
        }

        $post_id = wp_insert_post( $post_data, true );
        if ( is_wp_error( $post_id ) ) {
            return $post_id;
        }

        update_post_meta( $post_id, '_knt_generated', true );
        update_post_meta( $post_id, '_knt_area', $area );
        update_post_meta( $post_id, '_knt_genre', $genre_name );
        update_post_meta( $post_id, '_knt_shop_count', count( $shops ) );
        update_post_meta( $post_id, '_knt_generated_at', current_time( 'mysql' ) );

        $this->set_featured_image( $post_id, $shops[0] );

        return $post_id;
    }

    private function build_content( $shops, $area, $genre_name ) {
        $blocks = array();

        $blocks[] = $this->block_callout( 'note', 'PR',
            'この記事にはアフィリエイト広告・PR情報が含まれます' );

        $blocks[] = $this->block_paragraph( sprintf(
            '%sで%sを食べるならここ！ホットペッパーグルメに掲載されている人気店の中から、おすすめの%d店舗を厳選しました。各店舗の写真・アクセス・予算・営業時間に加えて、予約リンクも掲載しています。',
            $area, $genre_name, count( $shops )
        ) );

        foreach ( $shops as $i => $shop ) {
            $num = $i + 1;

            $blocks[] = $this->block_heading( sprintf( '%d. %s', $num, $shop['name'] ) );
            $blocks[] = $this->block_gmap_embed( $shop['gmap_embed'] );
            $blocks[] = $this->block_restaurant_card( $shop );

            $info_parts = array();
            $info_parts[] = esc_html( $shop['address'] );
            if ( $shop['access'] ) {
                $info_parts[] = '<strong>' . esc_html( $shop['access'] ) . '</strong>';
            }
            if ( $shop['budget'] ) {
                $info_parts[] = esc_html( $shop['budget'] );
            }
            if ( $shop['open'] ) {
                $info_parts[] = esc_html( $shop['open'] );
            }
            if ( $shop['close'] ) {
                $info_parts[] = esc_html( $shop['close'] );
            }
            $blocks[] = $this->block_paragraph( implode( '<br>', $info_parts ) );

            $facilities = array();
            if ( $shop['private_room'] && $shop['private_room'] !== 'なし' ) $facilities[] = '個室あり';
            if ( $shop['free_drink'] && $shop['free_drink'] !== 'なし' )     $facilities[] = '飲み放題';
            if ( $shop['free_food'] && $shop['free_food'] !== 'なし' )       $facilities[] = '食べ放題';
            if ( $shop['lunch'] && $shop['lunch'] !== 'なし' )               $facilities[] = 'ランチあり';
            if ( $shop['wifi'] && $shop['wifi'] !== 'なし' )                 $facilities[] = 'Wi-Fi';
            if ( $shop['card'] && $shop['card'] !== '利用不可' )             $facilities[] = 'カード可';
            if ( ! empty( $facilities ) ) {
                $blocks[] = $this->block_paragraph( implode( ' / ', $facilities ) );
            }

            // ── 予約・口コミボタン群 ──

            // ホットペッパー（APIから直接取得、確実）
            $blocks[] = $this->block_button(
                'ホットペッパーで予約する', $shop['hotpepper_url'], 'primary', 'medium', true, 'left'
            );
            if ( $shop['coupon_url'] ) {
                $blocks[] = $this->block_button(
                    'クーポンを見る', $shop['coupon_url'], 'secondary', 'small', true, 'left'
                );
            }

            // 食べログ（LinkSwitchで自動アフィリエイト変換）
            $tabelog_url = sprintf(
                'https://tabelog.com/rstLst/?vs=1&sa=%s&sk=%s',
                urlencode( $area ), urlencode( $shop['name'] )
            );
            $blocks[] = $this->block_button(
                '食べログで口コミを見る', $tabelog_url, 'secondary', 'medium', true, 'left'
            );

            // 一休.comレストラン（LinkSwitchで自動アフィリエイト変換）
            $ikkyuu_url = sprintf(
                'https://restaurant.ikyu.com/search/?keyword=%s',
                urlencode( $shop['name'] )
            );
            $blocks[] = $this->block_button(
                '一休.comで予約する', $ikkyuu_url, 'secondary', 'medium', true, 'left'
            );

            // Google Maps
            $blocks[] = $this->block_button(
                'Google Mapsで見る', $shop['gmap_url'], 'secondary', 'small', true, 'left'
            );

            if ( $i < count( $shops ) - 1 ) {
                $blocks[] = $this->block_separator();
            }
        }

        $blocks[] = $this->block_heading( $area . 'の' . $genre_name . '店の選び方' );
        $blocks[] = $this->block_paragraph( sprintf(
            '%sには多くの%s店がありますが、自分の好みに合ったお店を見つけるコツは「予算」「アクセス」「雰囲気」の3つを基準にすることです。駅近でサクッと食べたい方、個室でゆっくりしたい方、深夜営業のお店を探している方など、目的に合わせて選んでみてください。',
            $area, $genre_name
        ) );

        $blocks[] = $this->block_heading( 'まとめ' );
        $blocks[] = $this->block_paragraph( sprintf(
            '今回紹介した%d店舗は、どれもホットペッパーグルメで高く評価されている人気店ばかりです。気になるお店があれば、ぜひ予約してみてください。',
            count( $shops )
        ) );
        $blocks[] = $this->block_app_cta(
            $area . 'の' . $genre_name . '店を今すぐ探す',
            '「今日何食べる？」アプリなら、現在地から近い人気店をすぐに検索できます。',
            'アプリを使ってみる',
            'https://www.kyou-nani-taberu.app/'
        );

        $blocks[] = $this->block_faq( $area, $genre_name );

        $blocks[] = $this->block_paragraph(
            '<small>店舗情報・画像提供：<a href="https://webservice.recruit.co.jp/" target="_blank" rel="noopener noreferrer">ホットペッパーグルメ Webサービス</a></small>'
        );

        return implode( "\n\n", $blocks );
    }

    private function block_paragraph( $text ) {
        return "<!-- wp:paragraph -->\n<p>{$text}</p>\n<!-- /wp:paragraph -->";
    }

    private function block_heading( $text, $level = 2 ) {
        $attrs = $level !== 2 ? ' {"level":' . $level . '}' : '';
        return "<!-- wp:heading{$attrs} -->\n<h{$level}>{$text}</h{$level}>\n<!-- /wp:heading -->";
    }

    private function block_separator() {
        return "<!-- wp:separator -->\n<hr class=\"wp-block-separator has-alpha-channel-opacity\"/>\n<!-- /wp:separator -->";
    }

    private function block_callout( $type, $title, $content ) {
        $icons = array( 'note' => "\xF0\x9F\x93\x9D", 'info' => "\xE2\x84\xB9\xEF\xB8\x8F", 'tip' => "\xF0\x9F\x92\xA1", 'warning' => "\xE2\x9A\xA0\xEF\xB8\x8F" );
        $icon = $icons[ $type ] ?? "\xF0\x9F\x93\x9D";

        return "<!-- wp:html -->\n"
            . "<div class=\"knt-callout knt-callout--{$type}\">"
            . "<div class=\"knt-callout__header\"><span class=\"knt-callout__icon\">{$icon}</span>"
            . "<strong>{$title}</strong></div>"
            . "<div class=\"knt-callout__content\">{$content}</div></div>\n"
            . "<!-- /wp:html -->";
    }

    private function block_restaurant_card( $shop ) {
        $rating = 3;
        $desc = $shop['catch'] ?: ( $shop['genre_catch'] ?: '' );

        $attrs = array(
            'name'        => $shop['name'],
            'genre'       => $shop['genre'] ?: $shop['sub_genre'],
            'area'        => $shop['station'] ? $shop['station'] . '駅' : '',
            'rating'      => $rating,
            'description' => $desc,
            'url'         => '',
            'imageUrl'    => $shop['photo_l'],
            'imageId'     => 0,
        );
        $json = wp_json_encode( $attrs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
        $stars = str_repeat( "\xE2\x98\x85", $rating ) . str_repeat( "\xE2\x98\x86", 5 - $rating );

        $image_html = '';
        if ( $shop['photo_l'] ) {
            $image_html = '<div class="knt-restaurant-card__image">'
                . '<img src="' . esc_url( $shop['photo_l'] ) . '" alt="' . esc_attr( $shop['name'] ) . '">'
                . '<small class="knt-restaurant-card__credit">画像提供：ホットペッパー グルメ</small>'
                . '</div>';
        }

        return "<!-- wp:html -->\n"
            . '<div class="knt-restaurant-card">'
            . $image_html
            . '<div class="knt-restaurant-card__body">'
            . '<div class="knt-restaurant-card__tags">'
            . '<span class="cat-tag">' . esc_html( $attrs['genre'] ) . '</span>'
            . '<span class="knt-restaurant-card__area">' . esc_html( $attrs['area'] ) . '</span>'
            . '</div>'
            . '<div class="knt-restaurant-card__name">' . esc_html( $shop['name'] ) . '</div>'
            . '<div class="knt-rating__stars">' . $stars . '</div>'
            . '<p class="knt-restaurant-card__desc">' . esc_html( $desc ) . '</p>'
            . '</div></div>' . "\n"
            . '<!-- /wp:html -->';
    }

    private function block_button( $text, $url, $style = 'primary', $size = 'medium', $new_tab = true, $align = 'left' ) {
        $cls = "knt-btn knt-btn--{$style} knt-btn--{$size}";
        $target = $new_tab ? ' target="_blank" rel="noopener noreferrer sponsored"' : '';

        return "<!-- wp:html -->\n"
            . "<div class=\"knt-btn-wrapper\" style=\"text-align:{$align}\">"
            . "<a class=\"{$cls}\" href=\"{$url}\"{$target}>{$text}</a></div>\n"
            . "<!-- /wp:html -->";
    }

    private function block_gmap_embed( $embed_url ) {
        return "<!-- wp:html -->\n"
            . '<div style="border-radius:12px;overflow:hidden;margin:16px 0;">'
            . '<iframe src="' . esc_url( $embed_url ) . '" '
            . 'width="100%" height="250" style="border:0;" '
            . 'allowfullscreen="" loading="lazy"></iframe></div>' . "\n"
            . "<!-- /wp:html -->";
    }

    private function block_app_cta( $title, $desc, $btn_text, $btn_url ) {
        return "<!-- wp:html -->\n"
            . '<div class="knt-app-cta">'
            . '<h3 class="knt-app-cta__title">' . esc_html( $title ) . '</h3>'
            . '<p class="knt-app-cta__desc">' . esc_html( $desc ) . '</p>'
            . '<a href="' . esc_url( $btn_url ) . '" class="knt-btn knt-btn--primary knt-btn--large" target="_blank" rel="noopener noreferrer">'
            . esc_html( $btn_text ) . '</a></div>' . "\n"
            . "<!-- /wp:html -->";
    }

    private function block_faq( $area, $genre_name ) {
        $items = array(
            array(
                'question' => $area . 'で予約できる' . $genre_name . '店はありますか？',
                'answer'   => 'はい、この記事で紹介しているお店はすべてホットペッパーグルメに掲載されており、ネット予約やクーポン利用が可能な店舗もあります。',
            ),
            array(
                'question' => $area . 'の' . $genre_name . 'の平均予算はいくらですか？',
                'answer'   => '各店舗の予算は記事内に記載しています。ランチであれば1,000円前後、ディナーであれば2,000〜3,000円程度が目安です。',
            ),
            array(
                'question' => '個室がある' . $genre_name . '店はありますか？',
                'answer'   => '個室のあるお店には「個室あり」の表示がついています。デートや接待など、個室が必要な場合は各店舗の詳細ページでご確認ください。',
            ),
            array(
                'question' => '深夜まで営業している' . $genre_name . '店はありますか？',
                'answer'   => '各店舗の営業時間は記事内に掲載しています。深夜営業のお店を探す場合は、ホットペッパーグルメの検索で「深夜営業」のフィルターをご利用ください。',
            ),
        );
        $json = wp_json_encode( array( 'items' => $items ), JSON_UNESCAPED_UNICODE );
        return "<!-- wp:knt/faq {$json} /-->";
    }

    private function set_featured_image( $post_id, $shop ) {
        if ( empty( $shop['photo_l'] ) ) return;

        $tmp = download_url( $shop['photo_l'] );
        if ( is_wp_error( $tmp ) ) return;

        $file_array = array(
            'name'     => sanitize_file_name( $shop['name'] . '.jpg' ),
            'tmp_name' => $tmp,
        );

        $attachment_id = media_handle_sideload( $file_array, $post_id );
        if ( is_wp_error( $attachment_id ) ) {
            @unlink( $tmp );
            return;
        }

        set_post_thumbnail( $post_id, $attachment_id );
    }

    private function generate_slug( $area, $genre_name ) {
        $area_map = array(
            '渋谷' => 'shibuya', '新宿' => 'shinjuku', '池袋' => 'ikebukuro',
            '銀座' => 'ginza', '東京駅' => 'tokyo-station', '上野' => 'ueno',
            '浅草' => 'asakusa', '秋葉原' => 'akihabara', '六本木' => 'roppongi',
            '品川' => 'shinagawa', '恵比寿' => 'ebisu', '中目黒' => 'nakameguro',
            '代官山' => 'daikanyama', '表参道' => 'omotesando', '神田' => 'kanda',
            '横浜' => 'yokohama', '川崎' => 'kawasaki', '大阪' => 'osaka',
            '梅田' => 'umeda', '難波' => 'namba', '京都' => 'kyoto',
            '名古屋' => 'nagoya', '福岡' => 'fukuoka', '札幌' => 'sapporo',
            '仙台' => 'sendai', '広島' => 'hiroshima', '神戸' => 'kobe',
        );
        $genre_map = array(
            'ラーメン' => 'ramen', '焼肉' => 'yakiniku', '寿司' => 'sushi',
            '居酒屋' => 'izakaya', 'カフェ' => 'cafe', 'イタリアン' => 'italian',
            '中華' => 'chinese', 'カレー' => 'curry', 'フレンチ' => 'french',
            '韓国料理' => 'korean', '和食' => 'washoku', 'バー' => 'bar',
            'お好み焼き' => 'okonomiyaki', '洋食' => 'yoshoku', 'エスニック' => 'ethnic',
            'ダイニングバー' => 'diningbar',
        );

        $area_slug  = $area_map[ $area ] ?? sanitize_title( $area );
        $genre_slug = $genre_map[ $genre_name ] ?? sanitize_title( $genre_name );

        return $area_slug . '-' . $genre_slug . '-osusume';
    }

    private function get_area_slug( $area ) {
        $area_map = array(
            '渋谷' => 'shibuya', '新宿' => 'shinjuku', '池袋' => 'ikebukuro',
            '銀座' => 'ginza', '東京駅' => 'tokyo-station', '上野' => 'ueno',
            '浅草' => 'asakusa', '秋葉原' => 'akihabara', '六本木' => 'roppongi',
            '品川' => 'shinagawa', '恵比寿' => 'ebisu', '中目黒' => 'nakameguro',
            '代官山' => 'daikanyama', '表参道' => 'omotesando', '神田' => 'kanda',
            '横浜' => 'yokohama', '川崎' => 'kawasaki', '大阪' => 'osaka',
            '梅田' => 'umeda', '難波' => 'namba', '京都' => 'kyoto',
            '名古屋' => 'nagoya', '福岡' => 'fukuoka', '札幌' => 'sapporo',
            '仙台' => 'sendai', '広島' => 'hiroshima', '神戸' => 'kobe',
        );
        return $area_map[ $area ] ?? sanitize_title( $area );
    }

    // ========================================
    // シーン別記事生成
    // ========================================

    public function generate_by_scene( $area, $scene_key, $count = 5, $category_ids = array() ) {
        if ( ! isset( KNT_SCENES[ $scene_key ] ) ) {
            return new WP_Error( 'invalid_scene', '無効なシーンが指定されました。' );
        }
        $scene = KNT_SCENES[ $scene_key ];

        $search_args = array(
            'keyword' => $area . ' ' . $scene['keywords'],
            'count'   => $count + 5,
            'order'   => 4,
        );
        foreach ( $scene['api_filters'] as $key => $value ) {
            $search_args[ $key ] = $value;
        }

        $shops = $this->api->search_shops( $search_args );
        if ( is_wp_error( $shops ) ) return $shops;

        $shops = $this->filter_shops_by_scene( $shops, $scene_key );
        if ( empty( $shops ) ) {
            return new WP_Error( 'no_matching_shops', 'このシーンに合致する店舗が見つかりませんでした。' );
        }
        $shops = array_slice( $shops, 0, $count );

        $year  = date( 'Y' );
        $title = sprintf( '【%s年】%sで%sにおすすめのお店%d選｜%s', $year, $area, $scene['label'], count( $shops ), $scene['title_suffix'] );
        $slug  = $this->get_area_slug( $area ) . '-' . $scene['slug'] . '-osusume';

        $content = $this->build_scene_content( $shops, $area, $scene_key );
        $excerpt = sprintf( '%sで%sにぴったりのお店を厳選！%s 予約リンクあり。', $area, $scene['label'], $scene['description'] );

        $post_data = array(
            'post_title' => $title, 'post_content' => $content, 'post_status' => 'draft',
            'post_type' => 'post', 'post_name' => $slug, 'post_excerpt' => $excerpt,
        );
        if ( ! empty( $category_ids ) ) $post_data['post_category'] = $category_ids;

        $post_id = wp_insert_post( $post_data, true );
        if ( is_wp_error( $post_id ) ) return $post_id;

        update_post_meta( $post_id, '_knt_generated', true );
        update_post_meta( $post_id, '_knt_area', $area );
        update_post_meta( $post_id, '_knt_scene', $scene_key );
        update_post_meta( $post_id, '_knt_scene_label', $scene['label'] );
        update_post_meta( $post_id, '_knt_shop_count', count( $shops ) );
        update_post_meta( $post_id, '_knt_generated_at', current_time( 'mysql' ) );

        $this->set_featured_image( $post_id, $shops[0] );
        return $post_id;
    }

    private function filter_shops_by_scene( $shops, $scene_key ) {
        $scene   = KNT_SCENES[ $scene_key ];
        $excluded_names = array_map( 'knt_genre_code_to_name', $scene['excluded_genres'] );

        $ng_keywords = $this->get_scene_ng_keywords( $scene_key );
        $filtered = array();

        foreach ( $shops as $shop ) {
            $genre = $shop['genre'] ?? '';
            $is_excluded = false;

            foreach ( $excluded_names as $exc ) {
                if ( mb_strpos( $genre, $exc ) !== false ) { $is_excluded = true; break; }
            }
            if ( ! $is_excluded ) {
                $name_lower = mb_strtolower( $shop['name'] );
                foreach ( $ng_keywords as $ng ) {
                    if ( mb_strpos( $name_lower, $ng ) !== false ) { $is_excluded = true; break; }
                }
            }
            if ( ! $is_excluded ) $filtered[] = $shop;
        }
        return $filtered;
    }

    private function get_scene_ng_keywords( $scene_key ) {
        $map = array(
            'date'    => array( 'ラーメン','らーめん','つけ麺','牛丼','立ち食い','松屋','すき家','吉野家','なか卯','日高屋','幸楽苑','天下一品','丸亀' ),
            'settai'  => array( 'ラーメン','らーめん','牛丼','立ち食い','松屋','すき家','吉野家','サイゼリヤ','ガスト','日高屋','餃子の王将' ),
            'kinenbi' => array( 'ラーメン','らーめん','牛丼','立ち食い','松屋','すき家','吉野家','日高屋','幸楽苑' ),
            'joshikai'=> array( '牛丼','立ち食い','松屋','すき家','吉野家' ),
        );
        return $map[ $scene_key ] ?? array();
    }

    private function build_scene_content( $shops, $area, $scene_key ) {
        $scene  = KNT_SCENES[ $scene_key ];
        $blocks = array();

        $blocks[] = $this->block_callout( 'note', 'PR', 'この記事にはアフィリエイト広告・PR情報が含まれます' );

        $blocks[] = $this->block_paragraph( sprintf(
            '%sで%sにぴったりのお店をお探しですか？%s この記事では、ホットペッパーグルメに掲載されている人気店の中から、%sに最適な%d店舗を厳選しました。',
            $area, $scene['label'], $scene['description'], $scene['label'], count( $shops )
        ) );

        // 選定基準
        $tips = array();
        if ( ! empty( $scene['api_filters']['private_room'] ) ) $tips[] = '個室完備のお店を優先';
        if ( ! empty( $scene['api_filters']['free_drink'] ) )   $tips[] = '飲み放題付きコースあり';
        if ( ! empty( $scene['api_filters']['free_food'] ) )    $tips[] = '食べ放題プランあり';
        if ( ! empty( $scene['api_filters']['lunch'] ) )        $tips[] = 'ランチ営業あり';
        if ( ! empty( $scene['api_filters']['midnight'] ) )     $tips[] = '深夜営業あり';
        if ( ! empty( $scene['api_filters']['child'] ) )        $tips[] = 'お子様連れOK';
        if ( ! empty( $scene['api_filters']['course'] ) )       $tips[] = 'コースあり';
        if ( ! empty( $tips ) ) {
            $blocks[] = $this->block_callout( 'info', 'この記事の選定基準', implode( ' / ', $tips ) . ' の条件で厳選しています。' );
        }

        foreach ( $shops as $i => $shop ) {
            $num = $i + 1;
            $blocks[] = $this->block_heading( sprintf( '%d. %s', $num, $shop['name'] ) );
            $blocks[] = $this->block_gmap_embed( $shop['gmap_embed'] );
            $blocks[] = $this->block_restaurant_card( $shop );

            $info = array();
            $info[] = esc_html( $shop['address'] );
            if ( $shop['access'] )  $info[] = '<strong>' . esc_html( $shop['access'] ) . '</strong>';
            if ( $shop['budget'] )  $info[] = esc_html( $shop['budget'] );
            if ( $shop['open'] )    $info[] = esc_html( $shop['open'] );
            if ( $shop['close'] )   $info[] = esc_html( $shop['close'] );
            $blocks[] = $this->block_paragraph( implode( '<br>', $info ) );

            // シーン関連設備を強調
            $feats = array();
            if ( ! empty( $scene['api_filters']['private_room'] ) && $shop['private_room'] && $shop['private_room'] !== 'なし' ) $feats[] = '個室あり';
            if ( ! empty( $scene['api_filters']['free_drink'] ) && $shop['free_drink'] && $shop['free_drink'] !== 'なし' )       $feats[] = '飲み放題あり';
            if ( ! empty( $scene['api_filters']['free_food'] ) && $shop['free_food'] && $shop['free_food'] !== 'なし' )         $feats[] = '食べ放題あり';
            if ( ! empty( $scene['api_filters']['child'] ) && $shop['child'] && $shop['child'] !== '不可' )                     $feats[] = 'お子様連れOK';
            if ( ! empty( $scene['api_filters']['lunch'] ) && $shop['lunch'] && $shop['lunch'] !== 'なし' )                     $feats[] = 'ランチあり';
            if ( ! empty( $scene['api_filters']['midnight'] ) && $shop['midnight'] && $shop['midnight'] !== 'なし' )            $feats[] = '深夜営業';
            if ( ! empty( $feats ) ) {
                $blocks[] = $this->block_paragraph( '<strong>' . implode( ' / ', $feats ) . '</strong>' );
            }

            // 予約ボタン群
            $blocks[] = $this->block_button( 'ホットペッパーで予約する', $shop['hotpepper_url'], 'primary', 'medium', true, 'left' );
            if ( $shop['coupon_url'] ) {
                $blocks[] = $this->block_button( 'クーポンを見る', $shop['coupon_url'], 'secondary', 'small', true, 'left' );
            }
            $tabelog_url = sprintf( 'https://tabelog.com/rstLst/?vs=1&sa=%s&sk=%s', urlencode( $area ), urlencode( $shop['name'] ) );
            $blocks[] = $this->block_button( '食べログで口コミを見る', $tabelog_url, 'secondary', 'medium', true, 'left' );
            $ikkyuu_url = sprintf( 'https://restaurant.ikyu.com/search/?keyword=%s', urlencode( $shop['name'] ) );
            $blocks[] = $this->block_button( '一休.comで予約する', $ikkyuu_url, 'secondary', 'medium', true, 'left' );
            $blocks[] = $this->block_button( 'Google Mapsで見る', $shop['gmap_url'], 'secondary', 'small', true, 'left' );

            if ( $i < count( $shops ) - 1 ) $blocks[] = $this->block_separator();
        }

        // 選び方アドバイス
        $blocks[] = $this->block_heading( $area . 'で' . $scene['label'] . '向きのお店の選び方' );
        $blocks[] = $this->block_paragraph( $this->get_scene_advice( $scene_key ) );

        // まとめ
        $blocks[] = $this->block_heading( 'まとめ' );
        $blocks[] = $this->block_paragraph( sprintf(
            '今回は%sで%sに使えるおすすめのお店を%d店舗ご紹介しました。気になるお店があれば予約してみてください。',
            $area, $scene['label'], count( $shops )
        ) );
        $blocks[] = $this->block_app_cta( $area . 'のお店を今すぐ探す', '「今日何食べる？」アプリなら、現在地から近い人気店をすぐに検索できます。', 'アプリを使ってみる', 'https://www.kyou-nani-taberu.app/' );

        // シーンFAQ
        $blocks[] = $this->block_scene_faq( $area, $scene_key );

        // クレジット
        $blocks[] = $this->block_paragraph( '<small>店舗情報・画像提供：<a href="https://webservice.recruit.co.jp/" target="_blank" rel="noopener noreferrer">ホットペッパーグルメ Webサービス</a></small>' );

        return implode( "\n\n", $blocks );
    }

    private function get_scene_advice( $scene_key ) {
        $advice = array(
            'date'        => 'デートのお店選びで大切なのは「雰囲気」「個室の有無」「アクセスの良さ」の3つです。初デートなら駅近で個室のあるイタリアンやフレンチが安心です。',
            'nomikai'     => '飲み会の幹事さんは「飲み放題の内容」「コースの品数」「人数対応」をチェックしましょう。大人数なら個室やフロア貸切ができるお店が便利です。',
            'settai'      => '接待では「個室の質」「料理のグレード」「アクセス」が最重要です。事前にコース内容や個室の広さを確認しておくことをおすすめします。',
            'joshikai'    => '女子会は「おしゃれな雰囲気」「コスパの良いコース」「デザートの充実度」で選びましょう。インスタ映えするメニューがあるお店は盛り上がること間違いなしです。',
            'kinenbi'     => '記念日・誕生日のお店選びは「サプライズ対応」「特別感」「個室」が決め手。メッセージプレートを用意してくれるお店がおすすめです。',
            'hitorimeshi' => '一人飯のポイントは「カウンター席の有無」「入りやすい雰囲気」「サクッと食べられるメニュー」です。',
            'family'      => 'お子様連れの場合は「座敷や個室の有無」「キッズメニュー」「ベビーカー入店可」をチェックしましょう。',
            'goukon'      => '合コンの幹事なら「個室＋飲み放題」は必須条件。席の配置も重要で、L字型やコの字型の席の方が全員と話しやすくなります。',
            'lunch'       => 'ランチ選びは「コスパ」「待ち時間」「ボリューム」がポイント。11時台か13時半以降の訪問がおすすめです。',
            'shinya'      => '深夜のお店選びは「営業時間」「ラストオーダー」を必ず確認。飲んだ後のシメなら駅近のお店が便利です。',
            'tabehodai'   => '食べ放題は「制限時間」「メニューの種類」「追加料金の有無」を事前に確認しましょう。事前予約がおすすめです。',
        );
        return $advice[ $scene_key ] ?? '';
    }

    private function block_scene_faq( $area, $scene_key ) {
        $scene = KNT_SCENES[ $scene_key ];
        $items = array(
            array(
                'question' => $area . 'で' . $scene['label'] . 'に使えるお店は予約できますか？',
                'answer'   => 'はい、この記事で紹介しているお店はすべてホットペッパーグルメに掲載されており、ネット予約が可能です。',
            ),
            array(
                'question' => $area . 'で' . $scene['label'] . '向きのお店の平均予算はいくらですか？',
                'answer'   => '各店舗の予算は記事内に記載しています。お店によって異なりますので、詳細は各店舗ページでご確認ください。',
            ),
            array(
                'question' => $scene['label'] . 'に最適なジャンルは何ですか？',
                'answer'   => 'この記事では' . implode( '・', array_slice( array_map( 'knt_genre_code_to_name', $scene['allowed_genres'] ), 0, 4 ) ) . 'などのジャンルから厳選しています。',
            ),
        );
        $json = wp_json_encode( array( 'items' => $items ), JSON_UNESCAPED_UNICODE );
        return "<!-- wp:knt/faq {$json} /-->";
    }
}
