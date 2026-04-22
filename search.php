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

    <div style="max-width: 560px; margin: 0 auto 32px;">
        <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="search-form" style="margin-bottom: 16px;">
            <input type="search"
                   class="search-form__input"
                   placeholder="キーワードで検索..."
                   value="<?php echo esc_attr( get_search_query() ); ?>"
                   name="s"
                   aria-label="検索">
            <button type="submit" class="btn btn--primary search-form__submit">検索</button>
        </form>

        <?php // エリアフィルター ?>
        <div class="search-area-filter">
            <?php
            $current_area = isset( $_GET['area_cat'] ) ? intval( $_GET['area_cat'] ) : 0;
            $search_query = get_search_query();
            $base_url = home_url( '/?s=' . urlencode( $search_query ) );

            // 全エリアボタン
            $all_class = $current_area === 0 ? ' is-active' : '';
            echo '<a href="' . esc_url( $base_url ) . '" class="search-area-filter__btn' . $all_class . '">全エリア</a>';

            // 都道府県カテゴリ（記事があるもののみ）
            $area_cats = get_categories( array(
                'parent'     => 0,
                'orderby'    => 'count',
                'order'      => 'DESC',
                'hide_empty' => true,
                'number'     => 10,
            ) );
            foreach ( $area_cats as $ac ) :
                if ( ! preg_match( '/^area-\d{2}$/', $ac->slug ) ) continue;
                $active = ( $current_area === $ac->term_id ) ? ' is-active' : '';
                $filter_url = $base_url . '&area_cat=' . $ac->term_id;
            ?>
                <a href="<?php echo esc_url( $filter_url ); ?>" class="search-area-filter__btn<?php echo $active; ?>">
                    <?php echo esc_html( $ac->name ); ?>
                </a>
            <?php endforeach; ?>
        </div>
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
