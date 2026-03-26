<?php
/**
 * Kyou Nani Taberu Media Theme Functions
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'KNT_VERSION', '1.0.7' );
define( 'KNT_DIR', get_template_directory() );
define( 'KNT_URI', get_template_directory_uri() );

/**
 * Theme setup
 */
function knt_setup() {
    // Title tag support
    add_theme_support( 'title-tag' );

    // Post thumbnails
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 800, 500, true );
    add_image_size( 'knt-hero', 1200, 630, true );
    add_image_size( 'knt-card', 600, 375, true );
    add_image_size( 'knt-ranking', 160, 112, true );

    // Custom logo
    add_theme_support( 'custom-logo', array(
        'height'      => 96,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ) );

    // HTML5 support
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ) );

    // Register navigation menus
    register_nav_menus( array(
        'primary'  => 'ヘッダーナビゲーション',
        'footer'   => 'フッターナビゲーション',
        'mobile'   => 'モバイルメニュー',
    ) );

    // Editor style
    add_editor_style( 'style.css' );

    // Responsive embeds
    add_theme_support( 'responsive-embeds' );

    // Align wide
    add_theme_support( 'align-wide' );
}
add_action( 'after_setup_theme', 'knt_setup' );

/**
 * Enqueue scripts and styles
 */
function knt_enqueue_assets() {
    // Google Fonts
    wp_enqueue_style(
        'knt-google-fonts',
        'https://fonts.googleapis.com/css2?family=Klee+One:wght@400;600&family=Zen+Maru+Gothic:wght@400;500;700&display=swap',
        array(),
        null
    );

    // Theme stylesheet
    wp_enqueue_style( 'knt-style', get_stylesheet_uri(), array( 'knt-google-fonts' ), KNT_VERSION );

    // Theme script
    wp_enqueue_script( 'knt-script', KNT_URI . '/js/main.js', array(), KNT_VERSION, true );

    // Localize for AJAX if needed
    wp_localize_script( 'knt-script', 'kntData', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'knt_nonce' ),
    ) );

    // Japan Map (front page only)
    if ( is_front_page() && get_theme_mod( 'knt_japan_map_show', true ) ) {
        wp_enqueue_style( 'knt-japan-map', KNT_URI . '/css/japan-map.css', array(), KNT_VERSION );
        wp_enqueue_script( 'knt-japan-map', KNT_URI . '/js/japan-map.js', array(), KNT_VERSION, true );
        // municipalities JSON をインラインで渡す（CORS回避）
        $json_path = KNT_DIR . '/data/municipalities-full.json';
        $municipalities = array();
        if ( file_exists( $json_path ) ) {
            $raw = file_get_contents( $json_path );
            $municipalities = json_decode( $raw, true );
            if ( ! is_array( $municipalities ) ) {
                $municipalities = array();
            }
        }
        wp_localize_script( 'knt-japan-map', 'kntMapData', array(
            'themeUrl'       => KNT_URI,
            'homeUrl'        => home_url(),
            'municipalities' => $municipalities,
            'svgInline'      => true,
        ) );
    }
}
add_action( 'wp_enqueue_scripts', 'knt_enqueue_assets' );

/**
 * Register widget areas
 */
