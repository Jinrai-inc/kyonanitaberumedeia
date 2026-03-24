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

<?php wp_footer(); ?>
</body>
</html>
