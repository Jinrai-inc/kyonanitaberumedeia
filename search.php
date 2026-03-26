<?php
/**
 * Search results template
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
        <p class="archive-header__label">検索結果</p>
        <h1 class="archive-header__title">&ldquo;<?php echo esc_html( get_search_query() ); ?>&rdquo;</h1>
        <?php if ( have_posts() ) : ?>
            <p class="archive-header__desc"><?php printf( '%s件の記事が見つかりました', esc_html( $wp_query->found_posts ) ); ?></p>
        <?php endif; ?>
    </header>

    <div style="max-width: 480px; margin: 0 auto 40px;">
        <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="search-form">
            <input type="search"
                   class="search-form__input"
                   placeholder="キーワードで検索..."
                   value="<?php echo esc_attr( get_search_query() ); ?>"
                   name="s"
                   aria-label="検索">
            <button type="submit" class="btn btn--primary search-form__submit">検索</button>
        </form>
    </div>

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
        <div class="text-center" style="padding: 60px 0;">
            <p style="font-size: 48px; margin-bottom: 16px;">&#128531;</p>
            <p style="color: var(--color-text-main); font-size: 18px; font-weight: 700; margin-bottom: 8px;">該当の記事がありません</p>
            <p style="color: var(--color-text-light); font-size: 14px; margin-bottom: 24px;">別のキーワードで再度お試しください。</p>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--primary">トップページへ戻る</a>
        </div>
    <?php endif; ?>

</div>

<?php get_footer(); ?>
