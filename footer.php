</main><!-- #main -->

<?php
$footer_style = get_theme_mod( 'knt_footer_style', 'dark' );
$footer_class = ( $footer_style === 'light' ) ? 'site-footer site-footer--light' : 'site-footer';
?>

<footer class="<?php echo esc_attr( $footer_class ); ?>" role="contentinfo"
    <?php if ( $footer_style === 'light' ) : ?>
        style="background: var(--color-bg-secondary); color: var(--color-text-sub);"
    <?php endif; ?>
>
    <div class="container">
        <div class="footer__grid">
            <div class="footer__about">
                <?php if ( has_custom_logo() ) : ?>
                    <div style="margin-bottom: 12px;">
                        <?php the_custom_logo(); ?>
                    </div>
                <?php else : ?>
                    <div style="font-family: var(--font-heading); font-size: 18px; font-weight: 700; margin-bottom: 12px;
                        <?php echo ( $footer_style === 'light' ) ? 'color: var(--color-text-main);' : 'color: #fff;'; ?>">
                        <?php bloginfo( 'name' ); ?>
                    </div>
                <?php endif; ?>
                <p class="footer__brand-text"
                    <?php if ( $footer_style === 'light' ) : ?>
                        style="color: var(--color-text-sub);"
                    <?php endif; ?>
                >
                    <?php echo esc_html( get_theme_mod( 'knt_footer_description', '「今日何食べる？」は、毎日の食事選びをもっと楽しくするグルメアプリです。' ) ); ?>
                </p>
            </div>

            <div>
                <h4 class="footer__heading"
                    <?php if ( $footer_style === 'light' ) : ?>
                        style="color: var(--color-text-main);"
                    <?php endif; ?>
                >カテゴリ</h4>
                <ul class="footer__links">
                    <?php
                    $cats = get_categories( array( 'hide_empty' => false, 'number' => 6 ) );
                    foreach ( $cats as $cat ) :
                    ?>
                        <li><a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>"
                            <?php if ( $footer_style === 'light' ) : ?>
                                style="color: var(--color-text-sub);"
                            <?php endif; ?>
                        ><?php echo esc_html( $cat->name ); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div>
                <h4 class="footer__heading"
                    <?php if ( $footer_style === 'light' ) : ?>
                        style="color: var(--color-text-main);"
                    <?php endif; ?>
                >リンク</h4>
                <ul class="footer__links">
                    <li><a href="<?php echo esc_url( get_theme_mod( 'knt_header_cta_url', 'https://kyou-nani-taberu.app' ) ); ?>"
                        <?php if ( $footer_style === 'light' ) : ?>style="color: var(--color-text-sub);"<?php endif; ?>
                    >アプリを使う</a></li>
                    <?php if ( get_theme_mod( 'knt_company_url' ) ) : ?>
                        <li><a href="<?php echo esc_url( get_theme_mod( 'knt_company_url' ) ); ?>"
                            <?php if ( $footer_style === 'light' ) : ?>style="color: var(--color-text-sub);"<?php endif; ?>
                        ><?php echo esc_html( get_theme_mod( 'knt_company_name', '株式会社仁頼' ) ); ?></a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <?php if ( has_nav_menu( 'footer' ) ) : ?>
            <div>
                <h4 class="footer__heading"
                    <?php if ( $footer_style === 'light' ) : ?>
                        style="color: var(--color-text-main);"
                    <?php endif; ?>
                >メニュー</h4>
                <ul class="footer__links">
                    <?php
                    wp_nav_menu( array(
                        'theme_location' => 'footer',
                        'container'      => false,
                        'items_wrap'     => '%3$s',
                        'depth'          => 1,
                        'walker'         => new KNT_Footer_Walker(),
                    ) );
                    ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>

        <div class="footer__bottom"
            <?php if ( $footer_style === 'light' ) : ?>
                style="border-top-color: var(--color-border);"
            <?php endif; ?>
        >
            <p class="footer__copyright"
                <?php if ( $footer_style === 'light' ) : ?>
                    style="color: var(--color-text-light);"
                <?php endif; ?>
            >
                <?php echo esc_html( get_theme_mod( 'knt_copyright', '© 2025 Jinrai Inc.' ) ); ?>
            </p>
            <div class="footer__legal">
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
                    <a href="<?php echo esc_url( $privacy_url ); ?>"
                        <?php if ( $footer_style === 'light' ) : ?>style="color: var(--color-text-light);"<?php endif; ?>
                    >プライバシーポリシー</a>
                <?php endif; ?>

                <?php $terms_url = get_theme_mod( 'knt_terms_url' ); ?>
                <?php if ( $terms_url ) : ?>
                    <a href="<?php echo esc_url( $terms_url ); ?>"
                        <?php if ( $footer_style === 'light' ) : ?>style="color: var(--color-text-light);"<?php endif; ?>
                    >利用規約</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</footer>

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
