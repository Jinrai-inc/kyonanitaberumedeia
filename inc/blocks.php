<?php
/**
 * KNT Media - Custom Gutenberg Blocks
 *
 * Registers custom block category, all 10 blocks, enqueues assets,
 * and provides server-side rendering for the FAQ block (FAQPage schema).
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ──────────────────────────────────────────────
   1. Block Category
   ────────────────────────────────────────────── */

function knt_block_category( $categories ) {
    return array_merge(
        array(
            array(
                'slug'  => 'knt-media',
                'title' => 'KNTメディア',
                'icon'  => 'food',
            ),
        ),
        $categories
    );
}
add_filter( 'block_categories_all', 'knt_block_category', 10, 1 );

/* ──────────────────────────────────────────────
   2. Enqueue Editor Assets
   ────────────────────────────────────────────── */

function knt_enqueue_block_editor_assets() {
    wp_enqueue_script(
        'knt-blocks-editor',
        KNT_URI . '/js/blocks.js',
        array(
            'wp-blocks',
            'wp-element',
            'wp-block-editor',
            'wp-components',
            'wp-i18n',
            'wp-data',
        ),
        KNT_VERSION,
        true
    );

    wp_enqueue_style(
        'knt-blocks-editor-style',
        KNT_URI . '/css/blocks.css',
        array( 'wp-edit-blocks' ),
        KNT_VERSION
    );
}
add_action( 'enqueue_block_editor_assets', 'knt_enqueue_block_editor_assets' );

/* ──────────────────────────────────────────────
   3. Enqueue Frontend Styles
   ────────────────────────────────────────────── */

function knt_enqueue_block_assets() {
    if ( ! is_admin() ) {
        wp_enqueue_style(
            'knt-blocks-style',
            KNT_URI . '/css/blocks.css',
            array(),
            KNT_VERSION
        );
    }
}
add_action( 'wp_enqueue_scripts', 'knt_enqueue_block_assets' );

/* ──────────────────────────────────────────────
   4. Register All Blocks
   ────────────────────────────────────────────── */

function knt_register_blocks() {

    /* --- knt/section --- */
    register_block_type( 'knt/section', array(
        'api_version' => 2,
        'attributes'  => array(
            'backgroundColor'   => array( 'type' => 'string', 'default' => '' ),
            'backgroundImage'   => array( 'type' => 'string', 'default' => '' ),
            'backgroundImageId' => array( 'type' => 'number', 'default' => 0 ),
            'maxWidth'          => array( 'type' => 'number', 'default' => 1100 ),
            'paddingTop'        => array( 'type' => 'number', 'default' => 60 ),
            'paddingBottom'     => array( 'type' => 'number', 'default' => 60 ),
        ),
    ) );

    /* --- knt/button --- */
    register_block_type( 'knt/button', array(
        'api_version' => 2,
        'attributes'  => array(
            'text'   => array( 'type' => 'string',  'default' => 'ボタン' ),
            'url'    => array( 'type' => 'string',  'default' => '' ),
            'style'  => array( 'type' => 'string',  'default' => 'primary' ),
            'size'   => array( 'type' => 'string',  'default' => 'medium' ),
            'newTab' => array( 'type' => 'boolean', 'default' => false ),
            'align'  => array( 'type' => 'string',  'default' => 'center' ),
        ),
    ) );

    /* --- knt/faq (server-side rendered) --- */
    register_block_type( 'knt/faq', array(
        'api_version'     => 2,
        'render_callback' => 'knt_render_faq_block',
        'attributes'      => array(
            'items' => array(
                'type'    => 'array',
                'default' => array(),
                'items'   => array( 'type' => 'object' ),
            ),
        ),
    ) );

    /* --- knt/callout --- */
    register_block_type( 'knt/callout', array(
        'api_version' => 2,
        'attributes'  => array(
            'type'    => array( 'type' => 'string', 'default' => 'info' ),
            'title'   => array( 'type' => 'string', 'default' => '' ),
            'content' => array( 'type' => 'string', 'default' => '' ),
        ),
    ) );

    /* --- knt/profile --- */
    register_block_type( 'knt/profile', array(
        'api_version' => 2,
        'attributes'  => array(
            'imageUrl'    => array( 'type' => 'string', 'default' => '' ),
            'imageId'     => array( 'type' => 'number', 'default' => 0 ),
            'name'        => array( 'type' => 'string', 'default' => '' ),
            'role'        => array( 'type' => 'string', 'default' => '' ),
            'description' => array( 'type' => 'string', 'default' => '' ),
            'twitter'     => array( 'type' => 'string', 'default' => '' ),
            'instagram'   => array( 'type' => 'string', 'default' => '' ),
            'facebook'    => array( 'type' => 'string', 'default' => '' ),
            'website'     => array( 'type' => 'string', 'default' => '' ),
        ),
    ) );

    /* --- knt/rating --- */
    register_block_type( 'knt/rating', array(
        'api_version' => 2,
        'attributes'  => array(
            'label'  => array( 'type' => 'string', 'default' => '味' ),
            'rating' => array( 'type' => 'number', 'default' => 3 ),
        ),
    ) );

    /* --- knt/steps --- */
    register_block_type( 'knt/steps', array(
        'api_version' => 2,
        'attributes'  => array(
            'steps' => array(
                'type'    => 'array',
                'default' => array(),
                'items'   => array( 'type' => 'object' ),
            ),
        ),
    ) );

    /* --- knt/price-table --- */
    register_block_type( 'knt/price-table', array(
        'api_version' => 2,
        'attributes'  => array(
            'plans' => array(
                'type'    => 'array',
                'default' => array(),
                'items'   => array( 'type' => 'object' ),
            ),
        ),
    ) );

    /* --- knt/app-cta --- */
    register_block_type( 'knt/app-cta', array(
        'api_version' => 2,
        'attributes'  => array(
            'title'       => array( 'type' => 'string', 'default' => '今日なに食べる？で迷わない。' ),
            'description' => array( 'type' => 'string', 'default' => 'AIがあなたの気分にぴったりのお店を提案します。' ),
            'buttonText'  => array( 'type' => 'string', 'default' => '無料ではじめる' ),
            'buttonUrl'   => array( 'type' => 'string', 'default' => '' ),
        ),
    ) );

    /* --- knt/restaurant-card --- */
    register_block_type( 'knt/restaurant-card', array(
        'api_version' => 2,
        'attributes'  => array(
            'imageUrl'    => array( 'type' => 'string', 'default' => '' ),
            'imageId'     => array( 'type' => 'number', 'default' => 0 ),
            'name'        => array( 'type' => 'string', 'default' => '' ),
            'genre'       => array( 'type' => 'string', 'default' => '' ),
            'area'        => array( 'type' => 'string', 'default' => '' ),
            'rating'      => array( 'type' => 'number', 'default' => 3 ),
            'description' => array( 'type' => 'string', 'default' => '' ),
            'url'         => array( 'type' => 'string', 'default' => '' ),
        ),
    ) );
}
add_action( 'init', 'knt_register_blocks' );

