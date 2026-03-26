</main><!-- #main -->

<?php
$footer_style = get_theme_mod( 'knt_footer_style', 'dark' );
$footer_class = ( $footer_style === 'light' ) ? 'site-footer site-footer--light' : 'site-footer';
$is_light     = ( $footer_style === 'light' );
$text_color   = $is_light ? 'color: var(--color-text-sub);' : '';
$heading_color = $is_light ? 'color: var(--color-text-main);' : '';
$light_color  = $is_light ? 'color: var(--color-text-light);' : '';
?>

<footer class="<?php echo esc_attr( $footer_class ); ?>" role="contentinfo"
    <?php if ( $is_light ) : ?>
        style="background: var(--color-bg-secondary); color: var(--color-text-sub);"
    <?php endif; ?>
>
    <div class="container">
        <div class="footer__grid">
            <!-- サイト紹介 -->
            <div class="footer__about">
                <?php if ( has_custom_logo() ) : ?>
                    <div style="margin-bottom: 12px;">
                        <?php the_custom_logo(); ?>
                    </div>
                <?php else : ?>
                    <div style="font-family: var(--font-heading); font-size: 18px; font-weight: 700; margin-bottom: 12px;
                        <?php echo $is_light ? 'color: var(--color-text-main);' : 'color: #fff;'; ?>">
                        <?php bloginfo( 'name' ); ?>
                    </div>
                <?php endif; ?>
                <p class="footer__brand-text" <?php if ( $is_light ) : ?>style="<?php echo $text_color; ?>"<?php endif; ?>>
                    <?php echo esc_html( get_theme_mod( 'knt_footer_description', '「今日何食べる？」は、毎日の食事選びをもっと楽しくするグルメメディアです。' ) ); ?>
                </p>
                <p class="footer__brand-text" style="margin-top: 8px; font-size: 11px; <?php echo $is_light ? $text_color : 'color: rgba(255,255,255,0.5);'; ?>">
                    ※ 本サイトに掲載されている店舗情報の一部は、ホットペッパーグルメ Webサービスを利用して取得しています。<br>
                    ※ 掲載情報は取得時点のものです。最新情報は各店舗の公式サイト等でご確認ください。<br>
                    ※ 本サイトにはPR・広告を含むコンテンツが含まれます。
                </p>
            </div>

            <!-- カテゴリ -->
            <div>
                <h4 class="footer__heading" <?php if ( $is_light ) : ?>style="<?php echo $heading_color; ?>"<?php endif; ?>>カテゴリ</h4>
                <ul class="footer__links">
                    <?php
                    $cats = get_categories( array( 'hide_empty' => false, 'number' => 6, 'parent' => 0 ) );
                    foreach ( $cats as $cat ) :
                    ?>
                        <li><a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>"
                            <?php if ( $is_light ) : ?>style="<?php echo $text_color; ?>"<?php endif; ?>
                        ><?php echo esc_html( $cat->name ); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- 運営会社・リンク -->
            <div>
                <h4 class="footer__heading" <?php if ( $is_light ) : ?>style="<?php echo $heading_color; ?>"<?php endif; ?>>運営会社</h4>
                <ul class="footer__links">
                    <li><a href="https://jinrai.co.jp" target="_blank" rel="noopener noreferrer"
                        <?php if ( $is_light ) : ?>style="<?php echo $text_color; ?>"<?php endif; ?>
                    >株式会社仁頼</a></li>
                    <li><a href="<?php echo esc_url( get_theme_mod( 'knt_header_cta_url', 'https://kyou-nani-taberu.app' ) ); ?>"
                        <?php if ( $is_light ) : ?>style="<?php echo $text_color; ?>"<?php endif; ?>
                    >アプリを使う</a></li>
                    <li><a href="https://jinrai.co.jp/contact" target="_blank" rel="noopener noreferrer"
                        <?php if ( $is_light ) : ?>style="<?php echo $text_color; ?>"<?php endif; ?>
                    >お問い合わせ</a></li>
                </ul>
            </div>

            <!-- 法的情報 -->
            <div>
                <h4 class="footer__heading" <?php if ( $is_light ) : ?>style="<?php echo $heading_color; ?>"<?php endif; ?>>ポリシー</h4>
                <ul class="footer__links">
                    <?php
                    $privacy_url = get_theme_mod( 'knt_privacy_url' );
                    if ( ! $privacy_url ) {
                        $privacy_page = get_page_by_path( 'privacy-policy' );
                        if ( $privacy_page ) {
                            $privacy_url = get_permalink( $privacy_page->ID );
                        }
                    }
                    if ( $privacy_url ) :
                    ?>
                        <li><a href="<?php echo esc_url( $privacy_url ); ?>"
                            <?php if ( $is_light ) : ?>style="<?php echo $text_color; ?>"<?php endif; ?>
                        >プライバシーポリシー</a></li>
                    <?php endif; ?>

                    <?php $terms_url = get_theme_mod( 'knt_terms_url' ); ?>
                    <?php if ( $terms_url ) : ?>
                        <li><a href="<?php echo esc_url( $terms_url ); ?>"
                            <?php if ( $is_light ) : ?>style="<?php echo $text_color; ?>"<?php endif; ?>
                        >利用規約</a></li>
                    <?php endif; ?>

                    <li><a href="https://jinrai.co.jp/legal/commerce" target="_blank" rel="noopener noreferrer"
                        <?php if ( $is_light ) : ?>style="<?php echo $text_color; ?>"<?php endif; ?>
                    >特定商取引法に基づく表記</a></li>
                </ul>
            </div>
        </div>

        <!-- API クレジット + コピーライト -->
        <div class="footer__bottom" <?php if ( $is_light ) : ?>style="border-top-color: var(--color-border);"<?php endif; ?>>
            <div class="footer__credits">
                <a href="http://webservice.recruit.co.jp/" target="_blank" rel="noopener noreferrer">
                    <img src="http://webservice.recruit.co.jp/banner/hotpepper-s.gif"
                         alt="ホットペッパーグルメ Webサービス"
                         width="135" height="17"
                         loading="lazy"
                         style="vertical-align: middle;">
                </a>
            </div>
            <p class="footer__copyright" <?php if ( $is_light ) : ?>style="<?php echo $light_color; ?>"<?php endif; ?>>
                &copy; <?php echo date( 'Y' ); ?> 株式会社仁頼 All Rights Reserved.
            </p>
        </div>
    </div>
