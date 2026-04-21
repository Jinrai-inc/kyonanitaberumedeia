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
// 0. コンテンツスライダー
// ========================================
get_template_part( 'template-parts/content-slider' );
?>

<?php
// ========================================
// 1. ファーストビジュアル：日本地図エリア検索
// ========================================
if ( get_theme_mod( 'knt_japan_map_show', true ) ) :
    get_template_part( 'template-parts/japan-map' );
endif;
?>

<div class="container front-page-container">

    <?php
    // ========================================
    // 1.5 ジャンルクイックフィルタ（redesign）
    // ========================================
    if ( get_theme_mod( 'knt_genre_chips_show', true ) ) :
        get_template_part( 'template-parts/genre-chips' );
    endif;
    ?>

    <?php
    // ========================================
    // 1.8 実績カウンター
    // ========================================
    $knt_s = get_option( 'knt_settings', array() );
    if ( ! isset( $knt_s['fp_stats_counter_show'] ) || $knt_s['fp_stats_counter_show'] !== '0' ) :
        get_template_part( 'template-parts/stats-counter' );
    endif;
    ?>

    <?php
    // ========================================
    // 1.9 人気シーンチップ（redesign）
    // ========================================
    if ( get_theme_mod( 'knt_popular_searches_show', true ) ) :
        get_template_part( 'template-parts/popular-searches' );
    endif;
    ?>

    <?php
    // ========================================
    // 1.95 注目の特集（redesign: 1 lead + 3 side）
    // ========================================
    if ( get_theme_mod( 'knt_featured_show', true ) ) :
        get_template_part( 'template-parts/featured' );
    endif;
    ?>

    <?php
    // ========================================
    // 2. 人気記事
    // ========================================
    if ( get_theme_mod( 'knt_popular_show', true ) ) :
        $popular_title = get_theme_mod( 'knt_popular_title', '人気記事' );
        // 人気記事: 最新6件（PV順がなければ最新順）
        $popular_query = new WP_Query( array(
            'posts_per_page' => 6,
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );

        if ( $popular_query->have_posts() ) :
    ?>
    <section class="section">
        <div class="section__header">
            <h2 class="section__title"><?php echo esc_html( $popular_title ); ?></h2>
            <a href="<?php echo esc_url( home_url( '/?s=おすすめ&orderby=date' ) ); ?>" class="section__more">一覧 →</a>
        </div>

        <div class="grid grid--3 is-ranked">
            <?php while ( $popular_query->have_posts() ) : $popular_query->the_post(); ?>
                <article class="card fadeup">
                    <?php get_template_part( 'template-parts/card' ); ?>
                </article>
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
    // 2.5 食べたいもの診断
    // ========================================
    $knt_s = get_option( 'knt_settings', array() );
    if ( ! isset( $knt_s['fp_food_quiz_show'] ) || $knt_s['fp_food_quiz_show'] !== '0' ) :
        get_template_part( 'template-parts/food-quiz' );
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
    $latest_title = get_theme_mod( 'knt_latest_title', '新着記事' );

    $latest_query = new WP_Query( array(
        'posts_per_page' => 9,
        'post_type'      => 'post',
        'post_status'    => 'publish',
    ) );

    if ( $latest_query->have_posts() ) :
    ?>
    <section class="section">
        <div class="section__header">
            <h2 class="section__title"><?php echo esc_html( $latest_title ); ?></h2>
            <a href="<?php echo esc_url( home_url( '/?s=' ) ); ?>" class="section__more">すべて見る →</a>
        </div>
        <div class="grid grid--3">
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
    // 4.5 都道府県ピッカー（redesign: 47都道府県 + 8地方タブ）
    // ========================================
    if ( get_theme_mod( 'knt_pref_picker_show', true ) ) :
        get_template_part( 'template-parts/prefecture-picker' );
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
    // 6. アプリ紹介セクション
    // ========================================
    if ( get_theme_mod( 'knt_app_banner_show', true ) ) :
        $app_screenshot = get_theme_mod( 'knt_app_screenshot', '' );
        $app_url = get_theme_mod( 'knt_app_banner_url', 'https://kyou-nani-taberu.app' );
    ?>
    <section class="section">
        <div class="app-showcase fadeup">
            <?php
            $app_logo = get_theme_mod( 'knt_app_logo', '' );
            if ( $app_logo ) :
            ?>
                <img src="<?php echo esc_url( $app_logo ); ?>" alt="アプリロゴ" class="app-showcase__logo">
            <?php elseif ( has_custom_logo() ) : ?>
                <div class="app-showcase__logo"><?php the_custom_logo(); ?></div>
            <?php endif; ?>
            <div class="app-showcase__phone">
                <?php if ( $app_screenshot ) : ?>
                    <img src="<?php echo esc_url( $app_screenshot ); ?>" alt="今日何食べる？アプリ画面" class="app-showcase__screenshot" loading="lazy">
                <?php else : ?>
                    <div class="app-showcase__screenshot-placeholder">
                        <span>アプリ画面</span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="app-showcase__content">
                <h2 class="app-showcase__title">「今日何食べる？」で<br>お店選びをもっと楽しく</h2>
                <ul class="app-showcase__features">
                    <li>
                        <strong>現在地からすぐ検索</strong>
                        <span>GPSで今いる場所の周辺にある飲食店を距離順・評価順で瞬時に表示。徒歩圏内のお店がすぐ見つかります。</span>
                    </li>
                    <li>
                        <strong>ルーレットで迷わず決まる</strong>
                        <span>「何食べよう…」と迷ったらルーレット機能におまかせ。ジャンルや気分をシャッフルして、運命の一軒に出会えます。</span>
                    </li>
                    <li>
                        <strong>ジャンル・予算・シーンで絞り込み</strong>
                        <span>ラーメン・焼肉・カフェなどのジャンルはもちろん、デート・飲み会などのシーンでもお店を検索できます。</span>
                    </li>
                </ul>
                <a href="<?php echo esc_url( $app_url ); ?>"
                   class="btn btn--primary btn--large"
                   target="_blank" rel="noopener noreferrer">
                    無料でアプリを使ってみる →
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

</div><!-- .container -->

<?php get_footer(); ?>