/* ──────────────────────────────────────────────
   5. FAQ Block Server-Side Render + Schema
   ────────────────────────────────────────────── */

function knt_render_faq_block( $attributes ) {
    $items = isset( $attributes['items'] ) ? $attributes['items'] : array();

    if ( empty( $items ) ) {
        return '';
    }

    // Build HTML
    $html = '<div class="knt-faq">';
    foreach ( $items as $item ) {
        $question = isset( $item['question'] ) ? $item['question'] : '';
        $answer   = isset( $item['answer'] ) ? $item['answer'] : '';
        if ( empty( $question ) ) {
            continue;
        }
        $html .= '<div class="knt-faq__item">';
        $html .= '<button class="knt-faq__question" aria-expanded="false">';
        $html .= '<span class="knt-faq__q-label">Q</span>';
        $html .= '<span class="knt-faq__q-text">' . esc_html( $question ) . '</span>';
        $html .= '<span class="knt-faq__toggle" aria-hidden="true"></span>';
        $html .= '</button>';
        $html .= '<div class="knt-faq__answer" hidden>';
        $html .= '<span class="knt-faq__a-label">A</span>';
        $html .= '<div class="knt-faq__a-text">' . wp_kses_post( $answer ) . '</div>';
        $html .= '</div>';
        $html .= '</div>';
    }
    $html .= '</div>';

    // Build FAQPage JSON-LD
    $schema_items = array();
    foreach ( $items as $item ) {
        $question = isset( $item['question'] ) ? $item['question'] : '';
        $answer   = isset( $item['answer'] ) ? $item['answer'] : '';
        if ( empty( $question ) ) {
            continue;
        }
        $schema_items[] = array(
            '@type'          => 'Question',
            'name'           => $question,
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text'  => wp_strip_all_tags( $answer ),
            ),
        );
    }

    if ( ! empty( $schema_items ) ) {
        $schema = array(
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => $schema_items,
        );
        $html .= '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>';
    }

    // Inline toggle script (only once per page)
    static $faq_script_added = false;
    if ( ! $faq_script_added ) {
        $html .= '<script>
document.addEventListener("click",function(e){
    var btn=e.target.closest(".knt-faq__question");
    if(!btn)return;
    var answer=btn.nextElementSibling;
    var expanded=btn.getAttribute("aria-expanded")==="true";
    btn.setAttribute("aria-expanded",String(!expanded));
    if(expanded){answer.setAttribute("hidden","");}else{answer.removeAttribute("hidden");}
});
</script>';
        $faq_script_added = true;
    }

    return $html;
}
