<?php
/**
 * Restaurant Links - Custom Meta Box
 *
 * 記事に紐づく店舗の各種予約・口コミサイトURLを管理し、
 * 記事内に自動でリンクカードを表示する仕組み。
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * サポートするグルメサイト定義
 * key => [ label, icon_svg, color, placeholder, domain_pattern ]
 */
function knt_restaurant_link_services() {
    return array(
        'tabelog' => array(
            'label'   => '食べログ',
            'color'   => '#F09000',
            'domain'  => 'tabelog.com',
            'placeholder' => 'https://tabelog.com/tokyo/A1301/...',
        ),
        'hotpepper' => array(
            'label'   => 'ホットペッパー',
            'color'   => '#E60012',
            'domain'  => 'hotpepper.jp',
            'placeholder' => 'https://www.hotpepper.jp/strJ00...',
        ),
        'gurunavi' => array(
            'label'   => 'ぐるなび',
            'color'   => '#E4002B',
            'domain'  => 'gnavi.co.jp',
            'placeholder' => 'https://r.gnavi.co.jp/...',
        ),
        'ikkyuu' => array(
            'label'   => '一休.comレストラン',
            'color'   => '#B8860B',
            'domain'  => 'ikyu.com',
            'placeholder' => 'https://restaurant.ikyu.com/...',
        ),
        'retty' => array(
            'label'   => 'Retty',
            'color'   => '#FF6E40',
            'domain'  => 'retty.me',
            'placeholder' => 'https://retty.me/area/.../...',
        ),
        'hitosara' => array(
            'label'   => 'ヒトサラ',
            'color'   => '#1A1A1A',
            'domain'  => 'hitosara.com',
            'placeholder' => 'https://hitosara.com/...',
        ),
        'tablecheck' => array(
            'label'   => 'TableCheck',
            'color'   => '#2563EB',
            'domain'  => 'tablecheck.com',
            'placeholder' => 'https://www.tablecheck.com/shops/...',
        ),
        'toreta' => array(
            'label'   => 'トレタ予約',
            'color'   => '#00B894',
            'domain'  => 'toreta.in',
            'placeholder' => 'https://toreta.in/...',
        ),
        'yelp' => array(
            'label'   => 'Yelp',
            'color'   => '#D32323',
            'domain'  => 'yelp.co.jp',
            'placeholder' => 'https://www.yelp.co.jp/biz/...',
        ),
        'google_maps' => array(
            'label'   => 'Google マップ',
            'color'   => '#4285F4',
            'domain'  => 'google.com/maps',
            'placeholder' => 'https://maps.google.com/?cid=... or goo.gl/maps/...',
        ),
        'instagram' => array(
            'label'   => 'Instagram',
            'color'   => '#E4405F',
            'domain'  => 'instagram.com',
            'placeholder' => 'https://www.instagram.com/shopname/',
        ),
        'official' => array(
            'label'   => '公式サイト',
            'color'   => '#2A2622',
            'domain'  => '',
            'placeholder' => 'https://example.com/',
        ),
        'reservation' => array(
            'label'   => '予約ページ',
            'color'   => '#C9553E',
            'domain'  => '',
            'placeholder' => 'https://example.com/reserve',
        ),
    );
}

/**
 * Meta box 登録
 */
