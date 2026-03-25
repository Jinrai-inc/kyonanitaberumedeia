<?php
/**
 * Kyou Nani Taberu Media Theme Functions
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'KNT_VERSION', '1.0.0' );
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
 */
function knt_rest_cors_headers( $value ) {
    $allowed_origins = array(
        'https://kyou-nani-taberu.app',
        'https://www.kyou-nani-taberu.app',
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
 * Include Customizer settings
 */
require_once KNT_DIR . '/inc/customizer.php';

/**
 * Include Custom Gutenberg Blocks
 */
require_once KNT_DIR . '/inc/blocks.php';

/**
 * Include Restaurant Links meta box & auto-display
 */
require_once KNT_DIR . '/inc/restaurant-links.php';

/**
 * Lazy load images - add loading attribute
 */
function knt_lazy_load_images( $attr, $attachment, $size ) {
    $attr['loading'] = 'lazy';
    return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'knt_lazy_load_images', 10, 3 );

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
 * Limit post revisions for performance
 */
if ( ! defined( 'WP_POST_REVISIONS' ) ) {
    define( 'WP_POST_REVISIONS', 5 );
}
