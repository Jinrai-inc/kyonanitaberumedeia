<?php
/**
 * KNT Media テーマ設定ページ（タブ式）
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 設定取得ヘルパー（新設定 → Customizer フォールバック）
 */
function knt_get_setting( $key, $default = '' ) {
    $settings = get_option( 'knt_settings', array() );
    if ( isset( $settings[ $key ] ) ) {
        return $settings[ $key ];
    }
    // Customizer からのフォールバック
    $customizer_map = array(
        'fp_content_slider_show'   => 'knt_slider_show',
        'fp_japan_map_show'        => 'knt_japan_map_show',
        'fp_popular_show'          => 'knt_popular_show',
        'fp_pr_show'               => 'knt_pr_show',
        'fp_latest_show'           => 'knt_latest_show',
        'fp_category_sections_show'=> 'knt_category_sections_show',
        'fp_app_banner_show'       => 'knt_app_banner_show',
        'header_cta_show'          => 'knt_header_cta_show',
        'header_cta_text'          => 'knt_header_cta_text',
        'header_cta_url'           => 'knt_header_cta_url',
        'footer_style'             => 'knt_footer_style',
        'company_name'             => 'knt_company_name',
        'copyright'                => 'knt_copyright',
        'color_accent'             => 'knt_color_accent',
    );
    if ( isset( $customizer_map[ $key ] ) ) {
        return get_theme_mod( $customizer_map[ $key ], $default );
    }
    return $default;
}

/**
 * メニュー登録
 */
function knt_add_theme_settings_menu() {
    add_menu_page(
        'KNTメディア設定',
        'KNT設定',
        'manage_options',
        'knt-settings',
        'knt_render_theme_settings',
        'dashicons-food',
        3
    );
}
add_action( 'admin_menu', 'knt_add_theme_settings_menu' );

/**
 * 設定登録
 */
function knt_register_settings() {
    register_setting( 'knt_settings_group', 'knt_settings', array(
        'sanitize_callback' => 'knt_sanitize_settings',
    ) );
}
add_action( 'admin_init', 'knt_register_settings' );

function knt_sanitize_settings( $input ) {
    if ( ! is_array( $input ) ) return array();
    $clean = array();
    foreach ( $input as $key => $val ) {
        if ( is_array( $val ) ) {
            $clean[ $key ] = array_map( 'sanitize_text_field', $val );
        } else {
            $clean[ $key ] = sanitize_text_field( $val );
        }
    }
    return $clean;
}

/**
 * 管理CSS/JS
 */
function knt_theme_settings_assets( $hook ) {
    if ( $hook !== 'toplevel_page_knt-settings' ) return;
    wp_enqueue_style( 'knt-settings', KNT_URI . '/admin/settings.css', array(), KNT_VERSION );
    wp_enqueue_script( 'knt-settings', KNT_URI . '/js/admin-settings.js', array(), KNT_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'knt_theme_settings_assets' );

/**
 * タブ定義
 */
function knt_settings_tabs() {
    return array(
        'display' => '表示設定',
        'layout'  => 'レイアウト',
        'ogp'     => 'OGP',
        'sns'     => 'SNS',
        'cta'     => 'CTA',
        'header'  => 'ヘッダー',
        'footer'  => 'フッター',
        'search'  => '検索',
        'speed'   => '表示速度',
        'banner'  => '追尾バナー',
    );
}

/**
 * メインレンダリング
 */
function knt_render_theme_settings() {
    if ( ! current_user_can( 'manage_options' ) ) return;

    $tabs       = knt_settings_tabs();
    $active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'display';
    if ( ! isset( $tabs[ $active_tab ] ) ) $active_tab = 'display';

    $settings = get_option( 'knt_settings', array() );
    ?>
    <div class="wrap knt-settings">
        <h1>KNTメディア設定</h1>
        <p class="knt-settings__desc">サイトの表示設定、レイアウト、SNS、CTA などを一元管理できます。</p>

        <nav class="knt-settings__tabs">
            <?php foreach ( $tabs as $slug => $label ) :
                $url = admin_url( 'admin.php?page=knt-settings&tab=' . $slug );
                $cls = ( $active_tab === $slug ) ? ' is-active' : '';
            ?>
                <a href="<?php echo esc_url( $url ); ?>" class="knt-settings__tab<?php echo $cls; ?>">
                    <?php echo esc_html( $label ); ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <form method="post" action="options.php" class="knt-settings__form">
            <?php settings_fields( 'knt_settings_group' ); ?>

            <div class="knt-settings__panel">
                <?php
                $tab_file = KNT_DIR . '/admin/tabs/tab-' . $active_tab . '.php';
                if ( file_exists( $tab_file ) ) {
                    include $tab_file;
                } else {
                    echo '<p>このタブは準備中です。</p>';
                }
                ?>
            </div>

            <?php submit_button( '設定を保存' ); ?>
        </form>
    </div>
    <?php
}

/**
 * トグルスイッチ出力ヘルパー
 */
function knt_toggle( $key, $label, $settings, $default = true ) {
    $val = isset( $settings[ $key ] ) ? $settings[ $key ] : ( $default ? '1' : '0' );
    $checked = ( $val === '1' || $val === true || $val === 'true' ) ? 'checked' : '';
    ?>
    <div class="knt-toggle-row">
        <label class="knt-toggle">
            <input type="hidden" name="knt_settings[<?php echo esc_attr( $key ); ?>]" value="0">
            <input type="checkbox" name="knt_settings[<?php echo esc_attr( $key ); ?>]" value="1" <?php echo $checked; ?>>
            <span class="knt-toggle__slider"></span>
        </label>
        <span class="knt-toggle__label"><?php echo esc_html( $label ); ?></span>
    </div>
    <?php
}

/**
 * テキストフィールド出力ヘルパー
 */
function knt_text_field( $key, $label, $settings, $default = '', $placeholder = '' ) {
    $val = isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
    ?>
    <tr>
        <th><label><?php echo esc_html( $label ); ?></label></th>
        <td>
            <input type="text" name="knt_settings[<?php echo esc_attr( $key ); ?>]"
                   value="<?php echo esc_attr( $val ); ?>"
                   placeholder="<?php echo esc_attr( $placeholder ); ?>"
                   class="regular-text">
        </td>
    </tr>
    <?php
}
