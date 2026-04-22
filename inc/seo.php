<?php
/**
 * KNT Media - SEO最強化
 * メタタイトル / メタディスクリプション / 構造化データ / canonical / robots
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * metaタイトルの最適化
 */
function knt_seo_title( $title ) {
    $site_name = get_bloginfo( 'name' );

    if ( is_front_page() ) {
        return $site_name . ' | 今日食べるお店が見つかるグルメメディア';
    }
    if ( is_singular( 'post' ) ) {
        return get_the_title() . ' | ' . $site_name;
    }
    if ( is_category() ) {
        $cat = get_queried_object();
        $parent = $cat->parent ? get_category( $cat->parent ) : null;
        if ( $parent ) {
            return $cat->name . 'のグルメおすすめ記事一覧 | ' . $parent->name . ' | ' . $site_name;
        }
        return $cat->name . 'のグルメおすすめ記事一覧 | ' . $site_name;
    }
    if ( is_tag() ) {
        return '#' . single_tag_title( '', false ) . ' の記事一覧 | ' . $site_name;
    }
    if ( is_search() ) {
        return '「' . get_search_query() . '」の検索結果 | ' . $site_name;
    }
    if ( is_page() ) {
        return get_the_title() . ' | ' . $site_name;
    }
    if ( is_404() ) {
        return 'ページが見つかりません | ' . $site_name;
    }
    return $title;
}
add_filter( 'pre_get_document_title', 'knt_seo_title' );

/**
 * メタディスクリプション自動生成
 */
