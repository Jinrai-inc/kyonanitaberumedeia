<?php
/**
 * Restaurant Links - Multi-shop Meta Box
 *
 * 1記事に複数店舗を登録可能。
 * 記事本文中の店舗名（見出し or 段落）の直後にリンクカードを自動挿入。
 * 該当箇所がなければ記事末尾にまとめて表示。
 * ショートコード [knt_shop name="店舗名"] での手動配置も可能。
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ========================================
   Service Definitions
   ======================================== */
function knt_restaurant_link_services() {
    return array(
        'tabelog'     => array( 'label' => '食べログ',           'color' => '#F09000', 'placeholder' => 'https://tabelog.com/tokyo/A1301/...' ),
        'hotpepper'   => array( 'label' => 'ホットペッパー',     'color' => '#E60012', 'placeholder' => 'https://www.hotpepper.jp/strJ00...' ),
        'gurunavi'    => array( 'label' => 'ぐるなび',           'color' => '#E4002B', 'placeholder' => 'https://r.gnavi.co.jp/...' ),
        'ikkyuu'      => array( 'label' => '一休.comレストラン', 'color' => '#B8860B', 'placeholder' => 'https://restaurant.ikyu.com/...' ),
        'retty'       => array( 'label' => 'Retty',              'color' => '#FF6E40', 'placeholder' => 'https://retty.me/area/.../...' ),
        'hitosara'    => array( 'label' => 'ヒトサラ',           'color' => '#1A1A1A', 'placeholder' => 'https://hitosara.com/...' ),
        'tablecheck'  => array( 'label' => 'TableCheck',         'color' => '#2563EB', 'placeholder' => 'https://www.tablecheck.com/shops/...' ),
        'toreta'      => array( 'label' => 'トレタ予約',         'color' => '#00B894', 'placeholder' => 'https://toreta.in/...' ),
        'yelp'        => array( 'label' => 'Yelp',               'color' => '#D32323', 'placeholder' => 'https://www.yelp.co.jp/biz/...' ),
        'google_maps' => array( 'label' => 'Google マップ',      'color' => '#4285F4', 'placeholder' => 'https://maps.google.com/?cid=...' ),
        'instagram'   => array( 'label' => 'Instagram',          'color' => '#E4405F', 'placeholder' => 'https://www.instagram.com/shopname/' ),
        'official'    => array( 'label' => '公式サイト',          'color' => '#2A2622', 'placeholder' => 'https://example.com/' ),
        'reservation' => array( 'label' => '予約ページ',          'color' => '#C9553E', 'placeholder' => 'https://example.com/reserve' ),
    );
}

/* ========================================
   Meta Box Registration
   ======================================== */
