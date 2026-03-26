<?php
/**
 * 記事ページ専用サイドバー
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$post_id = get_the_ID();
$categories = wp_get_post_categories( $post_id, array( 'fields' => 'all' ) );

$city_cat = null;
$genre_names_list = array( 'ラーメン','焼肉','和食','中華','イタリアン・フレンチ','カフェ・スイーツ','カレー','居酒屋','韓国料理','ハンバーガー','ステーキ','エスニック','バー','洋食' );

foreach ( $categories as $cat ) {
    if ( $cat->parent > 0 && ! in_array( $cat->name, $genre_names_list, true ) ) {
        $parent = get_category( $cat->parent );
        if ( $parent && ! in_array( $parent->name, $genre_names_list, true ) ) {
            $city_cat = $cat;
            break;
        }
    }
}
?>
<div class="sidebar-sticky">

    <div class="sidebar-widget sidebar-ad">
        <?php $ad1 = get_theme_mod( 'knt_sidebar_ad_1', '' ); ?>
        <?php if ( $ad1 ) : ?>
            <?php echo $ad1; ?>
        <?php else : ?>
            <a href="https://www.kyou-nani-taberu.app/" target="_blank" rel="noopener noreferrer" class="sidebar-app-banner">
                <div class="sidebar-app-banner__inner">
                    <strong>今日何食べる？</strong>
                    <span>現在地から近いお店を探す</span>
                </div>
            </a>
        <?php endif; ?>
    </div>

    <div class="sidebar-widget">
        <h3 class="sidebar-widget__title">人気記事</h3>
        <?php
        $popular = knt_get_popular_posts( 5 );
        if ( $popular->have_posts() ) :
            $rank = 1;
        ?>
        <ul class="sidebar-popular">
            <?php while ( $popular->have_posts() ) : $popular->the_post(); ?>
            <li class="sidebar-popular__item">
                <span class="sidebar-popular__rank"><?php echo $rank++; ?></span>
                <a href="<?php the_permalink(); ?>" class="sidebar-popular__link">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="sidebar-popular__thumb"><?php the_post_thumbnail( 'thumbnail' ); ?></div>
                    <?php endif; ?>
                    <span class="sidebar-popular__title"><?php the_title(); ?></span>
                </a>
            </li>
            <?php endwhile; ?>
        </ul>
        <?php wp_reset_postdata(); endif; ?>
    </div>

    <?php $ad2 = get_theme_mod( 'knt_sidebar_ad_2', '' ); ?>
    <?php if ( $ad2 ) : ?>
    <div class="sidebar-widget sidebar-ad"><?php echo $ad2; ?></div>
    <?php endif; ?>

    <?php if ( $city_cat ) : ?>
    <div class="sidebar-widget">
        <h3 class="sidebar-widget__title"><?php echo esc_html( $city_cat->name ); ?>の記事</h3>
        <?php
        $area_q = new WP_Query( array(
            'posts_per_page' => 5, 'category__in' => array( $city_cat->term_id ),
            'post__not_in' => array( $post_id ), 'post_status' => 'publish',
        ) );
        if ( $area_q->have_posts() ) :
        ?>
        <ul class="sidebar-area-list">
            <?php while ( $area_q->have_posts() ) : $area_q->the_post(); ?>
            <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
            <?php endwhile; ?>
        </ul>
        <a href="<?php echo esc_url( get_category_link( $city_cat->term_id ) ); ?>" class="sidebar-more-link"><?php echo esc_html( $city_cat->name ); ?>の記事一覧 →</a>
        <?php wp_reset_postdata(); endif; ?>
    </div>
    <?php endif; ?>

    <div class="sidebar-widget">
        <h3 class="sidebar-widget__title">ジャンルから探す</h3>
        <ul class="sidebar-genre-list">
            <?php
            foreach ( get_categories( array( 'parent' => 0, 'hide_empty' => true, 'exclude' => array(1), 'orderby' => 'count', 'order' => 'DESC', 'number' => 10 ) ) as $gc ) :
                if ( in_array( $gc->name, $genre_names_list, true ) ) :
            ?>
            <li><a href="<?php echo esc_url( get_category_link( $gc->term_id ) ); ?>"><?php echo esc_html( $gc->name ); ?> <span class="sidebar-genre-count">(<?php echo $gc->count; ?>)</span></a></li>
            <?php endif; endforeach; ?>
        </ul>
    </div>

    <div class="sidebar-widget">
        <h3 class="sidebar-widget__title">記事を検索</h3>
        <?php get_search_form(); ?>
    </div>

    <?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
        <?php dynamic_sidebar( 'sidebar-1' ); ?>
    <?php endif; ?>
</div>