function knt_widgets_init() {
    register_sidebar( array(
        'name'          => 'サイドバー',
        'id'            => 'sidebar-1',
        'description'   => '記事ページのサイドバーに表示されるウィジェット',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget__title">',
        'after_title'   => '</h3>',
    ) );

    register_sidebar( array(
        'name'          => 'フッターウィジェット',
        'id'            => 'footer-1',
        'description'   => 'フッターに表示されるウィジェット',
        'before_widget' => '<div id="%1$s" class="widget footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="footer__heading">',
        'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'knt_widgets_init' );

/**
 * Custom excerpt length
 */
function knt_excerpt_length( $length ) {
    return 80;
}
add_filter( 'excerpt_length', 'knt_excerpt_length' );

/**
 * Custom excerpt more
 */
function knt_excerpt_more( $more ) {
    return '...';
}
add_filter( 'excerpt_more', 'knt_excerpt_more' );

/**
 * Add WebP upload support
 */
function knt_mime_types( $mimes ) {
    $mimes['webp'] = 'image/webp';
    $mimes['svg']  = 'image/svg+xml';
    return $mimes;
}
add_filter( 'upload_mimes', 'knt_mime_types' );

/**
 * Output custom CSS from Customizer settings
 */
function knt_customizer_css() {
    $accent = get_theme_mod( 'knt_color_accent', '#C9553E' );
    $bg_main = get_theme_mod( 'knt_color_bg_main', '#F4F0EB' );
    $bg_secondary = get_theme_mod( 'knt_color_bg_secondary', '#EDE8E1' );
    $text_main = get_theme_mod( 'knt_color_text_main', '#2A2622' );
    $accent_light = get_theme_mod( 'knt_color_accent_light', '#FAEAE6' );

    echo '<style id="knt-customizer-css">
    :root {
        --color-accent: ' . esc_attr( $accent ) . ';
        --color-bg-main: ' . esc_attr( $bg_main ) . ';
        --color-bg-secondary: ' . esc_attr( $bg_secondary ) . ';
        --color-text-main: ' . esc_attr( $text_main ) . ';
        --color-accent-light: ' . esc_attr( $accent_light ) . ';
    }
    </style>';
}
add_action( 'wp_head', 'knt_customizer_css' );

/**
 * REST API CORS for app (additive – keeps WordPress default CORS intact)
 * + REST API URLをWordPressアドレスに揃える（サブディレクトリ構成対応）
 */

// PHP側: rest_url フィルター
function knt_fix_rest_url_for_admin( $url ) {
    if ( is_admin() ) {
        $site_url = site_url();
        $home_url = home_url();
        if ( $site_url !== $home_url ) {
            $url = str_replace( $home_url, $site_url, $url );
        }
    }
    return $url;
}
add_filter( 'rest_url', 'knt_fix_rest_url_for_admin' );

// wp-api-request のローカライズデータを直接上書き
function knt_fix_wp_api_settings() {
    if ( ! is_admin() ) return;
    $site_url = untrailingslashit( site_url() );
    $home_url = untrailingslashit( home_url() );
    if ( $site_url === $home_url ) return;

    $root = $site_url . '/' . rest_get_url_prefix() . '/';
    wp_add_inline_script( 'wp-api-request', 'wpApiSettings.root="' . esc_js( $root ) . '";', 'after' );
}
add_action( 'admin_enqueue_scripts', 'knt_fix_wp_api_settings', 999 );

// JS側: wpApiSettings.root を完全上書き（複数箇所で強制）
function knt_fix_rest_url_js_head() {
    if ( ! is_admin() ) return;
    $site_url = untrailingslashit( site_url() );
    $home_url = untrailingslashit( home_url() );
    if ( $site_url === $home_url ) return;
    $root = $site_url . '/' . rest_get_url_prefix() . '/';
    echo '<script>var kntCorrectRestRoot="' . esc_js( $root ) . '";</script>' . "\n";
}
add_action( 'admin_print_scripts', 'knt_fix_rest_url_js_head', 1 );

function knt_fix_rest_url_js_footer() {
    if ( ! is_admin() ) return;
    $site_url = untrailingslashit( site_url() );
    $home_url = untrailingslashit( home_url() );
    if ( $site_url === $home_url ) return;
    $root = $site_url . '/' . rest_get_url_prefix() . '/';
    ?>
    <script>
    (function(){
        var r = '<?php echo esc_js( $root ); ?>';
        // 1. wpApiSettings上書き
        if(typeof wpApiSettings!=='undefined'){wpApiSettings.root=r;}
        // 2. wp.apiFetchのルート上書き
        if(typeof wp!=='undefined'&&wp.apiFetch){
            wp.apiFetch.use(function(options,next){
                if(options.url){
                    options.url=options.url.replace('<?php echo esc_js( $home_url ); ?>','<?php echo esc_js( $site_url ); ?>');
                }
                if(options.path&&!options.url){
                    options.url=r+options.path.replace(/^\//,'');
                    delete options.path;
                }
                return next(options);
            });
        }
        // 3. MutationObserverで遅延ロードされたスクリプト対策
        var obs=new MutationObserver(function(){
            if(typeof wpApiSettings!=='undefined'&&wpApiSettings.root!==r){
                wpApiSettings.root=r;
            }
        });
        obs.observe(document.body,{childList:true,subtree:true});
        setTimeout(function(){obs.disconnect();},10000);
    })();
    </script>
    <?php
}
add_action( 'admin_print_footer_scripts', 'knt_fix_rest_url_js_footer', 999 );

// OPTIONSプリフライトへの応答
function knt_handle_preflight() {
    if ( $_SERVER['REQUEST_METHOD'] === 'OPTIONS' ) {
        $origin = isset( $_SERVER['HTTP_ORIGIN'] ) ? $_SERVER['HTTP_ORIGIN'] : '';
        $allowed = array(
            'https://media.kyou-nani-taberu.app',
            'https://kyou-nani-taberu.app',
            'https://www.kyou-nani-taberu.app',
        );
        if ( in_array( $origin, $allowed, true ) ) {
            header( 'Access-Control-Allow-Origin: ' . $origin );
            header( 'Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS' );
            header( 'Access-Control-Allow-Headers: Content-Type, Authorization, X-WP-Nonce, X-Requested-With' );
            header( 'Access-Control-Allow-Credentials: true' );
            header( 'Access-Control-Max-Age: 86400' );
            header( 'Content-Length: 0' );
            header( 'Content-Type: text/plain' );
            exit;
        }
    }
}
add_action( 'init', 'knt_handle_preflight', 1 );

function knt_rest_cors_headers( $value ) {
    $allowed_origins = array(
        'https://kyou-nani-taberu.app',
        'https://www.kyou-nani-taberu.app',
        'https://media.kyou-nani-taberu.app',
    );

    $origin = get_http_origin();
    if ( $origin && in_array( $origin, $allowed_origins, true ) ) {
        header( 'Access-Control-Allow-Origin: ' . $origin );
        header( 'Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS' );
        header( 'Access-Control-Allow-Headers: Content-Type, Authorization' );
        header( 'Access-Control-Allow-Credentials: true' );
    }

    return $value;
}
add_filter( 'rest_pre_serve_request', 'knt_rest_cors_headers' );

/**
 * Add structured data (JSON-LD)
 */
function knt_structured_data() {
    if ( is_singular( 'post' ) ) {
        global $post;
        $image = get_the_post_thumbnail_url( $post->ID, 'full' );
        $data = array(
            '@context'      => 'https://schema.org',
            '@type'         => 'Article',
            'headline'      => get_the_title(),
            'datePublished' => get_the_date( 'c' ),
            'dateModified'  => get_the_modified_date( 'c' ),
            'author'        => array(
                '@type' => 'Organization',
                'name'  => get_theme_mod( 'knt_company_name', '株式会社仁頼' ),
            ),
            'publisher'     => array(
                '@type' => 'Organization',
                'name'  => get_theme_mod( 'knt_company_name', '株式会社仁頼' ),
            ),
        );
        if ( $image ) {
            $data['image'] = $image;
        }
        echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>';
    }

    // Organization (all pages)
    $org = array(
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        'name'     => get_theme_mod( 'knt_company_name', '株式会社仁頼' ),
        'url'      => home_url( '/' ),
    );
    $logo_id = get_theme_mod( 'custom_logo' );
    if ( $logo_id ) {
        $org['logo'] = wp_get_attachment_image_url( $logo_id, 'full' );
    }
    echo '<script type="application/ld+json">' . wp_json_encode( $org, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>';
}
add_action( 'wp_head', 'knt_structured_data' );

/**
 * Breadcrumb helper
 */
function knt_breadcrumb() {
    if ( is_front_page() ) {
        return;
    }

    $items = array();
    $items[] = '<a href="' . esc_url( home_url( '/' ) ) . '">ホーム</a>';

    if ( is_category() ) {
        $cat = get_queried_object();
        if ( $cat->parent ) {
            $parent = get_category( $cat->parent );
            $items[] = '<a href="' . esc_url( get_category_link( $parent->term_id ) ) . '">' . esc_html( $parent->name ) . '</a>';
        }
        $items[] = '<span class="breadcrumb__current">' . esc_html( $cat->name ) . '</span>';
    } elseif ( is_single() ) {
        $cats = get_the_category();
        if ( $cats ) {
            $items[] = '<a href="' . esc_url( get_category_link( $cats[0]->term_id ) ) . '">' . esc_html( $cats[0]->name ) . '</a>';
        }
        $items[] = '<span class="breadcrumb__current">' . esc_html( get_the_title() ) . '</span>';
    } elseif ( is_page() ) {
        $items[] = '<span class="breadcrumb__current">' . esc_html( get_the_title() ) . '</span>';
    } elseif ( is_search() ) {
        $items[] = '<span class="breadcrumb__current">検索結果: ' . esc_html( get_search_query() ) . '</span>';
    } elseif ( is_tag() ) {
        $items[] = '<span class="breadcrumb__current">タグ: ' . esc_html( single_tag_title( '', false ) ) . '</span>';
    } elseif ( is_404() ) {
        $items[] = '<span class="breadcrumb__current">404</span>';
    }

    // BreadcrumbList structured data
    $json_items = array();
    foreach ( $items as $i => $item ) {
        $position = $i + 1;
        // Extract URL from anchor tag if present
        if ( preg_match( '/href="([^"]+)"/', $item, $matches ) ) {
            $json_items[] = array(
                '@type'    => 'ListItem',
                'position' => $position,
                'name'     => wp_strip_all_tags( $item ),
                'item'     => $matches[1],
            );
        } else {
            $json_items[] = array(
                '@type'    => 'ListItem',
                'position' => $position,
                'name'     => wp_strip_all_tags( $item ),
            );
        }
    }

    echo '<script type="application/ld+json">' . wp_json_encode( array(
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $json_items,
    ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>';

    echo '<nav class="breadcrumb" aria-label="パンくずリスト">';
    echo implode( '<span class="breadcrumb__separator">&gt;</span>', $items );
    echo '</nav>';
}

/**
 * Popular posts (simple view count)
 */
function knt_track_post_views() {
    if ( is_singular( 'post' ) && ! is_admin() ) {
        $post_id = get_the_ID();
        $count = (int) get_post_meta( $post_id, 'knt_views', true );
        update_post_meta( $post_id, 'knt_views', $count + 1 );
    }
}
add_action( 'wp', 'knt_track_post_views' );

function knt_get_popular_posts( $limit = 5 ) {
    return new WP_Query( array(
        'posts_per_page' => $limit,
        'meta_key'       => 'knt_views',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
        'post_type'      => 'post',
        'post_status'    => 'publish',
    ) );
}

/**
 * Generate Table of Contents from post content
 */
function knt_generate_toc( $content ) {
    if ( ! is_singular( 'post' ) ) {
        return $content;
    }

    $show_toc = get_theme_mod( 'knt_show_toc', true );
    if ( ! $show_toc ) {
        return $content;
    }

    preg_match_all( '/<h([23])[^>]*>(.*?)<\/h[23]>/i', $content, $matches, PREG_SET_ORDER );

    if ( count( $matches ) < 2 ) {
        return $content;
    }

    $toc = '<div class="toc"><div class="toc__title" id="toc-toggle">&#9776; 目次</div><ul class="toc__list" id="toc-list">';

    foreach ( $matches as $i => $match ) {
        $level = $match[1];
        $title = wp_strip_all_tags( $match[2] );
        $id = 'section-' . $i;

        // Add ID to heading in content
        $content = str_replace(
            $match[0],
            '<h' . $level . ' id="' . $id . '">' . $match[2] . '</h' . $level . '>',
            $content
        );

        $class = ( $level === '3' ) ? ' class="toc__h3"' : '';
        $toc .= '<li' . $class . '><a href="#' . $id . '">' . esc_html( $title ) . '</a></li>';
    }

    $toc .= '</ul></div>';

    return $toc . $content;
}
add_filter( 'the_content', 'knt_generate_toc', 1 );

/**
 * アプリケーションパスワードを明示的に有効化
 */
add_filter( 'wp_is_application_passwords_available', '__return_true' );

/**
 * Include Customizer settings
 */
require_once KNT_DIR . '/inc/customizer.php';

/**
 * Include Custom Gutenberg Blocks
 */
require_once KNT_DIR . '/inc/blocks.php';

/**
 * Include Scene Definitions
 */
require_once KNT_DIR . '/inc/scenes.php';

/**
 * Include Station Data + Lead Templates
 */
require_once KNT_DIR . '/inc/station-data.php';
require_once KNT_DIR . '/inc/lead-templates.php';

/**
 * シーンページのリライトルール
 */
function knt_scene_rewrite_rules() {
    add_rewrite_rule(
        '^scene/([^/]+)/?$',
        'index.php?pagename=scene&knt_scene_slug=$matches[1]',
        'top'
    );
    add_rewrite_rule(
        '^scene/?$',
        'index.php?pagename=scene',
        'top'
    );
}
add_action( 'init', 'knt_scene_rewrite_rules' );

function knt_scene_query_vars( $vars ) {
    $vars[] = 'knt_scene_slug';
    return $vars;
}
add_filter( 'query_vars', 'knt_scene_query_vars' );

function knt_scene_template( $template ) {
    // Customizer プレビュー時は干渉しない
    if ( is_customize_preview() ) {
        return $template;
    }
    if ( get_query_var( 'knt_scene_slug' ) ) {
        $scene_template = KNT_DIR . '/page-scene.php';
        if ( file_exists( $scene_template ) ) {
            return $scene_template;
        }
    }
    return $template;
}
add_filter( 'template_include', 'knt_scene_template' );

/**
 * Include Restaurant Links meta box & auto-display
 */
require_once KNT_DIR . '/inc/restaurant-links.php';

/**
 * Include Area Categories setup tool
 */
require_once KNT_DIR . '/inc/area-categories.php';

/**
 * Lazy load images - add loading attribute
 */
function knt_lazy_load_images( $attr, $attachment, $size ) {
    $attr['loading'] = 'lazy';
    return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'knt_lazy_load_images', 10, 3 );

/**
 * テーマ有効化時にプライバシーポリシー・特商法ページを自動作成
 */
function knt_create_legal_pages() {
    // プライバシーポリシー
    if ( ! get_page_by_path( 'privacy-policy' ) ) {
        wp_insert_post( array(
            'post_title'   => 'プライバシーポリシー',
            'post_name'    => 'privacy-policy',
            'post_content' => knt_privacy_policy_content(),
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ) );
    }

    // 特定商取引法に基づく表記
    if ( ! get_page_by_path( 'legal-commerce' ) ) {
        wp_insert_post( array(
            'post_title'   => '特定商取引法に基づく表記',
            'post_name'    => 'legal-commerce',
            'post_content' => knt_commerce_law_content(),
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ) );
    }

    // 利用規約
    if ( ! get_page_by_path( 'terms' ) ) {
        wp_insert_post( array(
            'post_title'   => '利用規約',
            'post_name'    => 'terms',
            'post_content' => knt_terms_content(),
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ) );
    }
}
add_action( 'after_switch_theme', 'knt_create_legal_pages' );

// 管理画面からも手動実行可能
function knt_maybe_create_legal_pages() {
    if ( is_admin() && current_user_can( 'manage_options' ) ) {
        if ( ! get_page_by_path( 'privacy-policy' ) || ! get_page_by_path( 'legal-commerce' ) || ! get_page_by_path( 'terms' ) ) {
            knt_create_legal_pages();
        }
    }
}
add_action( 'admin_init', 'knt_maybe_create_legal_pages' );

function knt_privacy_policy_content() {
    return '<!-- wp:heading -->
<h2>個人情報の利用目的</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>当サイト「今日何食べるが決まるメディア」（以下、「当サイト」）では、お問い合わせやコメント投稿の際に、お名前・メールアドレス等の個人情報をご入力いただく場合がございます。取得した個人情報は、お問い合わせへの回答や必要な情報のご連絡のために利用し、それ以外の目的では利用いたしません。</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>広告について</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>当サイトでは、第三者配信の広告サービスを利用しています。このような広告配信事業者は、ユーザーの興味に応じた商品やサービスの広告を表示するため、当サイトや他サイトへのアクセスに関する情報（氏名、住所、メールアドレス、電話番号は含まれません）を使用することがあります。</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p>当サイトが利用している広告サービス：</p>
<!-- /wp:paragraph -->
<!-- wp:list -->
<ul>
<li>バリューコマース（ValueCommerce）</li>
<li>ホットペッパーグルメ Webサービス</li>
</ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2>アフィリエイトプログラムについて</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>当サイトは、バリューコマースアフィリエイトプログラムに参加しています。当サイトの記事内にはアフィリエイトリンクが含まれており、リンク先での商品購入やサービス利用により、当サイトが報酬を受け取る場合があります。</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>アクセス解析ツールについて</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>当サイトでは、Googleによるアクセス解析ツール「Googleアナリティクス」を利用しています。このGoogleアナリティクスはトラフィックデータの収集のためにCookieを使用しています。このトラフィックデータは匿名で収集されており、個人を特定するものではありません。</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>店舗情報について</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>当サイトに掲載されている店舗情報の一部は、ホットペッパーグルメ Webサービスを利用して取得しています。掲載情報は取得時点のものであり、最新情報は各店舗の公式サイト等でご確認ください。</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>免責事項</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>当サイトに掲載された内容によって生じた損害等の一切の責任を負いかねますのでご了承ください。当サイトからリンクやバナーなどによって他のサイトに移動された場合、移動先サイトで提供される情報、サービス等について一切の責任を負いません。</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>著作権について</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>当サイトで掲載している画像の著作権・肖像権等は各権利所有者に帰属いたします。権利を侵害する目的ではございません。記事の内容や掲載画像等に問題がございましたら、お手数ですがお問い合わせよりご連絡ください。</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>運営者情報</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>運営者：株式会社仁頼（じんらい）<br>所在地：〒221-0001 神奈川県横浜市神奈川区西寺尾4丁目6番6-3号<br>代表者：齊藤 一樹<br>URL：<a href="https://jinrai.co.jp">https://jinrai.co.jp</a><br>お問い合わせ：<a href="https://jinrai.co.jp/contact">お問い合わせフォーム</a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>制定日：2026年3月26日</p>
<!-- /wp:paragraph -->';
}

function knt_commerce_law_content() {
    return '<!-- wp:heading -->
<h2>特定商取引法に基づく表記</h2>
<!-- /wp:heading -->

<!-- wp:html -->
<table class="knt-legal-table">
<tr><th>事業者名</th><td>株式会社仁頼（じんらい）/ Jinrai Co., Ltd.</td></tr>
<tr><th>代表者</th><td>齊藤 一樹（さいとう かずき）</td></tr>
<tr><th>所在地</th><td>〒221-0001 神奈川県横浜市神奈川区西寺尾4丁目6番6-3号</td></tr>
<tr><th>設立</th><td>2022年9月</td></tr>
<tr><th>法人番号</th><td>4020001148080</td></tr>
<tr><th>電話番号</th><td>お問い合わせフォームよりご連絡ください</td></tr>
<tr><th>メールアドレス</th><td>お問い合わせフォームよりご連絡ください</td></tr>
<tr><th>URL</th><td><a href="https://jinrai.co.jp">https://jinrai.co.jp</a></td></tr>
<tr><th>商品の販売価格</th><td>各商品・サービスのページに記載</td></tr>
<tr><th>商品代金以外の必要料金</th><td>なし</td></tr>
<tr><th>支払方法</th><td>各サービスページに記載</td></tr>
<tr><th>商品の引渡時期</th><td>各サービスページに記載</td></tr>
<tr><th>返品・キャンセル</th><td>サービスの性質上、提供後の返品・キャンセルはお受けできません</td></tr>
</table>
<!-- /wp:html -->

<!-- wp:paragraph -->
<p>※ 当サイトはグルメ情報メディアであり、店舗の予約・決済は各外部サービス（ホットペッパーグルメ等）にて行われます。各店舗での飲食・予約に関するお問い合わせは、各店舗または各予約サービスにお問い合わせください。</p>
<!-- /wp:paragraph -->';
}

function knt_terms_content() {
    return '<!-- wp:heading -->
<h2>利用規約</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>この利用規約（以下、「本規約」）は、株式会社仁頼（以下、「当社」）が運営するウェブサイト「今日何食べるが決まるメディア」（以下、「当サイト」）の利用条件を定めるものです。当サイトをご利用いただくすべての方（以下、「利用者」）に本規約が適用されます。</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3>第1条（適用）</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>本規約は、利用者と当社との間の当サイト利用に関わる一切の関係に適用されるものとします。</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3>第2条（掲載情報について）</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>当サイトに掲載されている店舗情報は、ホットペッパーグルメ Webサービスから取得した情報に基づいています。情報は取得時点のものであり、最新の営業時間・定休日・メニュー・価格等は各店舗にご確認ください。当社は掲載情報の正確性を保証するものではありません。</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3>第3条（外部リンクについて）</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>当サイトにはホットペッパーグルメ、食べログ、一休.comレストラン、Google Maps等の外部サービスへのリンクが含まれます。これらのサービスでの予約・決済・トラブル等について、当社は一切の責任を負いません。</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3>第4条（広告・アフィリエイトについて）</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>当サイトにはアフィリエイト広告が含まれます。利用者が当サイト内のリンクを経由して外部サービスで商品購入やサービス利用をされた場合、当社が報酬を受け取ることがあります。これにより利用者に追加の費用が発生することはありません。</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3>第5条（禁止事項）</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>当サイトの利用にあたり、以下の行為を禁止します。</p>
<!-- /wp:paragraph -->
<!-- wp:list -->
<ul>
<li>当サイトのコンテンツを無断で複製・転載する行為</li>
<li>当サイトの運営を妨げる行為</li>
<li>その他、当社が不適切と判断する行為</li>
</ul>
<!-- /wp:list -->

<!-- wp:heading {"level":3} -->
<h3>第6条（免責事項）</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>当サイトに掲載された情報を利用することで生じた損害について、当社は一切の責任を負いません。</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3>第7条（規約の変更）</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>当社は、必要と判断した場合には、利用者に通知することなくいつでも本規約を変更することができるものとします。</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>制定日：2026年3月26日<br>株式会社仁頼</p>
<!-- /wp:paragraph -->';
}

/**
 * AJAX Load More posts
 */
function knt_load_more_posts() {
    check_ajax_referer( 'knt_nonce', 'nonce' );

    $paged    = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
    $per_page = isset( $_POST['per_page'] ) ? absint( $_POST['per_page'] ) : 9;
    $cat      = isset( $_POST['category'] ) ? absint( $_POST['category'] ) : 0;
    $search   = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';

    $args = array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => $per_page,
        'paged'          => $paged,
    );

    if ( $cat ) {
        $args['cat'] = $cat;
    }
    if ( $search ) {
        $args['s'] = $search;
    }

    $query = new WP_Query( $args );
    $html  = '';

    if ( $query->have_posts() ) {
        ob_start();
        while ( $query->have_posts() ) {
            $query->the_post();
            echo '<article class="card fadeup is-visible">';
            get_template_part( 'template-parts/card' );
            echo '</article>';
        }
        $html = ob_get_clean();
        wp_reset_postdata();
    }

    wp_send_json_success( array(
        'html'     => $html,
        'has_more' => $paged < $query->max_num_pages,
    ) );
}
add_action( 'wp_ajax_knt_load_more', 'knt_load_more_posts' );
add_action( 'wp_ajax_nopriv_knt_load_more', 'knt_load_more_posts' );

/**
 * エリア別関連記事
 */
function knt_render_area_related_posts() {
    $post_id = get_the_ID();
    $categories = wp_get_post_categories( $post_id, array( 'fields' => 'all' ) );
    $genre_names = array( 'ラーメン','焼肉','和食','中華','イタリアン・フレンチ','カフェ・スイーツ','カレー','居酒屋','韓国料理','ハンバーガー','ステーキ','エスニック','バー','洋食' );

    $pref_cat = null;
    $city_cat = null;
    foreach ( $categories as $cat ) {
        if ( in_array( $cat->name, $genre_names, true ) ) continue;
        if ( $cat->parent > 0 ) {
            $parent = get_category( $cat->parent );
            if ( $parent && ! in_array( $parent->name, $genre_names, true ) ) {
                $pref_cat = $parent;
                $city_cat = $cat;
            }
        } elseif ( $cat->parent === 0 ) {
            $pref_cat = $cat;
        }
    }

    $shown = array( $post_id );
    echo '<div class="related-area-posts" style="margin-top: 48px;">';

    if ( $city_cat ) {
        $q = new WP_Query( array( 'posts_per_page' => 6, 'category__in' => array( $city_cat->term_id ), 'post__not_in' => $shown, 'post_status' => 'publish' ) );
        if ( $q->have_posts() ) {
            while ( $q->have_posts() ) { $q->the_post(); $shown[] = get_the_ID(); }
            $q->rewind_posts();
            echo '<section class="related-area-section"><h2 class="section__title">' . esc_html( $city_cat->name ) . 'のその他のグルメ記事</h2><div class="grid grid--3">';
            while ( $q->have_posts() ) { $q->the_post(); echo '<article class="card fadeup">'; get_template_part( 'template-parts/card' ); echo '</article>'; }
            echo '</div><div style="text-align:center;margin-top:16px;"><a href="' . esc_url( get_category_link( $city_cat->term_id ) ) . '" class="btn btn--secondary">' . esc_html( $city_cat->name ) . 'の記事をもっと見る →</a></div></section>';
            wp_reset_postdata();
        }
    }

    if ( $pref_cat ) {
        $q2 = new WP_Query( array( 'posts_per_page' => 6, 'category__in' => array( $pref_cat->term_id ), 'post__not_in' => $shown, 'post_status' => 'publish' ) );
        if ( $q2->have_posts() ) {
            echo '<section class="related-area-section" style="margin-top:40px;"><h2 class="section__title">' . esc_html( $pref_cat->name ) . 'のその他のグルメ記事</h2><div class="grid grid--3">';
            while ( $q2->have_posts() ) { $q2->the_post(); echo '<article class="card fadeup">'; get_template_part( 'template-parts/card' ); echo '</article>'; }
            echo '</div><div style="text-align:center;margin-top:16px;"><a href="' . esc_url( get_category_link( $pref_cat->term_id ) ) . '" class="btn btn--secondary">' . esc_html( $pref_cat->name ) . 'の記事をもっと見る →</a></div></section>';
            wp_reset_postdata();
        }
    }

    echo '</div>';
}

/**
 * Limit post revisions for performance
 */
if ( ! defined( 'WP_POST_REVISIONS' ) ) {
    define( 'WP_POST_REVISIONS', 5 );
}

/**
 * ホットペッパーグルメ APIキー（wp_optionsが優先、未設定時のフォールバック）
 */
if ( ! defined( 'KNT_HOTPEPPER_API_KEY' ) ) {
    define( 'KNT_HOTPEPPER_API_KEY', 'acffba006a6824d4' );
}

/**
 * 記事自動生成機能の読み込み（管理画面のみ）
 */
if ( is_admin() ) {
    require_once KNT_DIR . '/admin/generator-page.php';
    require_once KNT_DIR . '/admin/theme-settings.php';
}