function knt_add_restaurant_meta_box() {
    add_meta_box(
        'knt_restaurant_links',
        '🍽️ 店舗リンク（複数店舗対応・自動表示）',
        'knt_restaurant_links_meta_box_html',
        'post',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'knt_add_restaurant_meta_box' );

/* ========================================
   Meta Box HTML
   ======================================== */
function knt_restaurant_links_meta_box_html( $post ) {
    wp_nonce_field( 'knt_restaurant_links_nonce', 'knt_restaurant_links_nonce_field' );

    $shops = get_post_meta( $post->ID, '_knt_shops', true );
    if ( ! is_array( $shops ) || empty( $shops ) ) {
        $shops = array( knt_empty_shop() );
    }

    $services = knt_restaurant_link_services();

    // ---- Admin CSS ----
    echo '<style>
        .knt-shops-wrap { }
        .knt-shop-panel { border: 1px solid #dcdcde; border-radius: 6px; padding: 16px; margin-bottom: 16px; background: #f9f9f9; position: relative; }
        .knt-shop-panel.is-collapsed .knt-shop-body { display: none; }
        .knt-shop-toggle { display: flex; align-items: center; gap: 8px; cursor: pointer; user-select: none; }
        .knt-shop-toggle-arrow { transition: transform 0.15s ease; font-size: 12px; }
        .knt-shop-panel.is-collapsed .knt-shop-toggle-arrow { transform: rotate(-90deg); }
        .knt-shop-toggle-title { font-weight: 700; font-size: 14px; color: #1d2327; }
        .knt-shop-toggle-badge { font-size: 11px; color: #646970; margin-left: auto; }
        .knt-shop-remove { position: absolute; top: 12px; right: 12px; background: #d63638; color: #fff; border: none; border-radius: 4px; padding: 2px 8px; font-size: 11px; cursor: pointer; }
        .knt-rl-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 12px 0; }
        .knt-rl-field { display: flex; flex-direction: column; gap: 3px; }
        .knt-rl-field label { font-weight: 600; font-size: 12px; color: #1d2327; }
        .knt-rl-field input { width: 100%; padding: 5px 8px; border: 1px solid #8c8f94; border-radius: 4px; font-size: 13px; }
        .knt-rl-links-title { font-weight: 700; font-size: 12px; color: #1d2327; margin: 14px 0 8px; padding-top: 10px; border-top: 1px solid #dcdcde; }
        .knt-rl-link-row { display: flex; align-items: center; gap: 6px; margin-bottom: 6px; }
        .knt-rl-badge { display: inline-block; min-width: 100px; padding: 2px 6px; border-radius: 3px; color: #fff; font-size: 10px; font-weight: 700; text-align: center; flex-shrink: 0; }
        .knt-rl-link-row input { flex: 1; padding: 4px 8px; border: 1px solid #8c8f94; border-radius: 4px; font-size: 12px; }
        .knt-add-shop { margin-top: 8px; }
        .knt-rl-help { font-size: 12px; color: #646970; margin-top: 12px; line-height: 1.6; }
    </style>';

    echo '<div class="knt-shops-wrap" id="knt-shops-wrap">';

    foreach ( $shops as $idx => $shop ) {
        knt_render_shop_panel( $idx, $shop, $services );
    }

    echo '</div>';

    echo '<button type="button" class="button knt-add-shop" id="knt-add-shop">+ 店舗を追加</button>';

    echo '<p class="knt-rl-help">';
    echo '<strong>自動配置:</strong> 記事本文中に店舗名が含まれていると、その直後にカードが自動挿入されます。<br>';
    echo '<strong>手動配置:</strong> <code>[knt_shop name="店舗名"]</code> をエディタ内に書くと好きな位置に表示できます。<br>';
    echo '<strong>フォールバック:</strong> 本文中に店舗名もショートコードもなければ、記事末尾にまとめて表示されます。';
    echo '</p>';

    // ---- Admin JS ----
    echo '<script>
    (function(){
        var wrap = document.getElementById("knt-shops-wrap");
        var addBtn = document.getElementById("knt-add-shop");
        var idx = ' . count( $shops ) . ';

        // Toggle collapse
        wrap.addEventListener("click", function(e) {
            var toggle = e.target.closest(".knt-shop-toggle");
            if (toggle) {
                toggle.closest(".knt-shop-panel").classList.toggle("is-collapsed");
                return;
            }
            var removeBtn = e.target.closest(".knt-shop-remove");
            if (removeBtn) {
                var panel = removeBtn.closest(".knt-shop-panel");
                if (wrap.querySelectorAll(".knt-shop-panel").length > 1) {
                    panel.remove();
                } else {
                    alert("最低1つの店舗パネルが必要です");
                }
            }
        });

        // Update panel title when shop name changes
        wrap.addEventListener("input", function(e) {
            if (e.target.name && e.target.name.indexOf("[name]") > -1) {
                var panel = e.target.closest(".knt-shop-panel");
                var titleEl = panel.querySelector(".knt-shop-toggle-title");
                titleEl.textContent = e.target.value || "（店舗名未入力）";
            }
        });

        // Add shop
        addBtn.addEventListener("click", function() {
            var template = wrap.querySelector(".knt-shop-panel").cloneNode(true);
            // Clear all inputs
            template.querySelectorAll("input").forEach(function(input) {
                input.value = "";
                input.name = input.name.replace(/knt_shops\[\d+\]/, "knt_shops[" + idx + "]");
            });
            template.querySelector(".knt-shop-toggle-title").textContent = "（店舗名未入力）";
            template.classList.remove("is-collapsed");
            // Update badge
            var badge = template.querySelector(".knt-shop-toggle-badge");
            if (badge) badge.textContent = "店舗 " + (idx + 1);
            wrap.appendChild(template);
            idx++;
        });
    })();
    </script>';
}

/**
 * Empty shop template
 */
function knt_empty_shop() {
    return array(
        'name'     => '',
        'genre'    => '',
        'area'     => '',
        'budget'   => '',
        'hours'    => '',
        'holiday'  => '',
        'address'  => '',
        'tel'      => '',
        'links'    => array(),
    );
}

/**
 * Render single shop panel in admin
 */
function knt_render_shop_panel( $idx, $shop, $services ) {
    $name = isset( $shop['name'] ) ? $shop['name'] : '';
    $prefix = 'knt_shops[' . $idx . ']';
    $link_count = 0;
    if ( isset( $shop['links'] ) && is_array( $shop['links'] ) ) {
        $link_count = count( array_filter( $shop['links'] ) );
    }
    ?>
    <div class="knt-shop-panel">
        <button type="button" class="knt-shop-remove" title="この店舗を削除">&times;</button>
        <div class="knt-shop-toggle">
            <span class="knt-shop-toggle-arrow">▼</span>
            <span class="knt-shop-toggle-title"><?php echo esc_html( $name ?: '（店舗名未入力）' ); ?></span>
            <span class="knt-shop-toggle-badge">店舗 <?php echo $idx + 1; ?><?php if ( $link_count ) echo " ({$link_count}リンク)"; ?></span>
        </div>
        <div class="knt-shop-body">
            <div class="knt-rl-grid">
                <div class="knt-rl-field"><label>店舗名 *</label><input type="text" name="<?php echo esc_attr( $prefix ); ?>[name]" value="<?php echo esc_attr( $name ); ?>" placeholder="例: 鮨 さいとう"></div>
                <div class="knt-rl-field"><label>ジャンル</label><input type="text" name="<?php echo esc_attr( $prefix ); ?>[genre]" value="<?php echo esc_attr( $shop['genre'] ?? '' ); ?>" placeholder="寿司、イタリアン"></div>
                <div class="knt-rl-field"><label>エリア</label><input type="text" name="<?php echo esc_attr( $prefix ); ?>[area]" value="<?php echo esc_attr( $shop['area'] ?? '' ); ?>" placeholder="東京・六本木"></div>
                <div class="knt-rl-field"><label>予算</label><input type="text" name="<?php echo esc_attr( $prefix ); ?>[budget]" value="<?php echo esc_attr( $shop['budget'] ?? '' ); ?>" placeholder="ランチ ¥1,500〜 / ディナー ¥5,000〜"></div>
                <div class="knt-rl-field"><label>営業時間</label><input type="text" name="<?php echo esc_attr( $prefix ); ?>[hours]" value="<?php echo esc_attr( $shop['hours'] ?? '' ); ?>" placeholder="11:30〜14:00 / 17:00〜22:00"></div>
                <div class="knt-rl-field"><label>定休日</label><input type="text" name="<?php echo esc_attr( $prefix ); ?>[holiday]" value="<?php echo esc_attr( $shop['holiday'] ?? '' ); ?>" placeholder="毎週月曜日"></div>
                <div class="knt-rl-field" style="grid-column:1/-1;"><label>住所</label><input type="text" name="<?php echo esc_attr( $prefix ); ?>[address]" value="<?php echo esc_attr( $shop['address'] ?? '' ); ?>" placeholder="東京都港区六本木1-2-3"></div>
                <div class="knt-rl-field"><label>電話番号</label><input type="text" name="<?php echo esc_attr( $prefix ); ?>[tel]" value="<?php echo esc_attr( $shop['tel'] ?? '' ); ?>" placeholder="03-1234-5678"></div>
            </div>
            <div class="knt-rl-links-title">各サイトURL（入力されたもののみ表示）</div>
            <?php foreach ( $services as $key => $svc ) :
                $url = $shop['links'][ $key ] ?? '';
            ?>
            <div class="knt-rl-link-row">
                <span class="knt-rl-badge" style="background:<?php echo esc_attr( $svc['color'] ); ?>;"><?php echo esc_html( $svc['label'] ); ?></span>
                <input type="url" name="<?php echo esc_attr( $prefix ); ?>[links][<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $url ); ?>" placeholder="<?php echo esc_attr( $svc['placeholder'] ); ?>">
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

/* ========================================
   Save Meta
   ======================================== */
function knt_save_restaurant_links( $post_id ) {
    if ( ! isset( $_POST['knt_restaurant_links_nonce_field'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['knt_restaurant_links_nonce_field'], 'knt_restaurant_links_nonce' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    // Legacy single-shop fields cleanup
    delete_post_meta( $post_id, '_knt_shop_name' );
    delete_post_meta( $post_id, '_knt_shop_genre' );
    delete_post_meta( $post_id, '_knt_shop_area' );
    delete_post_meta( $post_id, '_knt_shop_budget' );
    delete_post_meta( $post_id, '_knt_shop_hours' );
    delete_post_meta( $post_id, '_knt_shop_holiday' );
    delete_post_meta( $post_id, '_knt_shop_address' );
    delete_post_meta( $post_id, '_knt_shop_tel' );
    delete_post_meta( $post_id, '_knt_restaurant_links' );

    if ( ! isset( $_POST['knt_shops'] ) || ! is_array( $_POST['knt_shops'] ) ) {
        delete_post_meta( $post_id, '_knt_shops' );
        return;
    }

    $shops = array();
    foreach ( $_POST['knt_shops'] as $raw ) {
        $shop_name = isset( $raw['name'] ) ? sanitize_text_field( $raw['name'] ) : '';
        // Skip completely empty panels
        $links = array();
        if ( isset( $raw['links'] ) && is_array( $raw['links'] ) ) {
            foreach ( $raw['links'] as $k => $v ) {
                $v = esc_url_raw( trim( $v ) );
                if ( $v ) {
                    $links[ sanitize_key( $k ) ] = $v;
                }
            }
        }
        if ( ! $shop_name && empty( $links ) ) {
            continue;
        }
        $shops[] = array(
            'name'    => $shop_name,
            'genre'   => sanitize_text_field( $raw['genre'] ?? '' ),
            'area'    => sanitize_text_field( $raw['area'] ?? '' ),
            'budget'  => sanitize_text_field( $raw['budget'] ?? '' ),
            'hours'   => sanitize_text_field( $raw['hours'] ?? '' ),
            'holiday' => sanitize_text_field( $raw['holiday'] ?? '' ),
            'address' => sanitize_text_field( $raw['address'] ?? '' ),
            'tel'     => sanitize_text_field( $raw['tel'] ?? '' ),
            'links'   => $links,
        );
    }

    if ( $shops ) {
        update_post_meta( $post_id, '_knt_shops', $shops );
    } else {
        delete_post_meta( $post_id, '_knt_shops' );
    }
}
add_action( 'save_post', 'knt_save_restaurant_links' );

/* ========================================
   Shortcode: [knt_shop name="店舗名"]
   ======================================== */
function knt_shop_shortcode( $atts ) {
    $atts = shortcode_atts( array( 'name' => '' ), $atts, 'knt_shop' );
    if ( ! $atts['name'] ) return '';

    $shops = get_post_meta( get_the_ID(), '_knt_shops', true );
    if ( ! is_array( $shops ) ) return '';

    foreach ( $shops as $shop ) {
        if ( $shop['name'] === $atts['name'] ) {
            ob_start();
            knt_render_restaurant_card_from_data( $shop );
            return ob_get_clean();
        }
    }
    return '';
}
add_shortcode( 'knt_shop', 'knt_shop_shortcode' );

/* ========================================
   Auto-insert: 店舗名で本文内の位置を検出
   ======================================== */
function knt_auto_insert_restaurant_cards( $content ) {
    if ( ! is_singular( 'post' ) || ! is_main_query() ) {
        return $content;
    }

    $post_id = get_the_ID();
    $shops   = get_post_meta( $post_id, '_knt_shops', true );

    if ( ! is_array( $shops ) || empty( $shops ) ) {
        return $content;
    }

    $remaining = array();

    foreach ( $shops as $shop ) {
        $name = $shop['name'];
        if ( ! $name ) continue;

        // ショートコードで既に手動配置されていればスキップ
        if ( has_shortcode( $content, 'knt_shop' ) && strpos( $content, 'name="' . $name . '"' ) !== false ) {
            continue;
        }

        // 本文中に店舗名が含まれているか検索
        // 見出し (h2, h3, h4) または段落の中に店舗名があれば、その要素の直後に挿入
        $escaped_name = preg_quote( $name, '/' );
        $pattern = '/(<(?:h[2-4]|p)[^>]*>(?:(?!<\/(?:h[2-4]|p)>).)*' . $escaped_name . '.*?<\/(?:h[2-4]|p)>)/is';

        if ( preg_match( $pattern, $content, $matches, PREG_OFFSET_CAPTURE ) ) {
            $match_str = $matches[0][0];
            $match_pos = $matches[0][1];
            $insert_pos = $match_pos + strlen( $match_str );

            ob_start();
            knt_render_restaurant_card_from_data( $shop );
            $card_html = ob_get_clean();

            // 挿入（後ろから挿入するため位置がズレないよう一旦マーカーを使う）
            $marker = '<!--knt_shop_' . md5( $name ) . '-->';
            $content = substr_replace( $content, $match_str . $marker, $match_pos, strlen( $match_str ) );
            $content = str_replace( $marker, $card_html, $content );
        } else {
            // 本文に名前がなかった → 末尾にまとめる
            $remaining[] = $shop;
        }
    }

    // 末尾フォールバック
    if ( ! empty( $remaining ) ) {
        ob_start();
        foreach ( $remaining as $shop ) {
            knt_render_restaurant_card_from_data( $shop );
        }
        $content .= ob_get_clean();
    }

    return $content;
}
add_filter( 'the_content', 'knt_auto_insert_restaurant_cards', 20 );

/* ========================================
   Render Card from Data Array
   ======================================== */
function knt_render_restaurant_card_from_data( $shop ) {
    $name     = $shop['name'] ?? '';
    $genre    = $shop['genre'] ?? '';
    $area     = $shop['area'] ?? '';
    $budget   = $shop['budget'] ?? '';
    $hours    = $shop['hours'] ?? '';
    $holiday  = $shop['holiday'] ?? '';
    $address  = $shop['address'] ?? '';
    $tel      = $shop['tel'] ?? '';
    $links    = $shop['links'] ?? array();
    $services = knt_restaurant_link_services();

    if ( ! is_array( $links ) ) $links = array();
    ?>
    <div class="restaurant-card" data-shop="<?php echo esc_attr( $name ); ?>">
        <div class="restaurant-card__header">
            <h3 class="restaurant-card__name"><?php echo esc_html( $name ?: '店舗情報' ); ?></h3>
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
                       target="_blank" rel="noopener noreferrer sponsored">
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

/* ========================================
   Structured Data (JSON-LD) for all shops
   ======================================== */
function knt_restaurant_structured_data() {
    if ( ! is_singular( 'post' ) ) return;

    $shops = get_post_meta( get_the_ID(), '_knt_shops', true );
    if ( ! is_array( $shops ) ) return;

    $image = get_the_post_thumbnail_url( get_the_ID(), 'full' );

    foreach ( $shops as $shop ) {
        $name = $shop['name'] ?? '';
        if ( ! $name ) continue;

        $data = array(
            '@context' => 'https://schema.org',
            '@type'    => 'Restaurant',
            'name'     => $name,
            'url'      => get_permalink(),
        );
        if ( ! empty( $shop['address'] ) ) {
            $data['address'] = array( '@type' => 'PostalAddress', 'addressCountry' => 'JP', 'streetAddress' => $shop['address'] );
        }
        if ( ! empty( $shop['tel'] ) )   $data['telephone']     = $shop['tel'];
        if ( ! empty( $shop['genre'] ) ) $data['servesCuisine'] = $shop['genre'];
        if ( ! empty( $shop['hours'] ) ) $data['openingHours']  = $shop['hours'];
        if ( $image )                     $data['image']         = $image;

        echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
    }
}
add_action( 'wp_head', 'knt_restaurant_structured_data' );
