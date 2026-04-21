<?php
/**
 * Template Part: Featured (1 lead + 3 side layout)
 *
 * 最新の注目記事4本を「リード1本＋サイド3本」で表示する特集セクション。
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$rd_featured_count = 4;

// 優先: is_sticky の記事（編集部が明示的に目立たせている記事）
$rd_sticky_ids = get_option( 'sticky_posts', array() );

$rd_featured_args = array(
    'posts_per_page'      => $rd_featured_count,
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'ignore_sticky_posts' => 1,
);

if ( ! empty( $rd_sticky_ids ) ) {
    $rd_featured_args['post__in'] = $rd_sticky_ids;
    $rd_featured_args['orderby']  = 'post__in';
} else {
    $rd_featured_args['orderby'] = 'date';
    $rd_featured_args['order']   = 'DESC';
}

$rd_featured_query = new WP_Query( $rd_featured_args );

if ( ! $rd_featured_query->have_posts() ) {
    return;
}

$rd_featured_title = apply_filters( 'knt_featured_title', '注目の特集' );

// 事前に posts を配列で取得
$rd_featured_posts = $rd_featured_query->posts;
$rd_lead_post      = array_shift( $rd_featured_posts );
$rd_side_posts     = $rd_featured_posts;
?>
<section class="section rd-featured-section">
    <div class="section__header">
        <h2 class="section__title"><?php echo esc_html( $rd_featured_title ); ?></h2>
    </div>

    <div class="rd-featured<?php echo empty( $rd_side_posts ) ? ' rd-featured--lead-only' : ''; ?>">
        <?php
        // --- Lead ---
        setup_postdata( $GLOBALS['post'] = $rd_lead_post ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
        $rd_cats  = get_the_category();
        $rd_cat   = $rd_cats ? $rd_cats[0] : null;
        $rd_thumb = has_post_thumbnail()
            ? get_the_post_thumbnail( null, 'knt-hero', array(
                'class'   => 'rd-featured__img',
                'alt'     => get_the_title(),
                'loading' => 'eager',
            ) )
            : '<div class="rd-featured__img rd-featured__img--placeholder">🍱</div>';
        ?>
        <a href="<?php the_permalink(); ?>" class="rd-featured__lead">
            <div class="rd-featured__media"><?php echo $rd_thumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
            <div class="rd-featured__body">
                <span class="rd-featured__badge">PICK UP</span>
                <?php if ( $rd_cat ) : ?>
                    <span class="rd-featured__cat"><?php echo esc_html( $rd_cat->name ); ?></span>
                <?php endif; ?>
                <h3 class="rd-featured__title"><?php the_title(); ?></h3>
                <p class="rd-featured__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 40, '…' ) ); ?></p>
                <time class="rd-featured__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></time>
            </div>
        </a>

        <?php if ( ! empty( $rd_side_posts ) ) : ?>
            <div class="rd-featured__side">
                <?php foreach ( $rd_side_posts as $rd_side ) :
                    setup_postdata( $GLOBALS['post'] = $rd_side ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                    $rd_side_cats  = get_the_category();
                    $rd_side_cat   = $rd_side_cats ? $rd_side_cats[0] : null;
                    $rd_side_thumb = has_post_thumbnail()
                        ? get_the_post_thumbnail( null, 'knt-card', array(
                            'class'   => 'rd-featured__img',
                            'alt'     => get_the_title(),
                            'loading' => 'lazy',
                        ) )
                        : '<div class="rd-featured__img rd-featured__img--placeholder">🍱</div>';
                ?>
                    <a href="<?php the_permalink(); ?>" class="rd-featured__item">
                        <div class="rd-featured__item-media"><?php echo $rd_side_thumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                        <div class="rd-featured__item-body">
                            <?php if ( $rd_side_cat ) : ?>
                                <span class="rd-featured__cat rd-featured__cat--small"><?php echo esc_html( $rd_side_cat->name ); ?></span>
                            <?php endif; ?>
                            <h4 class="rd-featured__item-title"><?php the_title(); ?></h4>
                            <time class="rd-featured__date rd-featured__date--small" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></time>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php wp_reset_postdata(); ?>
