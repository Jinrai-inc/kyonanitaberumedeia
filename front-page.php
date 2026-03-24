<?php
/**
 * Front page template
 *
 * @package KNT_Media
 */

get_header();
?>

<?php
// ========================================
// 1. ファーストビジュアル：日本地図エリア検索
// ========================================
if ( get_theme_mod( 'knt_japan_map_show', true ) ) :
    get_template_part( 'template-parts/japan-map' );
endif;
?>

<div class="container">

    <?php
    // ========================================
    // 2. 人気記事
    // ========================================
    if ( get_theme_mod( 'knt_popular_show', true ) ) :
        $popular_title = get_theme_mod( 'knt_popular_title', '人気記事' );
        $popular_count = get_theme_mod( 'knt_popular_count', 5 );
        $popular_query = knt_get_popular_posts( $popular_count );

        if ( $popular_query->have_posts() ) :
    ?>
    <section class="section">
        <div class="section__header">
            <h2 class="section__title"><?php echo esc_html( $popular_title ); ?></h2>
            <a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/?post_type=post' ) ); ?>" class="section__more">一覧 →</a>
        </div>

        <div class="hscroll">
            <?php while ( $popular_query->have_posts() ) : $popular_query->the_post(); ?>
                <article class="card fadeup">
                    <?php get_template_part( 'template-parts/card' ); ?>
                </article>
            <?php endwhile; ?>
        </div>

        <div class="ranking-compact fadeup" style="margin-top: 20px;">
            <?php
            $popular_query->rewind_posts();
            $rank = 0;
            while ( $popular_query->have_posts() ) : $popular_query->the_post();
                $rank++;
            ?>
            <div class="ranking-item">
                <span class="ranking-item__number ranking-item__number--<?php echo $rank; ?>"><?php echo $rank; ?></span>
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="ranking-item__thumb">
                        <?php the_post_thumbnail( 'knt-ranking' ); ?>
                    </div>
                <?php endif; ?>
                <div class="ranking-item__content">
                    <h3 class="ranking-item__title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    <div class="ranking-item__meta">
                        <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></time>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </section>
    <?php
            wp_reset_postdata();
        endif;
    endif;
    ?>

    <?php
    // ========================================
    // 3. いま人気な店舗（PR記事）
    // ========================================
    $pr_slug = get_theme_mod( 'knt_pr_category_slug', 'pr' );
    $pr_category = get_category_by_slug( $pr_slug );
    $pr_tag = get_theme_mod( 'knt_pr_use_tag', false );
    $pr_title = get_theme_mod( 'knt_pr_title', 'いま人気な店舗' );
    $pr_count = get_theme_mod( 'knt_pr_count', 6 );

    $pr_args = array(
        'posts_per_page' => $pr_count,
        'post_type'      => 'post',
        'post_status'    => 'publish',
    );

    if ( $pr_tag ) {
        $pr_args['tag'] = $pr_slug;
    } elseif ( $pr_category ) {
        $pr_args['cat'] = $pr_category->term_id;
    } else {
        $pr_args['meta_key'] = 'knt_is_pr';
        $pr_args['meta_value'] = '1';
    }

    $pr_query = new WP_Query( $pr_args );

    if ( $pr_query->have_posts() ) :
    ?>
    <section class="section">
        <div class="section__header">
            <h2 class="section__title"><?php echo esc_html( $pr_title ); ?></h2>
            <?php if ( $pr_category && ! $pr_tag ) : ?>
                <a href="<?php echo esc_url( get_category_link( $pr_category->term_id ) ); ?>" class="section__more">一覧 →</a>
            <?php elseif ( $pr_tag ) : ?>
                <?php $tag_obj = get_term_by( 'slug', $pr_slug, 'post_tag' ); ?>
                <?php if ( $tag_obj ) : ?>
                    <a href="<?php echo esc_url( get_tag_link( $tag_obj ) ); ?>" class="section__more">一覧 →</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div class="grid grid--3">
            <?php while ( $pr_query->have_posts() ) : $pr_query->the_post(); ?>
                <article class="card fadeup">
                    <?php get_template_part( 'template-parts/card', 'pr' ); ?>
                </article>
            <?php endwhile; ?>
        </div>
    </section>
    <?php
        wp_reset_postdata();
    endif;
    ?>

    <?php
    // ========================================
    // 4. 新着記事
    // ========================================
    $latest_count = get_theme_mod( 'knt_latest_count', 6 );
    $latest_title = get_theme_mod( 'knt_latest_title', '新着記事' );

    $latest_query = new WP_Query( array(
        'posts_per_page' => $latest_count,
        'post_type'      => 'post',
        'post_status'    => 'publish',
    ) );

    if ( $latest_query->have_posts() ) :
    ?>
    <section class="section">
        <div class="section__header">
            <h2 class="section__title"><?php echo esc_html( $latest_title ); ?></h2>
            <a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/?post_type=post' ) ); ?>" class="section__more">すべて見る →</a>
        </div>
        <div class="hscroll">
            <?php while ( $latest_query->have_posts() ) : $latest_query->the_post(); ?>
                <article class="card fadeup">
                    <?php get_template_part( 'template-parts/card' ); ?>
                </article>
            <?php endwhile; ?>
        </div>
    </section>
    <?php
        wp_reset_postdata();
    endif;
    ?>

    <?php
    // ========================================
    // 5. カテゴリ別セクション
    // ========================================
    if ( get_theme_mod( 'knt_category_sections_show', true ) ) :
        $slugs_str = get_theme_mod( 'knt_category_sections_slugs', 'area,genre,trend' );
        $slugs = array_map( 'trim', explode( ',', $slugs_str ) );
        $cat_count = get_theme_mod( 'knt_category_sections_count', 3 );

        foreach ( $slugs as $slug ) :
            $category = get_category_by_slug( $slug );
            if ( ! $category ) continue;

            $cat_query = new WP_Query( array(
                'posts_per_page' => $cat_count,
                'cat'            => $category->term_id,
                'post_type'      => 'post',
                'post_status'    => 'publish',
            ) );

            if ( $cat_query->have_posts() ) :
    ?>
    <section class="section">
        <div class="section__header">
            <h2 class="section__title"><?php echo esc_html( $category->name ); ?></h2>
            <a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" class="section__more">もっと見る →</a>
        </div>
        <div class="grid grid--3">
            <?php while ( $cat_query->have_posts() ) : $cat_query->the_post(); ?>
                <article class="card fadeup">
                    <?php get_template_part( 'template-parts/card' ); ?>
                </article>
            <?php endwhile; ?>
        </div>
    </section>
    <?php
            endif;
            wp_reset_postdata();
        endforeach;
    endif;
    ?>

    <?php
    // ========================================
    // 6. アプリ訴求バナー
    // ========================================
    if ( get_theme_mod( 'knt_app_banner_show', true ) ) :
    ?>
    <section class="section">
        <div class="app-banner fadeup">
            <h2 class="app-banner__title">
                <?php echo esc_html( get_theme_mod( 'knt_app_banner_title', '近くのお店をサクッと検索' ) ); ?>
            </h2>
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
    </section>
    <?php endif; ?>

</div><!-- .container -->

<?php get_footer(); ?>
