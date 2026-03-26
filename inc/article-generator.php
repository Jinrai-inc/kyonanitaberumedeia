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
        $json = wp_json_encode( array( 'type' => $type, 'title' => $title, 'content' => $content ), JSON_UNESCAPED_UNICODE );

        return "<!-- wp:knt/callout {$json} -->\n"
            . "<div class=\"knt-callout knt-callout--{$type}\">"
            . "<div class=\"knt-callout__header\"><span class=\"knt-callout__icon\">{$icon}</span>"
            . "<strong>{$title}</strong></div>"
            . "<div class=\"knt-callout__content\">{$content}</div></div>\n"
            . "<!-- /wp:knt/callout -->";
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

        return "<!-- wp:knt/restaurant-card {$json} -->\n"
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
            . '<!-- /wp:knt/restaurant-card -->';
    }

    private function block_button( $text, $url, $style = 'primary', $size = 'medium', $new_tab = true, $align = 'left' ) {
        $attrs = array( 'text' => $text, 'url' => $url, 'style' => $style, 'size' => $size, 'newTab' => $new_tab, 'align' => $align );
        $json = wp_json_encode( $attrs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
        $cls = "knt-btn knt-btn--{$style} knt-btn--{$size}";
        $target = $new_tab ? ' target="_blank" rel="noopener noreferrer sponsored"' : '';

        return "<!-- wp:knt/button {$json} -->\n"
            . "<div class=\"knt-btn-wrapper\" style=\"text-align:{$align}\">"
            . "<a class=\"{$cls}\" href=\"{$url}\"{$target}>{$text}</a></div>\n"
            . "<!-- /wp:knt/button -->";
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
        $attrs = array( 'title' => $title, 'description' => $desc, 'buttonText' => $btn_text, 'buttonUrl' => $btn_url );
        $json = wp_json_encode( $attrs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

        return "<!-- wp:knt/app-cta {$json} -->\n"
            . '<div class="knt-app-cta">'
            . '<h3 class="knt-app-cta__title">' . esc_html( $title ) . '</h3>'
            . '<p class="knt-app-cta__desc">' . esc_html( $desc ) . '</p>'
            . '<a href="' . esc_url( $btn_url ) . '" class="knt-btn knt-btn--primary knt-btn--large" target="_blank" rel="noopener noreferrer">'
            . esc_html( $btn_text ) . '</a></div>' . "\n"
            . "<!-- /wp:knt/app-cta -->";
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
}