</footer>

<!-- Sticky App Banner (右下追尾) -->
<div class="sticky-app-banner" id="sticky-app-banner">
    <button class="sticky-app-banner__close" id="sticky-app-banner-close" aria-label="閉じる">&times;</button>
    <?php
    $sticky_logo = get_theme_mod( 'knt_app_logo', '' );
    if ( $sticky_logo ) :
    ?>
        <img src="<?php echo esc_url( $sticky_logo ); ?>" alt="" class="sticky-app-banner__icon" width="40" height="40">
    <?php endif; ?>
    <div class="sticky-app-banner__text">
        <strong>今日何食べる？</strong>
        <span>近くのお店をすぐ検索</span>
    </div>
    <a href="<?php echo esc_url( get_theme_mod( 'knt_header_cta_url', 'https://kyou-nani-taberu.app' ) ); ?>"
       class="sticky-app-banner__btn" target="_blank" rel="noopener noreferrer">
        開く
    </a>
</div>

<!-- Back to Top -->
<button class="back-to-top" id="back-to-top" aria-label="ページトップへ戻る">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
</button>

<!-- Mobile Bottom Nav -->
<nav class="mobile-bottom-nav" id="mobile-bottom-nav" role="navigation" aria-label="モバイルナビゲーション">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="mobile-bottom-nav__item<?php echo is_front_page() ? ' is-active' : ''; ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        <span>ホーム</span>
    </a>
    <a href="<?php echo esc_url( home_url( '/?s=' ) ); ?>" class="mobile-bottom-nav__item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <span>検索</span>
    </a>
    <a href="#" class="mobile-bottom-nav__item" id="mobile-bookmark-nav">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
        <span>保存済み</span>
    </a>
    <?php if ( get_theme_mod( 'knt_header_cta_show', true ) ) : ?>
    <a href="<?php echo esc_url( get_theme_mod( 'knt_header_cta_url', 'https://kyou-nani-taberu.app' ) ); ?>" class="mobile-bottom-nav__item mobile-bottom-nav__item--app" target="_blank" rel="noopener noreferrer">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
        <span>アプリ</span>
    </a>
    <?php endif; ?>
</nav>

<!-- Bookmark Modal -->
<div class="bookmark-modal" id="bookmark-modal">
    <div class="bookmark-modal__overlay"></div>
    <div class="bookmark-modal__content">
        <div class="bookmark-modal__header">
            <h3>保存した記事</h3>
            <button class="bookmark-modal__close" aria-label="閉じる">&times;</button>
        </div>
        <div class="bookmark-modal__list" id="bookmark-list">
            <p class="bookmark-modal__empty">まだ保存した記事がありません</p>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div class="toast" id="toast"></div>

<?php wp_footer(); ?>
</body>
</html>
