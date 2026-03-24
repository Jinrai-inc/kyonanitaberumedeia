<?php
/**
 * Card template part
 *
 * @package KNT_Media
 */
?>
<div class="card__image-wrapper">
    <?php if ( has_post_thumbnail() ) : ?>
        <?php the_post_thumbnail( 'knt-card', array( 'class' => 'card__image', 'alt' => get_the_title() ) ); ?>
    <?php else : ?>
        <div class="card__image-placeholder">&#127858;</div>
    <?php endif; ?>
    <div class="card__image-overlay"></div>
    <h3 class="card__image-title"><?php the_title(); ?></h3>
</div>
<div class="card__body">
    <div class="card__cat">
        <?php
        $cats = get_the_category();
        if ( $cats ) :
        ?>
            <a href="<?php echo esc_url( get_category_link( $cats[0]->term_id ) ); ?>" class="cat-tag">
                <?php echo esc_html( $cats[0]->name ); ?>
            </a>
        <?php endif; ?>
    </div>
    <div class="card__meta">
        <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></time>
    </div>
    <h3 class="card__title">
        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
    </h3>
</div>
