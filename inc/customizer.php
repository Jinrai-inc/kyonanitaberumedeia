<?php
/**
 * Theme Customizer settings
 * 管理画面 > 外観 > カスタマイズ で調整可能な項目
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function knt_customize_register( $wp_customize ) {

    // ========================================
    // カラー設定パネル
    // ========================================
    $wp_customize->add_panel( 'knt_colors_panel', array(
        'title'    => 'サイトカラー設定',
        'priority' => 30,
    ) );

    // --- メインカラー ---
    $wp_customize->add_section( 'knt_colors_main', array(
        'title' => 'メインカラー',
        'panel' => 'knt_colors_panel',
    ) );

    $color_settings = array(
        'knt_color_accent'       => array( 'label' => 'アクセントカラー（テラコッタ）', 'default' => '#C9553E' ),
        'knt_color_accent_light' => array( 'label' => 'アクセント淡色', 'default' => '#FAEAE6' ),
        'knt_color_bg_main'      => array( 'label' => '背景色（メイン）', 'default' => '#F4F0EB' ),
        'knt_color_bg_secondary' => array( 'label' => '背景色（セカンダリ）', 'default' => '#EDE8E1' ),
        'knt_color_text_main'    => array( 'label' => 'テキスト色（メイン）', 'default' => '#2A2622' ),
    );

    foreach ( $color_settings as $id => $args ) {
        $wp_customize->add_setting( $id, array(
            'default'           => $args['default'],
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, array(
            'label'   => $args['label'],
            'section' => 'knt_colors_main',
        ) ) );
    }

    // ========================================
    // ヘッダー設定
    // ========================================
    $wp_customize->add_section( 'knt_header', array(
        'title'    => 'ヘッダー設定',
        'priority' => 35,
    ) );

    // CTAボタンテキスト
    $wp_customize->add_setting( 'knt_header_cta_text', array(
        'default'           => 'アプリで探す',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'knt_header_cta_text', array(
        'label'   => 'CTAボタンテキスト',
        'section' => 'knt_header',
        'type'    => 'text',
    ) );

    // CTAボタンリンク
    $wp_customize->add_setting( 'knt_header_cta_url', array(
        'default'           => 'https://kyou-nani-taberu.app',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'knt_header_cta_url', array(
        'label'   => 'CTAボタンリンク先URL',
        'section' => 'knt_header',
        'type'    => 'url',
    ) );

    // CTAボタン表示
    $wp_customize->add_setting( 'knt_header_cta_show', array(
        'default'           => true,
        'sanitize_callback' => 'knt_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'knt_header_cta_show', array(
        'label'   => 'CTAボタンを表示する',
        'section' => 'knt_header',
        'type'    => 'checkbox',
    ) );

    // ========================================
    // トップページ設定
    // ========================================
    $wp_customize->add_panel( 'knt_frontpage_panel', array(
        'title'    => 'トップページ設定',
        'priority' => 40,
    ) );

    // --- ヒーロー ---
    $wp_customize->add_section( 'knt_hero', array(
        'title' => 'ヒーローセクション',
        'panel' => 'knt_frontpage_panel',
    ) );

    $wp_customize->add_setting( 'knt_hero_show', array(
        'default'           => true,
        'sanitize_callback' => 'knt_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'knt_hero_show', array(
        'label'   => 'ヒーローセクションを表示する',
        'section' => 'knt_hero',
        'type'    => 'checkbox',
    ) );

    $wp_customize->add_setting( 'knt_hero_post_id', array(
        'default'           => 0,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'knt_hero_post_id', array(
        'label'       => 'ピックアップ記事ID（0で最新記事）',
        'description' => '特定の記事をヒーローに表示したい場合、記事IDを入力',
        'section'     => 'knt_hero',
        'type'        => 'number',
    ) );

    // --- 日本地図エリア検索 ---
    $wp_customize->add_section( 'knt_japan_map', array(
        'title' => '日本地図エリア検索',
        'panel' => 'knt_frontpage_panel',
    ) );

    $wp_customize->add_setting( 'knt_japan_map_show', array(
        'default'           => true,
        'sanitize_callback' => 'knt_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'knt_japan_map_show', array(
        'label'   => '日本地図エリア検索を表示する',
        'section' => 'knt_japan_map',
        'type'    => 'checkbox',
    ) );

    $wp_customize->add_setting( 'knt_area_url_pattern', array(
        'default'           => 'search',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'knt_area_url_pattern', array(
        'label'       => 'エリアリンクの形式',
        'description' => 'search: 検索結果ページ / taxonomy: カスタムタクソノミー',
        'section'     => 'knt_japan_map',
        'type'        => 'select',
        'choices'     => array(
            'search'   => '検索形式（/?s=エリア名+グルメ）',
            'taxonomy' => 'タクソノミー形式（/area/都道府県/）',
        ),
    ) );

    $wp_customize->add_setting( 'knt_japan_map_title', array(
        'default'           => '食べたいエリアを選んでね',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'knt_japan_map_title', array(
        'label'   => 'タイトル',
        'section' => 'knt_japan_map',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'knt_japan_map_subtitle', array(
        'default'           => '地図をタップ or 下のメニューから選択できるよ',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'knt_japan_map_subtitle', array(
        'label'   => 'サブタイトル',
        'section' => 'knt_japan_map',
        'type'    => 'text',
    ) );

    // --- PR記事（いま人気な店舗） ---
    $wp_customize->add_section( 'knt_pr_section', array(
        'title' => 'PR記事（いま人気な店舗）',
        'panel' => 'knt_frontpage_panel',
    ) );

    $wp_customize->add_setting( 'knt_pr_title', array(
        'default'           => 'いま人気な店舗',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'knt_pr_title', array(
        'label'   => 'セクションタイトル',
        'section' => 'knt_pr_section',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'knt_pr_category_slug', array(
        'default'           => 'pr',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'knt_pr_category_slug', array(
        'label'       => 'PR記事のカテゴリ or タグのスラッグ',
        'description' => 'PR記事を判別するカテゴリまたはタグのスラッグ',
        'section'     => 'knt_pr_section',
        'type'        => 'text',
    ) );

    $wp_customize->add_setting( 'knt_pr_use_tag', array(
        'default'           => false,
        'sanitize_callback' => 'knt_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'knt_pr_use_tag', array(
        'label'       => 'タグで判別する（カテゴリの代わり）',
        'description' => 'ONにするとカテゴリではなくタグで検索します',
        'section'     => 'knt_pr_section',
        'type'        => 'checkbox',
    ) );

    $wp_customize->add_setting( 'knt_pr_count', array(
        'default'           => 6,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'knt_pr_count', array(
        'label'       => '表示件数',
        'section'     => 'knt_pr_section',
        'type'        => 'number',
        'input_attrs' => array( 'min' => 3, 'max' => 12 ),
    ) );

    // --- 新着記事 ---
    $wp_customize->add_section( 'knt_latest', array(
        'title' => '新着記事セクション',
        'panel' => 'knt_frontpage_panel',
    ) );

    $wp_customize->add_setting( 'knt_latest_title', array(
        'default'           => '新着記事',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'knt_latest_title', array(
        'label'   => 'セクションタイトル',
        'section' => 'knt_latest',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'knt_latest_count', array(
        'default'           => 6,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'knt_latest_count', array(
        'label'   => '表示件数',
        'section' => 'knt_latest',
        'type'    => 'number',
        'input_attrs' => array( 'min' => 3, 'max' => 12 ),
    ) );

    // --- カテゴリ別セクション ---
    $wp_customize->add_section( 'knt_category_sections', array(
        'title' => 'カテゴリ別セクション',
        'panel' => 'knt_frontpage_panel',
    ) );

    $wp_customize->add_setting( 'knt_category_sections_show', array(
        'default'           => true,
        'sanitize_callback' => 'knt_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'knt_category_sections_show', array(
        'label'   => 'カテゴリ別セクションを表示する',
        'section' => 'knt_category_sections',
        'type'    => 'checkbox',
    ) );

    $wp_customize->add_setting( 'knt_category_sections_slugs', array(
        'default'           => 'area,genre,trend',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'knt_category_sections_slugs', array(
        'label'       => '表示するカテゴリスラッグ（カンマ区切り）',
        'description' => '例: area,genre,trend,guide,column',
        'section'     => 'knt_category_sections',
        'type'        => 'text',
    ) );

    $wp_customize->add_setting( 'knt_category_sections_count', array(
        'default'           => 3,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'knt_category_sections_count', array(
        'label'   => '各カテゴリの表示件数',
        'section' => 'knt_category_sections',
        'type'    => 'number',
        'input_attrs' => array( 'min' => 2, 'max' => 6 ),
    ) );

    // --- 人気記事 ---
    $wp_customize->add_section( 'knt_popular', array(
        'title' => '人気記事セクション',
        'panel' => 'knt_frontpage_panel',
    ) );

    $wp_customize->add_setting( 'knt_popular_show', array(
        'default'           => true,
        'sanitize_callback' => 'knt_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'knt_popular_show', array(
        'label'   => '人気記事セクションを表示する',
        'section' => 'knt_popular',
        'type'    => 'checkbox',
    ) );

    $wp_customize->add_setting( 'knt_popular_title', array(
        'default'           => '人気記事ランキング',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'knt_popular_title', array(
        'label'   => 'セクションタイトル',
        'section' => 'knt_popular',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'knt_popular_count', array(
        'default'           => 5,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'knt_popular_count', array(
        'label'   => '表示件数',
        'section' => 'knt_popular',
        'type'    => 'number',
        'input_attrs' => array( 'min' => 3, 'max' => 10 ),
    ) );

    // ========================================
    // アプリ訴求バナー設定
    // ========================================
    $wp_customize->add_section( 'knt_app_banner', array(
        'title'    => 'アプリ訴求バナー',
        'priority' => 45,
    ) );

    $wp_customize->add_setting( 'knt_app_banner_show', array(
        'default'           => true,
        'sanitize_callback' => 'knt_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'knt_app_banner_show', array(
        'label'   => 'アプリ訴求バナーを表示する',
        'section' => 'knt_app_banner',
        'type'    => 'checkbox',
    ) );

    $wp_customize->add_setting( 'knt_app_banner_title', array(
        'default'           => '近くのお店をサクッと検索',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'knt_app_banner_title', array(
        'label'   => 'バナータイトル',
        'section' => 'knt_app_banner',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'knt_app_banner_text', array(
        'default'           => '「今日何食べる？」アプリなら、現在地周辺のお店をすぐに検索。気分やジャンルで絞り込んで、あなたにぴったりの一軒が見つかります。',
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );
    $wp_customize->add_control( 'knt_app_banner_text', array(
        'label'   => 'バナー説明文',
        'section' => 'knt_app_banner',
        'type'    => 'textarea',
    ) );

    $wp_customize->add_setting( 'knt_app_banner_btn_text', array(
        'default'           => 'アプリを使ってみる',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'knt_app_banner_btn_text', array(
        'label'   => 'ボタンテキスト',
        'section' => 'knt_app_banner',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'knt_app_banner_url', array(
        'default'           => 'https://kyou-nani-taberu.app',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'knt_app_banner_url', array(
        'label'   => 'ボタンリンク先URL',
        'section' => 'knt_app_banner',
        'type'    => 'url',
    ) );

    $wp_customize->add_setting( 'knt_app_screenshot', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'knt_app_screenshot', array(
        'label'   => 'アプリスクリーンショット画像',
        'description' => 'トップページのアプリ紹介セクションに表示される端末キャプチャ画像',
        'section' => 'knt_app_banner',
    ) ) );

    $wp_customize->add_setting( 'knt_app_logo', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'knt_app_logo', array(
        'label'   => 'アプリロゴ画像',
        'description' => 'アプリ紹介セクション左上 + 追尾バナーに表示されるロゴ',
        'section' => 'knt_app_banner',
    ) ) );

    // ========================================
    // 記事ページ設定
    // ========================================
    $wp_customize->add_section( 'knt_single', array(
        'title'    => '記事ページ設定',
        'priority' => 50,
    ) );

    $wp_customize->add_setting( 'knt_show_toc', array(
        'default'           => true,
        'sanitize_callback' => 'knt_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'knt_show_toc', array(
        'label'   => '目次を自動表示する',
        'section' => 'knt_single',
        'type'    => 'checkbox',
    ) );

    $wp_customize->add_setting( 'knt_show_related', array(
        'default'           => true,
        'sanitize_callback' => 'knt_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'knt_show_related', array(
        'label'   => '関連記事を表示する',
        'section' => 'knt_single',
        'type'    => 'checkbox',
    ) );

    $wp_customize->add_setting( 'knt_related_count', array(
        'default'           => 3,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'knt_related_count', array(
        'label'   => '関連記事の表示件数',
        'section' => 'knt_single',
        'type'    => 'number',
        'input_attrs' => array( 'min' => 2, 'max' => 6 ),
    ) );

    $wp_customize->add_setting( 'knt_show_app_cta_single', array(
        'default'           => true,
        'sanitize_callback' => 'knt_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'knt_show_app_cta_single', array(
        'label'   => '記事末尾にアプリCTAを表示する',
        'section' => 'knt_single',
        'type'    => 'checkbox',
    ) );

    $wp_customize->add_setting( 'knt_app_cta_text', array(
        'default'           => 'このエリアのお店をアプリで探す',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'knt_app_cta_text', array(
        'label'   => '記事内CTAボタンテキスト',
        'section' => 'knt_single',
        'type'    => 'text',
    ) );

    // ========================================
    // フッター設定
    // ========================================
    $wp_customize->add_section( 'knt_footer', array(
        'title'    => 'フッター設定',
        'priority' => 55,
    ) );

    $wp_customize->add_setting( 'knt_footer_style', array(
        'default'           => 'dark',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'knt_footer_style', array(
        'label'   => 'フッタースタイル',
        'section' => 'knt_footer',
        'type'    => 'select',
        'choices' => array(
            'dark'  => 'ダーク（#2A2622）',
            'light' => 'ライト（#EDE8E1）',
        ),
    ) );

    $wp_customize->add_setting( 'knt_company_name', array(
        'default'           => '株式会社仁頼',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'knt_company_name', array(
        'label'   => '運営会社名',
        'section' => 'knt_footer',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'knt_company_url', array(
        'default'           => 'https://jinrai.co.jp',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'knt_company_url', array(
        'label'   => '運営会社URL',
        'section' => 'knt_footer',
        'type'    => 'url',
    ) );

    $wp_customize->add_setting( 'knt_copyright', array(
        'default'           => '© 2025 Jinrai Inc.',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'knt_copyright', array(
        'label'   => 'コピーライト表記',
        'section' => 'knt_footer',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'knt_footer_description', array(
        'default'           => '「今日何食べる？」は、毎日の食事選びをもっと楽しくするグルメアプリです。',
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );
    $wp_customize->add_control( 'knt_footer_description', array(
        'label'   => 'フッター説明文',
        'section' => 'knt_footer',
        'type'    => 'textarea',
    ) );

    $wp_customize->add_setting( 'knt_privacy_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'knt_privacy_url', array(
        'label'       => 'プライバシーポリシーURL',
        'description' => '空欄の場合、固定ページ「privacy-policy」を検索',
        'section'     => 'knt_footer',
        'type'        => 'url',
    ) );

    $wp_customize->add_setting( 'knt_terms_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'knt_terms_url', array(
        'label'   => '利用規約URL',
        'section' => 'knt_footer',
        'type'    => 'url',
    ) );

    // ========================================
    // SNS / OGP設定
    // ========================================
    $wp_customize->add_section( 'knt_sns', array(
        'title'    => 'SNS・OGP設定',
        'priority' => 60,
    ) );

    $wp_customize->add_setting( 'knt_ogp_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'knt_ogp_image', array(
        'label'       => 'デフォルトOGP画像',
        'description' => '記事にアイキャッチがない場合に使用（1200x630px推奨）',
        'section'     => 'knt_sns',
    ) ) );

    $wp_customize->add_setting( 'knt_twitter_handle', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'knt_twitter_handle', array(
        'label'       => 'X (Twitter) アカウント名',
        'description' => '例: @kyounani',
        'section'     => 'knt_sns',
        'type'        => 'text',
    ) );

    // ========================================
    // AdSense設定
    // ========================================
    $wp_customize->add_section( 'knt_ads', array(
        'title'    => '広告設定',
        'priority' => 65,
    ) );

    $wp_customize->add_setting( 'knt_adsense_head', array(
        'default'           => '',
        'sanitize_callback' => 'knt_sanitize_html',
    ) );
    $wp_customize->add_control( 'knt_adsense_head', array(
        'label'       => 'AdSense <head>内コード',
        'description' => 'Google AdSenseの自動広告コードをここに貼り付け',
        'section'     => 'knt_ads',
        'type'        => 'textarea',
    ) );

    $wp_customize->add_setting( 'knt_ad_after_intro', array(
        'default'           => '',
        'sanitize_callback' => 'knt_sanitize_html',
    ) );
    $wp_customize->add_control( 'knt_ad_after_intro', array(
        'label'       => '記事冒頭の広告コード',
        'description' => '記事の最初の段落の後に表示',
        'section'     => 'knt_ads',
        'type'        => 'textarea',
    ) );

    $wp_customize->add_setting( 'knt_ad_before_related', array(
        'default'           => '',
        'sanitize_callback' => 'knt_sanitize_html',
    ) );
    $wp_customize->add_control( 'knt_ad_before_related', array(
        'label'       => '関連記事前の広告コード',
        'section'     => 'knt_ads',
        'type'        => 'textarea',
    ) );

    // ========================================
    // Google Analytics設定
    // ========================================
    $wp_customize->add_section( 'knt_analytics', array(
        'title'    => 'アクセス解析',
        'priority' => 70,
    ) );

    $wp_customize->add_setting( 'knt_ga_id', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'knt_ga_id', array(
        'label'       => 'Google Analytics 測定ID',
        'description' => '例: G-XXXXXXXXXX',
        'section'     => 'knt_analytics',
        'type'        => 'text',
    ) );
    // ========================================
    // API設定
    // ========================================
    $wp_customize->add_section( 'knt_api', array(
        'title'    => 'API設定',
        'priority' => 195,
    ) );

    $wp_customize->add_setting( 'knt_hotpepper_api_key', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'knt_hotpepper_api_key', array(
        'label'       => 'ホットペッパー APIキー',
        'description' => 'リクルートWebサービスのAPIキー',
        'section'     => 'knt_api',
        'type'        => 'text',
    ) );
}
add_action( 'customize_register', 'knt_customize_register' );

/**
 * Sanitize checkbox
 */
function knt_sanitize_checkbox( $input ) {
    return ( $input === true || $input === '1' || $input === 'true' ) ? true : false;
}

/**
 * Sanitize HTML (for ad codes)
 */
function knt_sanitize_html( $input ) {
    return $input; // Allow raw HTML for ad codes
}

/**
 * Customizer live preview
 */
function knt_customize_preview_js() {
    wp_enqueue_script(
        'knt-customize-preview',
        KNT_URI . '/js/customize-preview.js',
        array( 'customize-preview' ),
        KNT_VERSION,
        true
    );
}
add_action( 'customize_preview_init', 'knt_customize_preview_js' );
