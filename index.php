<?php
/**
 * Main index template (fallback)
 *
 * @package KNT_Media
 */

get_header();
?>

<div class="container" style="padding-top: 32px;">

    <?php if ( is_home() && ! is_front_page() ) : ?>
        <header class="archive-header">
            <h1 class="archive-header__title">記事一覧</h1>
        </header>
    <?php endif; ?>

    <?php if ( have_posts() ) : ?>
        <div class="grid grid--3">
            <?php while ( have_posts() ) : the_post(); ?>
                <article class="card fadeup">
                    <?php get_template_part( 'template-parts/card' ); ?>
                </article>
            <?php endwhile; ?>
        </div>

        <div class="pagination">
            <?php
            the_posts_pagination( array(
                'mid_size'  => 2,
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;',
            ) );
            ?>
        </div>
    <?php else : ?>
        <div class="text-center" style="padding: 80px 0;">
            <p style="color: var(--color-text-light); font-size: 18px;">記事が見つかりませんでした。</p>
        </div>
    <?php endif; ?>

</div>

<?php get_footer(); ?>