function knt_add_restaurant_meta_box() {
    add_meta_box(
        'knt_restaurant_links',
        '🍽️ 店舗リンク（自動表示）',
        'knt_restaurant_links_meta_box_html',
        'post',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'knt_add_restaurant_meta_box' );

/**
 * Meta box HTML
 */
function knt_restaurant_links_meta_box_html( $post ) {
    wp_nonce_field( 'knt_restaurant_links_nonce', 'knt_restaurant_links_nonce_field' );

    $services = knt_restaurant_link_services();
    $saved    = get_post_meta( $post->ID, '_knt_restaurant_links', true );
    if ( ! is_array( $saved ) ) {
        $saved = array();
    }

    // 店舗名
    $shop_name = get_post_meta( $post->ID, '_knt_shop_name', true );
    // 店舗ジャンル
    $shop_genre = get_post_meta( $post->ID, '_knt_shop_genre', true );
    // 店舗エリア
    $shop_area = get_post_meta( $post->ID, '_knt_shop_area', true );
    // 予算
    $shop_budget = get_post_meta( $post->ID, '_knt_shop_budget', true );
    // 営業時間
    $shop_hours = get_post_meta( $post->ID, '_knt_shop_hours', true );
    // 定休日
    $shop_holiday = get_post_meta( $post->ID, '_knt_shop_holiday', true );
    // 住所
    $shop_address = get_post_meta( $post->ID, '_knt_shop_address', true );
    // 電話番号
    $shop_tel = get_post_meta( $post->ID, '_knt_shop_tel', true );

    echo '<style>
        .knt-rl-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px; }
        .knt-rl-field { display: flex; flex-direction: column; gap: 4px; }
        .knt-rl-field label { font-weight: 600; font-size: 13px; color: #1d2327; }
        .knt-rl-field input, .knt-rl-field textarea { width: 100%; padding: 6px 10px; border: 1px solid #8c8f94; border-radius: 4px; font-size: 13px; }
        .knt-rl-divider { border-top: 1px solid #dcdcde; margin: 16px 0; padding-top: 12px; }
        .knt-rl-divider-title { font-weight: 700; font-size: 13px; color: #1d2327; margin-bottom: 12px; }
        .knt-rl-link-row { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
        .knt-rl-link-row .knt-rl-badge { display: inline-block; min-width: 110px; padding: 3px 8px; border-radius: 4px; color: #fff; font-size: 11px; font-weight: 700; text-align: center; flex-shrink: 0; }
        .knt-rl-link-row input { flex: 1; padding: 6px 10px; border: 1px solid #8c8f94; border-radius: 4px; font-size: 13px; }
        .knt-rl-link-row .knt-rl-status { width: 20px; flex-shrink: 0; text-align: center; font-size: 14px; }
        .knt-rl-help { font-size: 12px; color: #646970; margin-top: 8px; }
    </style>';

    // 店舗基本情報
    echo '<div class="knt-rl-grid">';
    echo '<div class="knt-rl-field"><label>店舗名</label><input type="text" name="knt_shop_name" value="' . esc_attr( $shop_name ) . '" placeholder="例: 鮨 さいとう"></div>';
    echo '<div class="knt-rl-field"><label>ジャンル</label><input type="text" name="knt_shop_genre" value="' . esc_attr( $shop_genre ) . '" placeholder="例: 寿司、イタリアン、焼肉"></div>';
    echo '<div class="knt-rl-field"><label>エリア</label><input type="text" name="knt_shop_area" value="' . esc_attr( $shop_area ) . '" placeholder="例: 東京・六本木"></div>';
    echo '<div class="knt-rl-field"><label>予算</label><input type="text" name="knt_shop_budget" value="' . esc_attr( $shop_budget ) . '" placeholder="例: ランチ ¥1,500〜 / ディナー ¥5,000〜"></div>';
    echo '<div class="knt-rl-field"><label>営業時間</label><input type="text" name="knt_shop_hours" value="' . esc_attr( $shop_hours ) . '" placeholder="例: 11:30〜14:00 / 17:00〜22:00"></div>';
    echo '<div class="knt-rl-field"><label>定休日</label><input type="text" name="knt_shop_holiday" value="' . esc_attr( $shop_holiday ) . '" placeholder="例: 毎週月曜日"></div>';
    echo '<div class="knt-rl-field" style="grid-column: 1 / -1;"><label>住所</label><input type="text" name="knt_shop_address" value="' . esc_attr( $shop_address ) . '" placeholder="例: 東京都港区六本木1-2-3 ○○ビル 2F"></div>';
    echo '<div class="knt-rl-field"><label>電話番号</label><input type="text" name="knt_shop_tel" value="' . esc_attr( $shop_tel ) . '" placeholder="例: 03-1234-5678"></div>';
    echo '</div>';

    // リンク入力
    echo '<div class="knt-rl-divider"><div class="knt-rl-divider-title">各サイトURL（入力されたもののみ自動表示）</div></div>';

    foreach ( $services as $key => $service ) {
        $value = isset( $saved[ $key ] ) ? $saved[ $key ] : '';
        $status = $value ? '✅' : '—';
        echo '<div class="knt-rl-link-row">';
        echo '<span class="knt-rl-badge" style="background:' . esc_attr( $service['color'] ) . ';">' . esc_html( $service['label'] ) . '</span>';
        echo '<input type="url" name="knt_restaurant_links[' . esc_attr( $key ) . ']" value="' . esc_attr( $value ) . '" placeholder="' . esc_attr( $service['placeholder'] ) . '">';
        echo '<span class="knt-rl-status">' . $status . '</span>';
        echo '</div>';
    }

    echo '<p class="knt-rl-help">💡 URLを入力すると記事本文の下に自動でリンクカードが表示されます。空欄のサービスは非表示になります。</p>';
}

/**
 * Meta box 保存
 */
function knt_save_restaurant_links( $post_id ) {
    if ( ! isset( $_POST['knt_restaurant_links_nonce_field'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['knt_restaurant_links_nonce_field'], 'knt_restaurant_links_nonce' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // 店舗基本情報
    $text_fields = array(
        'knt_shop_name', 'knt_shop_genre', 'knt_shop_area',
        'knt_shop_budget', 'knt_shop_hours', 'knt_shop_holiday',
        'knt_shop_address', 'knt_shop_tel',
    );
    foreach ( $text_fields as $field ) {
        if ( isset( $_POST[ $field ] ) ) {
            update_post_meta( $post_id, '_' . $field, sanitize_text_field( $_POST[ $field ] ) );
        }
    }

    // リンク
    if ( isset( $_POST['knt_restaurant_links'] ) && is_array( $_POST['knt_restaurant_links'] ) ) {
        $links = array();
        foreach ( $_POST['knt_restaurant_links'] as $key => $url ) {
            $url = esc_url_raw( trim( $url ) );
            if ( $url ) {
                $links[ sanitize_key( $key ) ] = $url;
            }
        }
        update_post_meta( $post_id, '_knt_restaurant_links', $links );
    } else {
        delete_post_meta( $post_id, '_knt_restaurant_links' );
    }
}
add_action( 'save_post', 'knt_save_restaurant_links' );

/**
 * 記事本文末尾に店舗情報を自動挿入（the_content フィルター）
 */
function knt_auto_insert_restaurant_card( $content ) {
    if ( ! is_singular( 'post' ) || ! is_main_query() ) {
        return $content;
    }

    $post_id = get_the_ID();
    $links   = get_post_meta( $post_id, '_knt_restaurant_links', true );
    $name    = get_post_meta( $post_id, '_knt_shop_name', true );

    // 店舗名もリンクもなければ何も出さない
    if ( ! $name && ( ! is_array( $links ) || empty( $links ) ) ) {
        return $content;
    }

    ob_start();
    knt_render_restaurant_card( $post_id );
    $card = ob_get_clean();

    return $content . $card;
}
add_filter( 'the_content', 'knt_auto_insert_restaurant_card', 20 );

/**
 * 店舗情報カードのレンダリング
 */
function knt_render_restaurant_card( $post_id ) {
    $name     = get_post_meta( $post_id, '_knt_shop_name', true );
    $genre    = get_post_meta( $post_id, '_knt_shop_genre', true );
    $area     = get_post_meta( $post_id, '_knt_shop_area', true );
    $budget   = get_post_meta( $post_id, '_knt_shop_budget', true );
    $hours    = get_post_meta( $post_id, '_knt_shop_hours', true );
    $holiday  = get_post_meta( $post_id, '_knt_shop_holiday', true );
    $address  = get_post_meta( $post_id, '_knt_shop_address', true );
    $tel      = get_post_meta( $post_id, '_knt_shop_tel', true );
    $links    = get_post_meta( $post_id, '_knt_restaurant_links', true );
    $services = knt_restaurant_link_services();

    if ( ! is_array( $links ) ) {
        $links = array();
    }
    ?>
    <div class="restaurant-card">
        <div class="restaurant-card__header">
            <h3 class="restaurant-card__name"><?php echo esc_html( $name ?: get_the_title( $post_id ) ); ?></h3>
            <?php if ( $genre ) : ?>
                <span class="restaurant-card__genre"><?php echo esc_html( $genre ); ?></span>
            <?php endif; ?>
        </div>

        <?php if ( $area || $budget || $hours || $holiday || $address || $tel ) : ?>
        <dl class="restaurant-card__info">
            <?php if ( $area ) : ?>
                <div class="restaurant-card__info-row">
                    <dt><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>エリア</dt>
                    <dd><?php echo esc_html( $area ); ?></dd>
                </div>
            <?php endif; ?>
            <?php if ( $budget ) : ?>
                <div class="restaurant-card__info-row">
                    <dt><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>予算</dt>
                    <dd><?php echo esc_html( $budget ); ?></dd>
                </div>
            <?php endif; ?>
            <?php if ( $hours ) : ?>
                <div class="restaurant-card__info-row">
                    <dt><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>営業時間</dt>
                    <dd><?php echo esc_html( $hours ); ?></dd>
                </div>
            <?php endif; ?>
            <?php if ( $holiday ) : ?>
                <div class="restaurant-card__info-row">
                    <dt><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>定休日</dt>
                    <dd><?php echo esc_html( $holiday ); ?></dd>
                </div>
            <?php endif; ?>
            <?php if ( $address ) : ?>
                <div class="restaurant-card__info-row">
                    <dt><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>住所</dt>
                    <dd><?php echo esc_html( $address ); ?></dd>
                </div>
            <?php endif; ?>
            <?php if ( $tel ) : ?>
                <div class="restaurant-card__info-row">
                    <dt><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>電話</dt>
                    <dd><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $tel ) ); ?>"><?php echo esc_html( $tel ); ?></a></dd>
                </div>
            <?php endif; ?>
        </dl>
        <?php endif; ?>

        <?php if ( ! empty( $links ) ) : ?>
        <div class="restaurant-card__links">
            <p class="restaurant-card__links-title">ネット予約・口コミを見る</p>
            <div class="restaurant-card__links-grid">
                <?php foreach ( $links as $key => $url ) :
                    if ( ! isset( $services[ $key ] ) || ! $url ) continue;
                    $svc = $services[ $key ];
                ?>
                    <a href="<?php echo esc_url( $url ); ?>"
                       class="restaurant-card__link"
                       style="--link-color: <?php echo esc_attr( $svc['color'] ); ?>;"
                       target="_blank"
                       rel="noopener noreferrer sponsored">
                        <span class="restaurant-card__link-dot" style="background: <?php echo esc_attr( $svc['color'] ); ?>;"></span>
                        <span class="restaurant-card__link-label"><?php echo esc_html( $svc['label'] ); ?></span>
                        <svg class="restaurant-card__link-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * 店舗情報の構造化データ (JSON-LD Restaurant)
 */
function knt_restaurant_structured_data() {
    if ( ! is_singular( 'post' ) ) {
        return;
    }

    $post_id = get_the_ID();
    $name    = get_post_meta( $post_id, '_knt_shop_name', true );
    if ( ! $name ) {
        return;
    }

    $address = get_post_meta( $post_id, '_knt_shop_address', true );
    $tel     = get_post_meta( $post_id, '_knt_shop_tel', true );
    $genre   = get_post_meta( $post_id, '_knt_shop_genre', true );
    $hours   = get_post_meta( $post_id, '_knt_shop_hours', true );
    $image   = get_the_post_thumbnail_url( $post_id, 'full' );

    $data = array(
        '@context' => 'https://schema.org',
        '@type'    => 'Restaurant',
        'name'     => $name,
        'url'      => get_permalink( $post_id ),
    );

    if ( $address ) {
        $data['address'] = array(
            '@type'          => 'PostalAddress',
            'addressCountry' => 'JP',
            'streetAddress'  => $address,
        );
    }
    if ( $tel ) {
        $data['telephone'] = $tel;
    }
    if ( $genre ) {
        $data['servesCuisine'] = $genre;
    }
    if ( $hours ) {
        $data['openingHours'] = $hours;
    }
    if ( $image ) {
        $data['image'] = $image;
    }

    echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>';
}
add_action( 'wp_head', 'knt_restaurant_structured_data' );
