<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">

    <?php // OGP Tags ?>
    <?php if ( is_singular() ) : ?>
        <meta property="og:title" content="<?php the_title(); ?> | <?php bloginfo( 'name' ); ?>">
        <meta property="og:description" content="<?php echo esc_attr( wp_trim_words( get_the_excerpt(), 55 ) ); ?>">
        <meta property="og:type" content="article">
        <meta property="og:url" content="<?php the_permalink(); ?>">
        <?php if ( has_post_thumbnail() ) : ?>
            <meta property="og:image" content="<?php echo esc_url( get_the_post_thumbnail_url( null, 'knt-hero' ) ); ?>">
        <?php elseif ( get_theme_mod( 'knt_ogp_image' ) ) : ?>
            <meta property="og:image" content="<?php echo esc_url( get_theme_mod( 'knt_ogp_image' ) ); ?>">
        <?php endif; ?>
    <?php else : ?>
        <meta property="og:title" content="<?php bloginfo( 'name' ); ?>">
        <meta property="og:description" content="<?php bloginfo( 'description' ); ?>">
        <meta property="og:type" content="website">
        <meta property="og:url" content="<?php echo esc_url( home_url( '/' ) ); ?>">
        <?php if ( get_theme_mod( 'knt_ogp_image' ) ) : ?>
            <meta property="og:image" content="<?php echo esc_url( get_theme_mod( 'knt_ogp_image' ) ); ?>">
        <?php endif; ?>
    <?php endif; ?>
    <meta property="og:site_name" content="<?php bloginfo( 'name' ); ?>">
    <meta property="og:locale" content="ja_JP">

    <?php // Twitter Card ?>
    <meta name="twitter:card" content="summary_large_image">
    <?php if ( get_theme_mod( 'knt_twitter_handle' ) ) : ?>
        <meta name="twitter:site" content="<?php echo esc_attr( get_theme_mod( 'knt_twitter_handle' ) ); ?>">
    <?php endif; ?>

    <?php // Google Analytics ?>
    <?php $ga_id = get_theme_mod( 'knt_ga_id' ); ?>
    <?php if ( $ga_id ) : ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $ga_id ); ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?php echo esc_js( $ga_id ); ?>');
        </script>
    <?php endif; ?>

    <?php // AdSense head code ?>
    <?php $adsense_head = get_theme_mod( 'knt_adsense_head' ); ?>
    <?php if ( $adsense_head ) : ?>
        <?php echo $adsense_head; ?>
    <?php endif; ?>

    <?php // ValueCommerce LinkSwitch ?>
    <script type="text/javascript">var vc_pid = "892570587";</script>
    <script type="text/javascript" src="//aml.valuecommerce.com/vcdal.js" async></script>

    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header" role="banner">
    <div class="site-header__inner">
        <div class="site-header__logo">
            <?php if ( has_custom_logo() ) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <strong style="font-family: var(--font-heading); font-size: 18px; color: var(--color-text-main);">
                        <?php bloginfo( 'name' ); ?>
                    </strong>
                </a>
            <?php endif; ?>
        </div>

        <nav class="site-header__nav" role="navigation" aria-label="メインナビゲーション">
            <?php
            if ( has_nav_menu( 'primary' ) ) {
                wp_nav_menu( array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'items_wrap'     => '%3$s',
                    'depth'          => 1,
                ) );
            } else {
                // ナビゲーション: 表示名 → 検索キーワード のマッピング
                // タグページではなく検索結果で確実に記事を表示
                $nav_items = array(
                    'ラーメン' => 'ラーメン',
                    'デート'   => 'デート',
                    '飲み会'   => '居酒屋',
                    'ランチ'   => 'ランチ',
                    '接待'     => '接待',
                    '女子会'   => '女子会',
                    '子連れ'   => '子連れ',
                );
                foreach ( $nav_items as $display => $keyword ) {
                    $url = home_url( '/?s=' . urlencode( $keyword ) );
                    echo '<a href="' . esc_url( $url ) . '">' . esc_html( $display ) . '</a>';
                }
            }
            ?>
        </nav>

        <div class="site-header__actions">
            <?php if ( get_theme_mod( 'knt_header_cta_show', true ) ) : ?>
                <a href="<?php echo esc_url( get_theme_mod( 'knt_header_cta_url', 'https://kyou-nani-taberu.app' ) ); ?>"
                   class="btn btn--primary btn--small"
                   target="_blank"
                   rel="noopener noreferrer">
                    <?php echo esc_html( get_theme_mod( 'knt_header_cta_text', 'アプリで探す' ) ); ?>
                </a>
            <?php endif; ?>
            <button class="menu-toggle" aria-label="メニューを開く" aria-expanded="false">
                <span></span>
            </button>
        </div>
    </div>

    <div class="mobile-menu" id="mobile-menu">
        <?php
        if ( has_nav_menu( 'mobile' ) ) {
            wp_nav_menu( array(
                'theme_location' => 'mobile',
                'container'      => false,
                'items_wrap'     => '%3$s',
                'depth'          => 1,
            ) );
        } elseif ( has_nav_menu( 'primary' ) ) {
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'container'      => false,
                'items_wrap'     => '%3$s',
                'depth'          => 1,
            ) );
        } else {
            $cats = get_categories( array( 'number' => 5, 'hide_empty' => false ) );
            foreach ( $cats as $cat ) {
                echo '<a href="' . esc_url( get_category_link( $cat->term_id ) ) . '">' . esc_html( $cat->name ) . '</a>';
            }
        }
        ?>
        <?php if ( get_theme_mod( 'knt_header_cta_show', true ) ) : ?>
            <a href="<?php echo esc_url( get_theme_mod( 'knt_header_cta_url', 'https://kyou-nani-taberu.app' ) ); ?>"
               class="btn btn--primary"
               style="margin-top: 16px; width: 100%; text-align: center;"
               target="_blank"
               rel="noopener noreferrer">
                <?php echo esc_html( get_theme_mod( 'knt_header_cta_text', 'アプリで探す' ) ); ?>
            </a>
        <?php endif; ?>
    </div>
</header>

<main id="main" role="main">
