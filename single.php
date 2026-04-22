<?php
/**
 * Single post template - 2カラムレイアウト
 *
 * @package KNT_Media
 */

get_header();
?>

<div class="container" style="padding-top: 16px;">
    <?php knt_breadcrumb(); ?>
</div>

<?php while ( have_posts() ) : the_post(); ?>

<div class="single-layout container">

    <main class="single-layout__main">
        <article>
            <header class="article-header">
                <?php
                $is_pr = get_post_meta( get_the_ID(), 'knt_is_pr', true );
                if ( $is_pr ) :
                ?>
                    <div class="article-header__pr">PR</div>
                <?php endif; ?>
                <div class="article-header__cat">
                    <?php foreach ( get_the_category() as $cat ) : ?>
                        <a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="cat-tag"><?php echo esc_html( $cat->name ); ?></a>
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

            <?php $ad_intro = get_theme_mod( 'knt_ad_after_intro' ); ?>
            <?php if ( $ad_intro ) : ?>
                <div style="margin-bottom: 24px;"><?php echo $ad_intro; ?></div>
            <?php endif; ?>

            <div class="article-content">
                <?php the_content(); ?>
            </div>

            <?php wp_link_pages( array( 'before' => '<div class="page-links">', 'after' => '</div>' ) ); ?>

            <?php $tags = get_the_tags(); if ( $tags ) : ?>
            <div class="article-tags">
                <?php foreach ( $tags as $tag ) : ?>
                    <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="article-tags__item">#<?php echo esc_html( $tag->name ); ?></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php get_template_part( 'template-parts/share-buttons' ); ?>
            <?php get_template_part( 'template-parts/author-box' ); ?>

            <?php $ad_related = get_theme_mod( 'knt_ad_before_related' ); ?>
            <?php if ( $ad_related ) : ?>
                <div style="margin-top: 40px;"><?php echo $ad_related; ?></div>
            <?php endif; ?>

            <?php
            // エリア別関連記事
            knt_render_area_related_posts();
            ?>

        </article>
    </main>

    <aside class="single-layout__sidebar">
        <?php get_sidebar( 'single' ); ?>
    </aside>

</div>

<?php endwhile; ?>

<?php get_footer(); ?>
