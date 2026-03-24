<?php
/**
 * Page template
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
        <h1 class="article-header__title"><?php the_title(); ?></h1>
    </header>

    <div class="article-content">
        <?php the_content(); ?>
    </div>

    <?php
    wp_link_pages( array(
        'before' => '<div class="page-links">',
        'after'  => '</div>',
    ) );
    ?>
</article>

<?php endwhile; ?>

<?php get_footer(); ?>