function knt_seo_meta_description() {
    $desc = '';

    if ( is_front_page() ) {
        $desc = '「今日何食べる？」が決まるグルメメディア。東京・大阪・全国の人気レストラン・ラーメン・居酒屋・カフェのおすすめ店舗を写真・予算・アクセス付きで紹介。ホットペッパーグルメ連携で簡単ネット予約。';
    } elseif ( is_singular( 'post' ) ) {
        $excerpt = get_the_excerpt();
        if ( $excerpt ) {
            $desc = wp_strip_all_tags( $excerpt );
        } else {
            $desc = wp_trim_words( wp_strip_all_tags( get_the_content() ), 80, '...' );
        }
    } elseif ( is_category() ) {
        $cat = get_queried_object();
        $cat_desc = category_description( $cat->term_id );
        if ( $cat_desc ) {
            $desc = wp_strip_all_tags( $cat_desc );
        } else {
            $desc = $cat->name . 'のグルメおすすめ記事一覧。人気店・話題のお店を写真・予算・営業時間付きで紹介。ネット予約リンクあり。';
        }
    } elseif ( is_tag() ) {
        $desc = '#' . single_tag_title( '', false ) . ' に関するグルメ記事一覧。おすすめ店舗を厳選して紹介しています。';
    } elseif ( is_search() ) {
        $desc = '「' . get_search_query() . '」の検索結果。今日何食べるが決まるメディアで、あなたにぴったりのお店を見つけよう。';
    }

    if ( $desc ) {
        $desc = mb_substr( $desc, 0, 155 );
        echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'knt_seo_meta_description', 1 );

/**
 * canonical URL
 */
function knt_seo_canonical() {
    if ( is_front_page() ) {
        echo '<link rel="canonical" href="' . esc_url( home_url( '/' ) ) . '">' . "\n";
    } elseif ( is_singular() ) {
        echo '<link rel="canonical" href="' . esc_url( get_permalink() ) . '">' . "\n";
    } elseif ( is_category() ) {
        echo '<link rel="canonical" href="' . esc_url( get_category_link( get_queried_object_id() ) ) . '">' . "\n";
    } elseif ( is_tag() ) {
        echo '<link rel="canonical" href="' . esc_url( get_tag_link( get_queried_object_id() ) ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'knt_seo_canonical', 1 );

/**
 * robots meta
 */
function knt_seo_robots() {
    // 検索結果・アーカイブの2ページ目以降はnoindex
    if ( is_search() ) {
        echo '<meta name="robots" content="noindex, follow">' . "\n";
    } elseif ( is_paged() && ! is_singular() ) {
        echo '<meta name="robots" content="noindex, follow">' . "\n";
    } elseif ( is_404() ) {
        echo '<meta name="robots" content="noindex, nofollow">' . "\n";
    }
}
add_action( 'wp_head', 'knt_seo_robots', 1 );

/**
 * 構造化データ（JSON-LD）を全面強化
 */
function knt_seo_structured_data() {
    $site_name = get_bloginfo( 'name' );
    $site_url  = home_url( '/' );
    $logo_id   = get_theme_mod( 'custom_logo' );
    $logo_url  = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';

    // ===== WebSite（全ページ共通） =====
    $website = array(
        '@context' => 'https://schema.org',
        '@type'    => 'WebSite',
        'name'     => $site_name,
        'url'      => $site_url,
        'publisher' => array(
            '@type' => 'Organization',
            'name'  => '株式会社仁頼',
            'url'   => 'https://jinrai.co.jp',
        ),
        'potentialAction' => array(
            '@type'       => 'SearchAction',
            'target'      => $site_url . '?s={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ),
    );
    if ( $logo_url ) {
        $website['publisher']['logo'] = $logo_url;
    }
    echo '<script type="application/ld+json">' . wp_json_encode( $website, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";

    // ===== Article（記事ページ） =====
    if ( is_singular( 'post' ) ) {
        global $post;
        $image = get_the_post_thumbnail_url( $post->ID, 'full' );
        $cats  = get_the_category();
        $tags  = get_the_tags();
        $excerpt = get_the_excerpt() ?: wp_trim_words( wp_strip_all_tags( $post->post_content ), 80, '' );

        $article = array(
            '@context'         => 'https://schema.org',
            '@type'            => 'Article',
            'mainEntityOfPage' => array( '@type' => 'WebPage', '@id' => get_permalink() ),
            'headline'         => get_the_title(),
            'description'      => mb_substr( $excerpt, 0, 155 ),
            'datePublished'    => get_the_date( 'c' ),
            'dateModified'     => get_the_modified_date( 'c' ),
            'author'           => array( '@type' => 'Organization', 'name' => '株式会社仁頼', 'url' => 'https://jinrai.co.jp' ),
            'publisher'        => array(
                '@type' => 'Organization',
                'name'  => '株式会社仁頼',
                'url'   => 'https://jinrai.co.jp',
            ),
            'wordCount' => mb_strlen( wp_strip_all_tags( $post->post_content ) ),
            'inLanguage' => 'ja',
        );
        if ( $image ) {
            $article['image'] = array( '@type' => 'ImageObject', 'url' => $image );
            $article['publisher']['logo'] = array( '@type' => 'ImageObject', 'url' => $logo_url ?: $image );
        }
        if ( $cats ) {
            $article['articleSection'] = $cats[0]->name;
        }
        if ( $tags ) {
            $article['keywords'] = implode( ', ', wp_list_pluck( $tags, 'name' ) );
        }

        echo '<script type="application/ld+json">' . wp_json_encode( $article, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";

        // ItemList（店舗一覧記事の場合）
        if ( get_post_meta( $post->ID, '_knt_generated', true ) ) {
            $shop_count = intval( get_post_meta( $post->ID, '_knt_shop_count', true ) );
            if ( $shop_count > 0 ) {
                $item_list = array(
                    '@context'        => 'https://schema.org',
                    '@type'           => 'ItemList',
                    'name'            => get_the_title(),
                    'numberOfItems'   => $shop_count,
                    'itemListOrder'   => 'https://schema.org/ItemListOrderDescending',
                    'itemListElement' => array(),
                );
                for ( $i = 1; $i <= $shop_count; $i++ ) {
                    $item_list['itemListElement'][] = array(
                        '@type'    => 'ListItem',
                        'position' => $i,
                        'url'      => get_permalink() . '#section-' . ( $i - 1 ),
                    );
                }
                echo '<script type="application/ld+json">' . wp_json_encode( $item_list, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
            }
        }
    }

    // ===== CollectionPage（カテゴリ/タグアーカイブ） =====
    if ( is_category() || is_tag() ) {
        $obj = get_queried_object();
        $collection = array(
            '@context' => 'https://schema.org',
            '@type'    => 'CollectionPage',
            'name'     => $obj->name . 'のグルメ記事一覧',
            'url'      => is_category() ? get_category_link( $obj->term_id ) : get_tag_link( $obj->term_id ),
            'description' => $obj->name . 'に関するおすすめグルメ記事を掲載中。',
            'isPartOf' => array( '@type' => 'WebSite', 'name' => $site_name, 'url' => $site_url ),
        );
        echo '<script type="application/ld+json">' . wp_json_encode( $collection, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
    }

    // ===== LocalBusiness / Organization（全ページ） =====
    $org = array(
        '@context'    => 'https://schema.org',
        '@type'       => 'Organization',
        'name'        => '株式会社仁頼',
        'alternateName' => 'Jinrai Co., Ltd.',
        'url'         => 'https://jinrai.co.jp',
        'foundingDate' => '2022-09',
        'founder'     => array( '@type' => 'Person', 'name' => '齊藤 一樹' ),
        'address'     => array(
            '@type'           => 'PostalAddress',
            'addressCountry'  => 'JP',
            'postalCode'      => '221-0001',
            'addressRegion'   => '神奈川県',
            'addressLocality' => '横浜市神奈川区',
            'streetAddress'   => '西寺尾4丁目6番6-3号',
        ),
        'sameAs' => array( 'https://x.com/kazuki15xxxx' ),
    );
    if ( $logo_url ) {
        $org['logo'] = $logo_url;
    }
    echo '<script type="application/ld+json">' . wp_json_encode( $org, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}

add_action( 'wp_head', 'knt_seo_structured_data', 5 );

/**
 * OGPタグの強化（既存header.phpのOGPを補完）
 */
function knt_seo_og_extras() {
    // og:locale
    echo '<meta property="og:locale" content="ja_JP">' . "\n";
    // article:author for posts
    if ( is_singular( 'post' ) ) {
        echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( 'c' ) ) . '">' . "\n";
        echo '<meta property="article:modified_time" content="' . esc_attr( get_the_modified_date( 'c' ) ) . '">' . "\n";
        $cats = get_the_category();
        if ( $cats ) {
            echo '<meta property="article:section" content="' . esc_attr( $cats[0]->name ) . '">' . "\n";
        }
        $tags = get_the_tags();
        if ( $tags ) {
            foreach ( $tags as $tag ) {
                echo '<meta property="article:tag" content="' . esc_attr( $tag->name ) . '">' . "\n";
            }
        }
    }
}
add_action( 'wp_head', 'knt_seo_og_extras', 2 );

/**
 * パンくずの構造化データを強化（既存knt_breadcrumb内のJSON-LDと共存）
 */

/**
 * RSS feed にアイキャッチ画像を追加
 */
function knt_seo_rss_thumbnail( $content ) {
    global $post;
    if ( has_post_thumbnail( $post->ID ) ) {
        $content = '<p>' . get_the_post_thumbnail( $post->ID, 'knt-card' ) . '</p>' . $content;
    }
    return $content;
}
add_filter( 'the_excerpt_rss', 'knt_seo_rss_thumbnail' );
add_filter( 'the_content_feed', 'knt_seo_rss_thumbnail' );

/**
 * 不要なWordPressのhead出力を削除（軽量化+セキュリティ）
 */
function knt_seo_cleanup_head() {
    remove_action( 'wp_head', 'wp_generator' );                    // WordPressバージョン
    remove_action( 'wp_head', 'wlwmanifest_link' );                // Windows Live Writer
    remove_action( 'wp_head', 'rsd_link' );                        // Really Simple Discovery
    remove_action( 'wp_head', 'wp_shortlink_wp_head' );            // ショートリンク
    remove_action( 'wp_head', 'rest_output_link_wp_head' );        // REST API link
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );   // oEmbed
    remove_action( 'wp_head', 'rel_canonical' );                   // WPデフォルトcanonical（seo.phpで出力）
}
add_action( 'init', 'knt_seo_cleanup_head' );

/**
 * 多言語SEO: hreflang + 国際対応メタタグ
 */
function knt_seo_international() {
    $current_url = home_url( add_query_arg( array() ) );

    // hreflang タグ（検索エンジンに言語バージョンを通知）
    $languages = array(
        'ja'    => $current_url,
        'en'    => $current_url, // Google翻訳で動的翻訳
        'zh-CN' => $current_url,
        'zh-TW' => $current_url,
        'ko'    => $current_url,
    );
    foreach ( $languages as $lang => $url ) {
        echo '<link rel="alternate" hreflang="' . esc_attr( $lang ) . '" href="' . esc_url( $url ) . '">' . "\n";
    }
    echo '<link rel="alternate" hreflang="x-default" href="' . esc_url( $current_url ) . '">' . "\n";

    // Content-Language
    echo '<meta http-equiv="content-language" content="ja">' . "\n";

    // geo メタタグ（日本の飲食メディアであることを明示）
    echo '<meta name="geo.region" content="JP">' . "\n";
    echo '<meta name="geo.placename" content="Japan">' . "\n";

    // Google に対する国際ターゲティング
    echo '<meta name="google" content="notranslate" />' . "\n"; // 自動翻訳バーを抑制（ウィジェットで対応）
}
add_action( 'wp_head', 'knt_seo_international', 2 );
