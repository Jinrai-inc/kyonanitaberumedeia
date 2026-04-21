<?php
/**
 * Card template part
 *
 * @package KNT_Media
 */

$post_id = get_the_ID();
?>
<div class="card__image-wrapper">
    <a class="card__image-link" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
        <?php if ( has_post_thumbnail() ) : ?>
            <?php the_post_thumbnail( 'knt-card', array( 'class' => 'card__image', 'alt' => get_the_title() ) ); ?>
        <?php else : ?>
            <div class="card__image-placeholder">&#127858;</div>
        <?php endif; ?>
        <div class="card__image-overlay"></div>
        <h3 class="card__image-title"><?php the_title(); ?></h3>
    </a>
    <button class="card__bookmark" data-post-id="<?php echo esc_attr( $post_id ); ?>" aria-label="ブックマーク">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
    </button>
</div>
<div class="card__body">
    <?php
    $cats = get_the_category();
    if ( $cats ) :
    ?>
        <div class="card__cat">
            <a href="<?php echo esc_url( get_category_link( $cats[0]->term_id ) ); ?>" class="cat-tag">
                <?php echo esc_html( $cats[0]->name ); ?>
            </a>
        </div>
    <?php endif; ?>
    <div class="card__meta">
        <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></time>
    </div>
    <h3 class="card__title">
        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
    </h3>
</div>
