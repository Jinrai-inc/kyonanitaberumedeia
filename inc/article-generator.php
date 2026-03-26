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

    /**
     * スラッグまたはタイトルで重複チェック
     */
    private function check_duplicate( $slug, $title ) {
        // スラッグで検索
        $existing = get_page_by_path( $slug, OBJECT, 'post' );
        if ( $existing ) {
            return new WP_Error( 'duplicate_post',
                sprintf( '同じスラッグの記事が既に存在します（ID: %d「%s」）。スキップしました。',
                    $existing->ID, $existing->post_title )
            );
        }
        // タイトルで検索（下書き含む）
        $title_check = get_posts( array(
            'post_type'   => 'post',
            'post_status' => array( 'publish', 'draft', 'pending' ),
            'title'       => $title,
            'numberposts' => 1,
        ) );
        if ( ! empty( $title_check ) ) {
            return new WP_Error( 'duplicate_post',
                sprintf( '同じタイトルの記事が既に存在します（ID: %d）。スキップしました。',
                    $title_check[0]->ID )
            );
        }
        return true;
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

        // 重複チェック
        $dup = $this->check_duplicate( $slug, $title );
        if ( is_wp_error( $dup ) ) return $dup;

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
        $this->auto_set_tags( $post_id, array( 'genre' => $genre_name, 'area' => $area ) );

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
            $blocks[] = $this->block_shop_image( $shop );
            $blocks[] = $this->block_restaurant_card( $shop );
            $blocks[] = $this->block_gmap_embed( $shop['gmap_embed'] );

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
            $blocks[] = $this->block_button( 'Google Mapsで見る', $shop['gmap_url'], 'secondary', 'small', true, 'left' );

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

        $blocks[] = $this->block_heading( $area . 'の' . $genre_name . 'に関するよくある質問' );
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

    // ========================================
    // Phase 5: 充実化ブロック群
    // ========================================

    private function block_shop_image( $shop ) {
        $photo = $shop['photo_l'] ?: ( $shop['photo_m'] ?: $shop['photo_mobile'] );
        if ( ! $photo ) return '';
        return "<!-- wp:html -->\n"
            . '<div class="knt-shop-photo">'
            . '<img src="' . esc_url( $photo ) . '" alt="' . esc_attr( $shop['name'] ) . '" loading="lazy">'
            . '<span class="knt-shop-photo__credit">画像提供：ホットペッパー グルメ</span>'
            . '</div>' . "\n<!-- /wp:html -->";
    }

    private function build_heading_text( $shop, $index ) {
        $h = sprintf( '%d. %s', $index, $shop['name'] );
        $sub = $shop['genre_catch'] ?: mb_substr( $shop['catch'] ?? '', 0, 30 );
        if ( $sub ) $h .= '｜' . $sub;
        return $h;
    }

    private function block_shop_info_table( $shop ) {
        $rows = '';
        $fields = array(
            array( '住所', $shop['address'] ),
            array( 'アクセス', $shop['access'] ),
            array( '予算', $shop['budget_avg'] ?: $shop['budget'] ),
            array( '営業時間', $shop['open'] ),
            array( '定休日', $shop['close'] ),
            array( '席数', $shop['capacity'] ? $shop['capacity'] . '席' : '' ),
            array( '駐車場', $shop['parking'] ),
        );
        foreach ( $fields as $f ) {
            if ( ! empty( $f[1] ) && $f[1] !== 'なし' ) {
                $rows .= '<tr><th>' . esc_html( $f[0] ) . '</th><td>' . esc_html( $f[1] ) . '</td></tr>';
            }
        }
        if ( ! $rows ) return '';
        return "<!-- wp:html -->\n<div class=\"knt-shop-info\"><table class=\"knt-shop-info__table\">{$rows}</table></div>\n<!-- /wp:html -->";
    }

    private function build_facility_badges_block( $shop, $scene_key = '' ) {
        $badges = $this->build_facility_badges( $shop, $scene_key );
        if ( empty( $badges ) ) return '';
        return "<!-- wp:html -->\n<div class=\"knt-facility-badges\">" . implode( ' / ', $badges ) . "</div>\n<!-- /wp:html -->";
    }

    private function block_special_callout( $shop ) {
        if ( empty( $shop['specials'] ) || empty( $shop['specials'][0]['title'] ) ) return '';
        return $this->block_callout( 'tip', 'おすすめポイント', esc_html( $shop['specials'][0]['title'] ) );
    }

    private function build_editorial_comment( $shop, $index ) {
        $parts = array();
        if ( $shop['access'] ) $parts[] = $shop['access'] . 'の好立地';
        if ( $shop['genre_catch'] ) $parts[] = $shop['genre_catch'] . 'が評判';

        $hl = array();
        if ( $this->has_value( $shop, 'private_room' ) ) $hl[] = '個室もあるのでゆっくり食事が楽しめます';
        if ( $this->has_value( $shop, 'free_drink' ) )   $hl[] = '飲み放題付きコースもあり宴会にも◎';
        if ( $this->has_value( $shop, 'lunch' ) && $this->has_value( $shop, 'midnight' ) )
            $hl[] = 'ランチから深夜まで営業で使い勝手抜群';
        elseif ( $this->has_value( $shop, 'lunch' ) )    $hl[] = 'ランチ営業もしているのでお昼にもおすすめ';
        elseif ( $this->has_value( $shop, 'midnight' ) ) $hl[] = '深夜まで営業で飲み会の後にも';
        if ( $this->has_value( $shop, 'night_view' ) )   $hl[] = '夜景が見える席がありデートにもぴったり';
        if ( $this->has_value( $shop, 'child' ) )        $hl[] = 'お子様連れでも安心して利用可能';
        if ( ! empty( $hl ) ) $parts[] = implode( '。', array_slice( $hl, 0, 2 ) );

        if ( $shop['budget_avg'] ) $parts[] = '予算は' . $shop['budget_avg'] . '程度';
        if ( $shop['capacity'] && intval( $shop['capacity'] ) > 50 )
            $parts[] = '全' . $shop['capacity'] . '席の広々とした店内';
        if ( $shop['party_capacity'] && intval( $shop['party_capacity'] ) > 20 )
            $parts[] = '最大' . $shop['party_capacity'] . '名までの宴会にも対応';

        if ( empty( $parts ) ) return '';
        $closings = array( 'ぜひ一度足を運んでみてください。', '気になる方はホットペッパーからチェックしてみてください。', '予約してから訪れるのがおすすめです。' );
        $text = implode( '。', $parts ) . '。' . $closings[ array_rand( $closings ) ];
        return $this->block_paragraph( $text );
    }

    private function block_gmap_accordion( $shop ) {
        return "<!-- wp:html -->\n"
            . '<details class="knt-map-accordion">'
            . '<summary>地図を見る（タップで開く）</summary>'
            . '<div class="knt-map-accordion__content">'
            . '<iframe src="' . esc_url( $shop['gmap_embed'] ) . '" width="100%" height="200" style="border:0;" loading="lazy" allowfullscreen></iframe>'
            . '</div></details>' . "\n<!-- /wp:html -->";
    }

    private function block_main_cta( $shop ) {
        return "<!-- wp:html -->\n"
            . '<div class="knt-btn-wrapper" style="text-align:center">'
            . '<a class="knt-btn knt-btn--primary knt-btn--large" href="' . esc_url( $shop['hotpepper_url'] ) . '" target="_blank" rel="noopener noreferrer sponsored">'
            . 'ホットペッパーで予約・詳細を見る</a></div>' . "\n<!-- /wp:html -->";
    }

    private function block_shop_sublinks( $shop, $area = '' ) {
        $links = array();
        if ( $shop['coupon_url'] ) {
            $links[] = '<a href="' . esc_url( $shop['coupon_url'] ) . '" target="_blank" rel="noopener noreferrer sponsored">&#x1F3AB; クーポンを使う</a>';
        }
        $links[] = '<a href="' . esc_url( $shop['gmap_url'] ) . '" target="_blank" rel="noopener noreferrer">&#x1F4CD; Google Maps</a>';
        return "<!-- wp:html -->\n<div class=\"knt-shop-sublinks\">" . implode( '', $links ) . "</div>\n<!-- /wp:html -->";
    }

    private function block_genre_divider( $genre ) {
        $icons = array(
            'ラーメン' => "\xF0\x9F\x8D\x9C", "\xE7\x84\xBC\xE8\x82\x89" => "\xF0\x9F\xA5\xA9",
            '和食' => "\xF0\x9F\x8D\xA3", '中華' => "\xF0\x9F\xA5\x9F",
            'イタリアン' => "\xF0\x9F\x8D\x9D", 'カフェ' => "\xE2\x98\x95",
            'カレー' => "\xF0\x9F\x8D\x9B", '居酒屋' => "\xF0\x9F\x8D\xBA",
        );
        $icon = "\xF0\x9F\x8D\xBD"; // default fork+knife
        foreach ( $icons as $k => $v ) {
            if ( mb_strpos( $genre, $k ) !== false ) { $icon = $v; break; }
        }
        return "<!-- wp:html -->\n<div class=\"knt-shop-divider\"><span class=\"knt-shop-divider__icon\">{$icon}</span></div>\n<!-- /wp:html -->";
    }

    private function block_summary_table( $shops ) {
        $rows = '';
        foreach ( $shops as $i => $shop ) {
            $rows .= sprintf( '<tr><td>%d</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
                $i + 1, esc_html( $shop['name'] ), esc_html( $shop['genre'] ?: $shop['sub_genre'] ),
                esc_html( $shop['budget'] ?: $shop['budget_avg'] ),
                esc_html( $shop['station'] ? $shop['station'] . '駅' : '' )
            );
        }
        return "<!-- wp:html -->\n<div class=\"knt-summary-table\"><table>"
            . '<thead><tr><th>#</th><th>店名</th><th>ジャンル</th><th>予算</th><th>最寄駅</th></tr></thead>'
            . '<tbody>' . $rows . '</tbody></table></div>' . "\n<!-- /wp:html -->";
    }

    private function block_article_overview( $area, $genre, $count ) {
        $content = sprintf(
            '%sで人気の%s店%d選（写真・住所・営業時間つき）<br>'
            . '各店舗の特徴・おすすめポイント<br>'
            . 'ネット予約リンク・クーポン情報<br>'
            . 'Google Mapsへのリンクでアクセスも簡単',
            esc_html( $area ), esc_html( $genre ), $count
        );
        return $this->block_callout( 'info', 'この記事でわかること', $content );
    }
    private function block_restaurant_card( $shop ) {
        $desc  = $shop['catch'] ?: ( $shop['genre_catch'] ?: '' );
        $genre = $shop['genre'] ?: $shop['sub_genre'];
        $area  = $shop['station'] ? $shop['station'] . '駅' : '';

        return "<!-- wp:html -->\n"
            . '<div class="knt-restaurant-card knt-restaurant-card--info">'
            . '<div class="knt-restaurant-card__body">'
            . '<div class="knt-restaurant-card__tags">'
            . '<span class="cat-tag">' . esc_html( $genre ) . '</span>'
            . '<span class="knt-restaurant-card__area">' . esc_html( $area ) . '</span>'
            . '</div>'
            . '<div class="knt-restaurant-card__name">' . esc_html( $shop['name'] ) . '</div>'
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

    /**
     * 自動タグ付け（駅名・シーン・ジャンル）
     */
    private function has_value( $shop, $key ) {
        $v = $shop[ $key ] ?? '';
        return $v && $v !== 'なし' && $v !== '利用不可' && $v !== '不可' && $v !== '';
    }

    private function build_shop_description( $shop ) {
        $parts = array();
        if ( ! empty( $shop['catch'] ) ) $parts[] = $shop['catch'];
        if ( ! empty( $shop['specials'] ) ) {
            foreach ( $shop['specials'] as $sp ) {
                if ( ! empty( $sp['title'] ) && $sp['title'] !== ( $shop['catch'] ?? '' ) ) {
                    $parts[] = $sp['title'];
                    break;
                }
            }
        }
        if ( empty( $parts ) && ! empty( $shop['genre_catch'] ) ) $parts[] = $shop['genre_catch'];
        if ( ! empty( $shop['budget_avg'] ) ) $parts[] = $shop['budget_avg'];
        return implode( '。', array_filter( $parts ) );
    }

    private function build_facility_badges( $shop, $scene_key = '' ) {
        $all_badges = array(
            'private_room' => '個室', 'free_drink' => '飲み放題', 'free_food' => '食べ放題',
            'horigotatsu' => '掘りごたつ', 'tatami' => '座敷', 'night_view' => '夜景',
            'open_air' => 'テラス', 'lunch' => 'ランチ', 'midnight' => '深夜営業',
            'english' => '英語OK', 'child' => '子連れOK', 'wifi' => 'Wi-Fi',
            'parking' => '駐車場', 'card' => 'カード可', 'non_smoking' => '禁煙',
            'sommelier' => 'ソムリエ', 'charter' => '貸切可', 'karaoke' => 'カラオケ',
        );
        $highlight = array(
            'date' => array('private_room','night_view','open_air','sommelier','wine'),
            'nomikai' => array('free_drink','party_capacity','charter','karaoke','private_room'),
            'settai' => array('private_room','horigotatsu','tatami','sake','card'),
            'joshikai' => array('private_room','cocktail','wine','non_smoking'),
            'kinenbi' => array('private_room','night_view','sommelier','wine'),
            'hitorimeshi' => array('lunch','wifi','non_smoking','card'),
            'family' => array('child','tatami','horigotatsu','parking','barrier_free'),
            'goukon' => array('private_room','free_drink','karaoke','charter'),
            'lunch' => array('lunch','non_smoking','wifi'),
            'shinya' => array('midnight','card'),
            'tabehodai' => array('free_food','free_drink','private_room'),
        );
        $scene_fields = $highlight[ $scene_key ] ?? array();
        $badges = array();
        foreach ( $all_badges as $key => $label ) {
            if ( $this->has_value( $shop, $key ) ) {
                $is_highlight = in_array( $key, $scene_fields );
                $badges[] = $is_highlight ? '<strong>' . $label . '</strong>' : $label;
            }
        }
        if ( $shop['party_capacity'] && in_array( 'party_capacity', $scene_fields ) ) {
            $badges[] = '<strong>最大宴会' . esc_html( $shop['party_capacity'] ) . '名</strong>';
        }
        return $badges;
    }

    private function auto_set_tags( $post_id, $params = array() ) {
        $tags = array();

        // 駅名タグ
        if ( ! empty( $params['station'] ) ) {
            $tags[] = $params['station'];
        }

        // ジャンル名タグ
        if ( ! empty( $params['genre'] ) && $params['genre'] !== 'グルメ' ) {
            $tags[] = $params['genre'];
        }

        // シーン名タグ + 連想ジャンルタグ
        if ( ! empty( $params['scene_key'] ) && isset( KNT_SCENES[ $params['scene_key'] ] ) ) {
            $scene = KNT_SCENES[ $params['scene_key'] ];
            $tags[] = $scene['label'];
            // シーンに紐づくジャンル名もタグに追加（連想タグ）
            $scene_genre_tags = array(
                'date'        => array( 'デート', 'イタリアン', 'フレンチ', '個室' ),
                'nomikai'     => array( '飲み会', '居酒屋', '飲み放題' ),
                'settai'      => array( '接待', '和食', '個室', '高級' ),
                'joshikai'    => array( '女子会', 'カフェ', 'おしゃれ' ),
                'kinenbi'     => array( '記念日', '誕生日', 'サプライズ' ),
                'hitorimeshi' => array( '一人飯', 'ラーメン', 'カレー' ),
                'family'      => array( '子連れ', 'ファミリー', 'キッズ' ),
                'goukon'      => array( '合コン', '個室', '飲み放題' ),
                'lunch'       => array( 'ランチ', 'コスパ' ),
                'shinya'      => array( '深夜', 'シメ', 'ラーメン' ),
                'tabehodai'   => array( '食べ放題', '焼肉', 'ビュッフェ' ),
            );
            if ( isset( $scene_genre_tags[ $params['scene_key'] ] ) ) {
                $tags = array_merge( $tags, $scene_genre_tags[ $params['scene_key'] ] );
            }
        }

        // エリア名タグ
        if ( ! empty( $params['area'] ) ) {
            $tags[] = $params['area'];
        }

        if ( ! empty( $tags ) ) {
            wp_set_post_tags( $post_id, $tags, true );
        }
    }

    /**
     * 日本語→ローマ字変換マスター
     */
    private static $romaji_map = array(
        // エリア・都道府県・市区町村
        '北海道' => 'hokkaido', '青森' => 'aomori', '岩手' => 'iwate', '宮城' => 'miyagi',
        '秋田' => 'akita', '山形' => 'yamagata', '福島' => 'fukushima',
        '茨城' => 'ibaraki', '栃木' => 'tochigi', '群馬' => 'gunma',
        '埼玉' => 'saitama', 'さいたま' => 'saitama', '千葉' => 'chiba',
        '東京' => 'tokyo', '神奈川' => 'kanagawa', '新潟' => 'niigata',
        '富山' => 'toyama', '石川' => 'ishikawa', '福井' => 'fukui',
        '山梨' => 'yamanashi', '長野' => 'nagano', '岐阜' => 'gifu',
        '静岡' => 'shizuoka', '愛知' => 'aichi', '三重' => 'mie',
        '滋賀' => 'shiga', '京都' => 'kyoto', '大阪' => 'osaka',
        '兵庫' => 'hyogo', '奈良' => 'nara', '和歌山' => 'wakayama',
        '鳥取' => 'tottori', '島根' => 'shimane', '岡山' => 'okayama',
        '広島' => 'hiroshima', '山口' => 'yamaguchi', '徳島' => 'tokushima',
        '香川' => 'kagawa', '愛媛' => 'ehime', '高知' => 'kochi',
        '福岡' => 'fukuoka', '佐賀' => 'saga', '長崎' => 'nagasaki',
        '熊本' => 'kumamoto', '大分' => 'oita', '宮崎' => 'miyazaki',
        '鹿児島' => 'kagoshima', '沖縄' => 'okinawa',
        // 主要エリア・駅
        '渋谷' => 'shibuya', '新宿' => 'shinjuku', '池袋' => 'ikebukuro',
        '銀座' => 'ginza', '上野' => 'ueno', '浅草' => 'asakusa',
        '秋葉原' => 'akihabara', '六本木' => 'roppongi', '品川' => 'shinagawa',
        '恵比寿' => 'ebisu', '中目黒' => 'nakameguro', '代官山' => 'daikanyama',
        '表参道' => 'omotesando', '神田' => 'kanda', '横浜' => 'yokohama',
        '川崎' => 'kawasaki', '梅田' => 'umeda', '難波' => 'namba',
        '名古屋' => 'nagoya', '札幌' => 'sapporo', '仙台' => 'sendai', '神戸' => 'kobe',
        '東京駅' => 'tokyo-eki', '新橋' => 'shimbashi', '赤坂' => 'akasaka',
        '麻布十番' => 'azabujuban', '原宿' => 'harajuku', '神泉' => 'shinsen',
        '高田馬場' => 'takadanobaba', '神楽坂' => 'kagurazaka', '新大久保' => 'shinokubo',
        '錦糸町' => 'kinshicho', '豊洲' => 'toyosu', '門前仲町' => 'monzennakacho',
        '亀戸' => 'kameido', '大井町' => 'oimachi', '五反田' => 'gotanda',
        '目黒' => 'meguro', '自由が丘' => 'jiyugaoka', '学芸大学' => 'gakugeidaigaku',
        '蒲田' => 'kamata', '大森' => 'omori', '三軒茶屋' => 'sangenjaya',
        '下北沢' => 'shimokitazawa', '二子玉川' => 'futakotamagawa',
        '成城学園前' => 'seijogakuenmae', '中野' => 'nakano',
        '荻窪' => 'ogikubo', '高円寺' => 'koenji', '阿佐ヶ谷' => 'asagaya',
        '大塚' => 'otsuka', '巣鴨' => 'sugamo', '赤羽' => 'akabane',
        '王子' => 'oji', '十条' => 'jujo', '北千住' => 'kitasenju',
        '西新井' => 'nishiarai', '竹ノ塚' => 'takenotsuka',
        '後楽園' => 'korakuen', '本郷三丁目' => 'hongosanchome',
        '御茶ノ水' => 'ochanomizu', '神保町' => 'jimbocho', '大手町' => 'otemachi',
        '日本橋' => 'nihonbashi', '築地' => 'tsukiji', '人形町' => 'ningyocho',
        '月島' => 'tsukishima', '御徒町' => 'okachimachi',
        '八王子' => 'hachioji', '立川' => 'tachikawa', '吉祥寺' => 'kichijoji', '町田' => 'machida',
        '関内' => 'kannai', '桜木町' => 'sakuragicho', '新横浜' => 'shinyokohama',
        '中華街' => 'chinatown', '武蔵小杉' => 'musashikosugi', '溝の口' => 'mizonokuchi',
        '鎌倉' => 'kamakura', '藤沢' => 'fujisawa',
        '心斎橋' => 'shinsaibashi', '天王寺' => 'tennoji', '新大阪' => 'shinosaka',
        // 区
        '千代田区' => 'chiyoda', '中央区' => 'chuo', '港区' => 'minato',
        '新宿区' => 'shinjuku', '文京区' => 'bunkyo', '台東区' => 'taito',
        '墨田区' => 'sumida', '江東区' => 'koto', '品川区' => 'shinagawa',
        '目黒区' => 'meguro', '大田区' => 'ota', '世田谷区' => 'setagaya',
        '渋谷区' => 'shibuya', '中野区' => 'nakano', '杉並区' => 'suginami',
        '豊島区' => 'toshima', '北区' => 'kita', '荒川区' => 'arakawa',
        '板橋区' => 'itabashi', '練馬区' => 'nerima', '足立区' => 'adachi',
        '葛飾区' => 'katsushika', '江戸川区' => 'edogawa',
        // ジャンル
        'ラーメン' => 'ramen', '焼肉' => 'yakiniku', '寿司' => 'sushi',
        '居酒屋' => 'izakaya', 'カフェ' => 'cafe', 'イタリアン' => 'italian',
        '中華' => 'chinese', 'カレー' => 'curry', 'フレンチ' => 'french',
        '韓国料理' => 'korean', '和食' => 'washoku', 'バー' => 'bar',
        'お好み焼き' => 'okonomiyaki', '洋食' => 'yoshoku', 'エスニック' => 'ethnic',
        'ダイニングバー' => 'diningbar', 'グルメ' => 'gourmet',
        'カフェ・スイーツ' => 'cafe', 'イタリアン・フレンチ' => 'italian-french',
        '焼肉・ホルモン' => 'yakiniku', 'アジア・エスニック' => 'asian',
        'バー・カクテル' => 'bar',
    );

    private function to_romaji( $text ) {
        // 完全一致
        if ( isset( self::$romaji_map[ $text ] ) ) {
            return self::$romaji_map[ $text ];
        }
        // 「駅」を除去して再検索
        $no_eki = str_replace( array( '駅', '区', '市', '県', '都', '府' ), '', $text );
        if ( isset( self::$romaji_map[ $no_eki ] ) ) {
            return self::$romaji_map[ $no_eki ];
        }
        // 部分一致（長い方から順にマッチ）
        $sorted = self::$romaji_map;
        uksort( $sorted, function($a, $b) { return mb_strlen($b) - mb_strlen($a); });
        foreach ( $sorted as $jp => $en ) {
            if ( mb_strpos( $text, $jp ) !== false ) {
                return $en;
            }
        }
        // フォールバック: ランダムID
        return 'area-' . substr( md5( $text ), 0, 8 );
    }

    private function generate_slug( $area, $genre_name ) {
        return $this->to_romaji( $area ) . '-' . $this->to_romaji( $genre_name ) . '-osusume';
    }

    private function get_area_slug( $area ) {
        return $this->to_romaji( $area );
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

        // 重複チェック
        $dup = $this->check_duplicate( $slug, $title );
        if ( is_wp_error( $dup ) ) return $dup;

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
        $this->auto_set_tags( $post_id, array( 'scene_key' => $scene_key, 'area' => $area ) );
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
            $blocks[] = $this->block_shop_image( $shop );
            $blocks[] = $this->block_restaurant_card( $shop );
            $blocks[] = $this->block_gmap_embed( $shop['gmap_embed'] );

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

            // 予約ボタン（ホットペッパーのみ）
            $blocks[] = $this->block_button( 'ホットペッパーで予約する', $shop['hotpepper_url'], 'primary', 'medium', true, 'left' );
            if ( $shop['coupon_url'] ) {
                $blocks[] = $this->block_button( 'クーポンを見る', $shop['coupon_url'], 'secondary', 'small', true, 'left' );
            }
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

    // ========================================
    // Phase 3: 駅ベース検索 + リード文強化
    // ========================================

    public function generate_smart( $params ) {
        $area       = $params['area'] ?? '';
        $city_id    = intval( $params['city_id'] ?? 0 );
        $pref_id    = intval( $params['prefecture_id'] ?? 0 );
        $station    = $params['station_name'] ?? '';
        $lat        = $params['station_lat'] ?? '';
        $lng        = $params['station_lng'] ?? '';
        $mode       = $params['mode'] ?? 'genre';
        $genre_name = $params['genre_name'] ?? 'グルメ';
        $genre_code = $params['genre_code'] ?? '';
        $scene_key  = $params['scene'] ?? '';
        $count      = intval( $params['count'] ?? 5 );

        $category_ids = array();
        if ( $pref_id ) $category_ids[] = $pref_id;
        if ( $city_id ) $category_ids[] = $city_id;

        // 駅名テキスト
        $station_text = '';
        if ( $city_id ) {
            $station_text = knt_format_station_names( $city_id, 3 );
        }

        // API検索パラメータ構築
        $search_args = array( 'count' => $count + 5, 'order' => 4 );

        if ( $station && $lat && $lng ) {
            // 駅指定 → 緯度経度ベース検索
            $search_args['lat']   = floatval( $lat );
            $search_args['lng']   = floatval( $lng );
            $search_args['range'] = 3; // 1000m

            if ( $mode === 'genre' && $genre_name ) {
                $search_args['keyword'] = $genre_name;
            }
            if ( $genre_code ) {
                $search_args['genre'] = $genre_code;
            }
        } else {
            // キーワード検索
            $search_args['keyword'] = $area . ' ' . $genre_name;
            if ( $genre_code ) {
                $search_args['genre'] = $genre_code;
            }
        }

        // シーンの場合は追加条件
        if ( $mode === 'scene' && $scene_key && isset( KNT_SCENES[ $scene_key ] ) ) {
            $scene = KNT_SCENES[ $scene_key ];
            if ( ! $station ) {
                $search_args['keyword'] = $area . ' ' . $scene['keywords'];
            } else {
                $search_args['keyword'] = $scene['keywords'];
            }
            foreach ( $scene['api_filters'] as $k => $v ) {
                $search_args[ $k ] = $v;
            }
        }

        // API検索
        $shops = $this->api->search_shops( $search_args );
        if ( is_wp_error( $shops ) ) return $shops;

        // シーンフィルタリング
        if ( $mode === 'scene' && $scene_key ) {
            $shops = $this->filter_shops_by_scene( $shops, $scene_key );
        }

        if ( empty( $shops ) ) {
            return new WP_Error( 'no_shops', '該当する店舗が見つかりませんでした。' );
        }
        $shops = array_slice( $shops, 0, $count );

        // リード文変数
        $lead_vars = array(
            'area'       => $area,
            'stations'   => $station_text,
            'station'    => $station,
            'genre'      => $genre_name,
            'scene'      => $mode === 'scene' && $scene_key ? ( KNT_SCENES[ $scene_key ]['label'] ?? '' ) : '',
            'scene_desc' => $mode === 'scene' && $scene_key ? ( KNT_SCENES[ $scene_key ]['description'] ?? '' ) : '',
            'count'      => count( $shops ),
        );

        // タイトル生成
        $title_vars = $lead_vars;
        $title_vars['suffix'] = $mode === 'scene' && $scene_key ? ( KNT_SCENES[ $scene_key ]['title_suffix'] ?? '' ) : '';

        if ( $station ) {
            $title_type = $mode === 'scene' ? 'station_scene' : 'station_genre';
            $lead_type  = 'station';
        } elseif ( $station_text ) {
            $title_type = $mode === 'scene' ? 'scene' : 'genre';
            $lead_type  = $mode === 'scene' ? 'scene' : 'genre';
        } else {
            $title_type = $mode === 'scene' ? 'scene_no_st' : 'genre_no_st';
            $lead_type  = $mode === 'scene' ? 'scene' : 'genre';
        }

        $title = knt_generate_title( $title_type, $title_vars );
        $lead  = knt_generate_lead( $lead_type, $lead_vars );

        // スラッグ
        $area_slug = $this->get_area_slug( $station ?: $area );
        if ( $mode === 'scene' && $scene_key ) {
            $slug = $area_slug . '-' . ( KNT_SCENES[ $scene_key ]['slug'] ?? $scene_key ) . '-osusume';
        } else {
            $genre_slug = $this->get_area_slug( $genre_name );
            $slug = $area_slug . '-' . $genre_slug . '-osusume';
        }

        // 記事HTML構築（Phase 5 新構成）
        $blocks = array();
        $blocks[] = $this->block_callout( 'note', 'PR', 'この記事にはアフィリエイト広告・PR情報が含まれます' );
        $blocks[] = $this->block_paragraph( $lead );
        $blocks[] = $this->block_article_overview( $area, $genre_name, count( $shops ) );

        // シーンの選定基準
        if ( $mode === 'scene' && $scene_key && isset( KNT_SCENES[ $scene_key ] ) ) {
            $tips = array();
            $sf = KNT_SCENES[ $scene_key ]['api_filters'];
            if ( ! empty( $sf['private_room'] ) ) $tips[] = '個室完備';
            if ( ! empty( $sf['free_drink'] ) )   $tips[] = '飲み放題あり';
            if ( ! empty( $sf['free_food'] ) )    $tips[] = '食べ放題あり';
            if ( ! empty( $sf['lunch'] ) )        $tips[] = 'ランチ営業あり';
            if ( ! empty( $sf['midnight'] ) )     $tips[] = '深夜営業あり';
            if ( ! empty( $sf['child'] ) )        $tips[] = 'お子様連れOK';
            if ( ! empty( $sf['course'] ) )       $tips[] = 'コースあり';
            if ( $tips ) {
                $blocks[] = $this->block_callout( 'info', 'この記事の選定基準', implode( ' / ', $tips ) . ' の条件で厳選しています。' );
            }
        }

        $scene_for_badges = ( $mode === 'scene' && $scene_key ) ? $scene_key : '';

        // 各店舗セクション
        foreach ( $shops as $i => $shop ) {
            $blocks[] = $this->block_heading( $this->build_heading_text( $shop, $i + 1 ) );
            $blocks[] = $this->block_shop_image( $shop );
            $blocks[] = $this->block_restaurant_card( $shop );
            $blocks[] = $this->block_shop_info_table( $shop );
            $b = $this->build_facility_badges_block( $shop, $scene_for_badges );
            if ( $b ) $blocks[] = $b;
            $b = $this->block_special_callout( $shop );
            if ( $b ) $blocks[] = $b;
            $b = $this->build_editorial_comment( $shop, $i + 1 );
            if ( $b ) $blocks[] = $b;
            $blocks[] = $this->block_gmap_accordion( $shop );
            $blocks[] = $this->block_main_cta( $shop );
            $b = $this->block_shop_sublinks( $shop, $area );
            if ( $b ) $blocks[] = $b;
            if ( $i < count( $shops ) - 1 ) $blocks[] = $this->block_genre_divider( $genre_name );
        }

        // まとめ
        $blocks[] = $this->block_heading( 'まとめ｜' . $area . 'で美味しい' . $genre_name . 'を見つけよう' );
        $blocks[] = $this->block_paragraph( sprintf( '今回ご紹介した%d店舗は、どれも人気の実力店ばかりです。気になるお店があればぜひ予約してみてください。', count( $shops ) ) );
        // FAQ（まとめの上）
        $faq_title = $area . 'の' . $genre_name . 'に関するよくある質問';
        $blocks[] = $this->block_heading( $faq_title );
        $blocks[] = $this->block_faq( $area, $genre_name );

        // まとめ比較テーブル
        $blocks[] = $this->block_summary_table( $shops );
        $blocks[] = $this->block_paragraph( '<small>店舗情報・画像提供：<a href="https://webservice.recruit.co.jp/" target="_blank" rel="noopener noreferrer">ホットペッパーグルメ Webサービス</a></small>' );

        $content = implode( "\n\n", $blocks );

        $excerpt = $station
            ? sprintf( '%s周辺で美味しい%sを厳選！%d店舗を写真・予算・営業時間付きで紹介。予約リンクあり。', $station, $genre_name, count( $shops ) )
            : sprintf( '%sで%sを食べるならここ！厳選%d店舗を紹介。予約リンクあり。', $area, $genre_name, count( $shops ) );

        // 重複チェック
        $dup = $this->check_duplicate( $slug, $title );
        if ( is_wp_error( $dup ) ) return $dup;

        $post_data = array(
            'post_title' => $title, 'post_content' => $content, 'post_status' => 'draft',
            'post_type' => 'post', 'post_name' => $slug, 'post_excerpt' => $excerpt,
        );
        if ( ! empty( $category_ids ) ) $post_data['post_category'] = $category_ids;

        $post_id = wp_insert_post( $post_data, true );
        if ( is_wp_error( $post_id ) ) return $post_id;

        update_post_meta( $post_id, '_knt_generated', true );
        update_post_meta( $post_id, '_knt_area', $area );
        update_post_meta( $post_id, '_knt_station', $station );
        update_post_meta( $post_id, '_knt_genre', $genre_name );
        update_post_meta( $post_id, '_knt_shop_count', count( $shops ) );
        update_post_meta( $post_id, '_knt_generated_at', current_time( 'mysql' ) );
        if ( $mode === 'scene' && $scene_key ) {
            update_post_meta( $post_id, '_knt_scene', $scene_key );
        }

        $this->set_featured_image( $post_id, $shops[0] );
        $this->auto_set_tags( $post_id, array(
            'station'   => $station,
            'genre'     => $genre_name,
            'scene_key' => ( $mode === 'scene' && $scene_key ) ? $scene_key : '',
            'area'      => $area,
        ) );
        return $post_id;
    }
}
