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
// Hero Section
// ========================================
if ( get_theme_mod( 'knt_hero_show', true ) ) :
    $hero_post_id = get_theme_mod( 'knt_hero_post_id', 0 );
    if ( $hero_post_id ) {
        $hero_query = new WP_Query( array(
            'p'         => $hero_post_id,
            'post_type' => 'post',
        ) );
    } else {
        $hero_query = new WP_Query( array(
            'posts_per_page' => 1,
            'post_type'      => 'post',
            'post_status'    => 'publish',
        ) );
    }

    if ( $hero_query->have_posts() ) :
        $hero_query->the_post();
?>
<section class="hero fadeup">
    <?php if ( has_post_thumbnail() ) : ?>
        <?php the_post_thumbnail( 'knt-hero', array( 'class' => 'hero__image', 'alt' => get_the_title() ) ); ?>
    <?php else : ?>
        <div class="hero__image" style="background: linear-gradient(135deg, var(--color-accent) 0%, #D4795E 50%, var(--color-bg-secondary) 100%); position: absolute; inset: 0;"></div>
    <?php endif; ?>
    <div class="hero__overlay"></div>
    <div class="hero__content">
        <div class="hero__cat">
            <?php
            $cats = get_the_category();
            if ( $cats ) :
            ?>
                <a href="<?php echo esc_url( get_category_link( $cats[0]->term_id ) ); ?>" class="cat-tag" style="background: rgba(255,255,255,0.2); color: #fff;">
                    <?php echo esc_html( $cats[0]->name ); ?>
                </a>
            <?php endif; ?>
        </div>
        <h2 class="hero__title"><?php the_title(); ?></h2>
        <div class="hero__meta">
            <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></time>
        </div>
    </div>
    <a href="<?php the_permalink(); ?>" class="hero__link" aria-label="<?php the_title_attribute(); ?>"></a>
</section>
<?php
        wp_reset_postdata();
    endif;
endif;
?>

<div class="container">

    <?php
    // ========================================
    // Latest Posts Section
    // ========================================
    $latest_count = get_theme_mod( 'knt_latest_count', 6 );
    $latest_title = get_theme_mod( 'knt_latest_title', '新着記事' );

    $latest_query = new WP_Query( array(
        'posts_per_page' => $latest_count,
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'offset'         => 1, // Skip hero post
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
    // Japan Map - Area Search
    // ========================================
    if ( get_theme_mod( 'knt_japan_map_show', true ) ) :
        get_template_part( 'template-parts/japan-map' );
    endif;
    ?>

    <?php
    // ========================================
    // Category Sections
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
    // Popular Posts Section
    // ========================================
    if ( get_theme_mod( 'knt_popular_show', true ) ) :
        $popular_title = get_theme_mod( 'knt_popular_title', '人気記事ランキング' );
        $popular_count = get_theme_mod( 'knt_popular_count', 5 );
        $popular_query = knt_get_popular_posts( $popular_count );

        if ( $popular_query->have_posts() ) :
    ?>
    <section class="section">
        <div class="section__header">
            <h2 class="section__title"><?php echo esc_html( $popular_title ); ?></h2>
        </div>
        <div style="background: var(--color-card-bg); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); border: 1.5px solid var(--color-border); border-radius: var(--radius-lg); padding: 8px 24px;">
            <?php
            $rank = 0;
            while ( $popular_query->have_posts() ) : $popular_query->the_post();
                $rank++;
            ?>
            <div class="ranking-item fadeup">
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
                        <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
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
    // App Banner
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
