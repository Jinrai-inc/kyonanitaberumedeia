<?php
/**
 * Single post template
 *
 * @package KNT_Media
 */

get_header();
?>

<div class="container--narrow" style="padding-top: 16px;">
    <?php knt_breadcrumb(); ?>
</div>

<?php while ( have_posts() ) : the_post(); ?>

<article class="container--narrow" style="padding-top: 16px; padding-bottom: 48px;">

    <header class="article-header">
        <?php
        $is_pr = get_post_meta( get_the_ID(), 'knt_is_pr', true );
        if ( $is_pr ) :
        ?>
            <div class="article-header__pr">PR</div>
        <?php endif; ?>
        <div class="article-header__cat">
            <?php
            $cats = get_the_category();
            foreach ( $cats as $cat ) :
            ?>
                <a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="cat-tag">
                    <?php echo esc_html( $cat->name ); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <h1 class="article-header__title"><?php the_title(); ?></h1>

        <div class="article-header__meta">
            <span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                公開: <?php echo esc_html( get_the_date() ); ?>
            </span>
            <?php if ( get_the_modified_date() !== get_the_date() ) : ?>
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                    更新: <?php echo esc_html( get_the_modified_date() ); ?>
                </span>
            <?php endif; ?>
        </div>
    </header>

    <?php if ( has_post_thumbnail() ) : ?>
        <div class="article-featured-image">
            <?php the_post_thumbnail( 'knt-hero', array( 'alt' => get_the_title() ) ); ?>
        </div>
    <?php endif; ?>

    <?php
    // Ad after intro
    $ad_after_intro = get_theme_mod( 'knt_ad_after_intro' );
    if ( $ad_after_intro ) :
    ?>
        <div style="margin-bottom: 24px;">
            <?php echo $ad_after_intro; ?>
        </div>
    <?php endif; ?>

    <div class="article-content">
        <?php the_content(); ?>
    </div>

    <?php
    // Page links for paginated posts
    wp_link_pages( array(
        'before' => '<div class="page-links">',
        'after'  => '</div>',
    ) );
    ?>

    <?php
    // Tags
    $tags = get_the_tags();
    if ( $tags ) :
    ?>
    <div class="article-tags">
        <?php foreach ( $tags as $tag ) : ?>
            <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="article-tags__item">
                #<?php echo esc_html( $tag->name ); ?>
            </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php // SNS Share Buttons ?>
    <?php get_template_part( 'template-parts/share-buttons' ); ?>

    <?php // Author Box ?>
    <?php get_template_part( 'template-parts/author-box' ); ?>

    <?php
    // App CTA in article
    if ( get_theme_mod( 'knt_show_app_cta_single', true ) ) :
    ?>
    <div class="app-banner" style="margin-top: 40px;">
        <h3 class="app-banner__title" style="font-size: 18px;">
            <?php echo esc_html( get_theme_mod( 'knt_app_cta_text', 'このエリアのお店をアプリで探す' ) ); ?>
        </h3>
        <p class="app-banner__text">
            <?php echo esc_html( get_theme_mod( 'knt_app_banner_text', '「今日何食べる？」アプリなら、現在地周辺のお店をすぐに検索。気分やジャンルで絞り込んで、あなたにぴったりの一軒が見つかります。' ) ); ?>
        </p>
        <a href="<?php echo esc_url( get_theme_mod( 'knt_app_banner_url', 'https://kyou-nani-taberu.app' ) ); ?>"
           class="btn btn--primary"
           target="_blank"
           rel="noopener noreferrer">
            <?php echo esc_html( get_theme_mod( 'knt_app_banner_btn_text', 'アプリを使ってみる' ) ); ?> →
        </a>
    </div>
    <?php endif; ?>

    <?php
    // Ad before related
    $ad_before_related = get_theme_mod( 'knt_ad_before_related' );
    if ( $ad_before_related ) :
    ?>
        <div style="margin-top: 40px;">
            <?php echo $ad_before_related; ?>
        </div>
    <?php endif; ?>

    <?php
    // ========================================
    // Related Posts
    // ========================================
    if ( get_theme_mod( 'knt_show_related', true ) ) :
        $related_count = get_theme_mod( 'knt_related_count', 3 );
        $current_cats = wp_get_post_categories( get_the_ID() );

        $related_query = new WP_Query( array(
            'posts_per_page' => $related_count,
            'category__in'   => $current_cats,
            'post__not_in'   => array( get_the_ID() ),
            'post_type'      => 'post',
            'post_status'    => 'publish',
        ) );

        if ( $related_query->have_posts() ) :
    ?>
    <div class="related-posts">
        <h2 class="section__title" style="margin-bottom: 24px;">関連記事</h2>
        <div class="hscroll">
            <?php while ( $related_query->have_posts() ) : $related_query->the_post(); ?>
                <article class="card fadeup">
                    <?php get_template_part( 'template-parts/card' ); ?>
                </article>
            <?php endwhile; ?>
        </div>
    </div>
    <?php
            wp_reset_postdata();
        endif;
    endif;
    ?>

</article>

<?php endwhile; ?>

<?php get_footer(); ?>
