<?php
/**
 * Archive template (categories, tags, date archives)
 *
 * @package KNT_Media
 */

get_header();
?>

<div class="container" style="padding-top: 16px;">
    <?php knt_breadcrumb(); ?>
</div>

<div class="container">

    <header class="archive-header">
        <?php if ( is_category() ) : ?>
            <p class="archive-header__label">カテゴリ</p>
            <h1 class="archive-header__title"><?php single_cat_title(); ?></h1>
            <?php if ( category_description() ) : ?>
                <p class="archive-header__desc"><?php echo wp_strip_all_tags( category_description() ); ?></p>
            <?php endif; ?>
        <?php elseif ( is_tag() ) : ?>
            <p class="archive-header__label">タグ</p>
            <h1 class="archive-header__title">#<?php single_tag_title(); ?></h1>
        <?php elseif ( is_date() ) : ?>
            <p class="archive-header__label">アーカイブ</p>
            <h1 class="archive-header__title"><?php echo get_the_date( 'Y年n月' ); ?></h1>
        <?php elseif ( is_author() ) : ?>
            <p class="archive-header__label">著者</p>
            <h1 class="archive-header__title"><?php the_author(); ?></h1>
        <?php else : ?>
            <h1 class="archive-header__title"><?php the_archive_title(); ?></h1>
        <?php endif; ?>
    </header>

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
