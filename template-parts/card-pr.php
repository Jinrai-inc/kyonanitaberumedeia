<?php
/**
 * Card template part - PR (Sponsored) variant
 * PR記事用カード（PRバッジ付き）
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
    <span class="card__pr-badge">PR</span>
</div>
<div class="card__body">
    <div class="card__cat">
        <?php
        $cats = get_the_category();
        if ( $cats ) :
            // PRカテゴリ以外の最初のカテゴリを表示
            $display_cat = null;
            foreach ( $cats as $cat ) {
                if ( $cat->slug !== 'pr' ) {
                    $display_cat = $cat;
                    break;
                }
            }
            if ( ! $display_cat && $cats ) {
                $display_cat = $cats[0];
            }
            if ( $display_cat ) :
        ?>
            <a href="<?php echo esc_url( get_category_link( $display_cat->term_id ) ); ?>" class="cat-tag">
                <?php echo esc_html( $display_cat->name ); ?>
            </a>
        <?php
            endif;
        endif;
        ?>
    </div>
    <div class="card__meta">
        <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></time>
    </div>
    <h3 class="card__title">
        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
    </h3>
</div>
