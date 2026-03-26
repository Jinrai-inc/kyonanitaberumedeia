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

    <!-- Search Bar -->
    <div class="archive-search">
        <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="archive-search__form">
            <input type="search" class="archive-search__input" name="s" placeholder="キーワードで記事を検索..." value="<?php echo esc_attr( get_search_query() ); ?>" />
        </form>
    </div>

    <!-- Category Filter Tabs -->
    <div class="cat-filter">
        <?php
        $current_cat_id = is_category() ? get_queried_object_id() : 0;
        $all_active = ! is_category() ? ' cat-filter__tab--active' : '';
        $blog_url = get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/?post_type=post' );
        ?>
        <a href="<?php echo esc_url( $blog_url ); ?>" class="cat-filter__tab<?php echo $all_active; ?>">すべて</a>
        <?php
        $categories = get_categories( array(
            'orderby'    => 'count',
            'order'      => 'DESC',
            'hide_empty' => true,
        ) );
        foreach ( $categories as $cat ) :
            $is_active = ( $current_cat_id === $cat->term_id ) ? ' cat-filter__tab--active' : '';
        ?>
            <a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="cat-filter__tab<?php echo $is_active; ?>">
                <?php echo esc_html( $cat->name ); ?><span class="cat-filter__count"><?php echo esc_html( $cat->count ); ?></span>
            </a>
        <?php endforeach; ?>
    </div>

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

    <?php // Genre/Tag Filter Chips ?>
    <?php
    $popular_tags = get_tags( array( 'orderby' => 'count', 'order' => 'DESC', 'number' => 12 ) );
    if ( $popular_tags ) :
    ?>
    <div class="archive-filters">
        <?php foreach ( $popular_tags as $ptag ) : ?>
            <a href="<?php echo esc_url( get_tag_link( $ptag->term_id ) ); ?>" class="archive-filters__chip<?php echo is_tag( $ptag->slug ) ? ' is-active' : ''; ?>">
                #<?php echo esc_html( $ptag->name ); ?>
            </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ( is_category() ) : ?>
        <meta name="knt-category-id" content="<?php echo esc_attr( get_queried_object_id() ); ?>">
    <?php endif; ?>

    <?php if ( have_posts() ) : ?>
        <div class="grid grid--3">
            <?php while ( have_posts() ) : the_post(); ?>
                <article class="card fadeup">
                    <?php get_template_part( 'template-parts/card' ); ?>
                </article>
            <?php endwhile; ?>
        </div>

        <?php
        global $wp_query;
        if ( $wp_query->max_num_pages > 1 ) :
        ?>
        <div class="load-more-wrap">
            <button class="load-more-btn" id="load-more-btn">もっと見る</button>
        </div>
        <?php endif; ?>

        <noscript>
        <div class="pagination">
            <?php
            the_posts_pagination( array(
                'mid_size'  => 2,
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;',
            ) );
            ?>
        </div>
        </noscript>
    <?php else : ?>
        <div class="text-center" style="padding: 80px 0;">
            <p style="font-size: 48px; margin-bottom: 16px;">&#128531;</p>
            <p style="color: var(--color-text-main); font-size: 18px; font-weight: 700; margin-bottom: 8px;">該当の記事がありません</p>
            <p style="color: var(--color-text-light); font-size: 14px; margin-bottom: 24px;">このエリアの記事はまだ公開されていません。<br>他のエリアを探してみてください。</p>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--primary">トップページへ戻る</a>
        </div>
    <?php endif; ?>

</div>

<?php get_footer(); ?>
