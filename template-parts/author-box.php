<?php
/**
 * Template Part: Author Box
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$author_id   = get_the_author_meta( 'ID' );
$author_name = get_the_author();
$author_desc = get_the_author_meta( 'description' );
$author_url  = get_author_posts_url( $author_id );
$avatar      = get_avatar_url( $author_id, array( 'size' => 96 ) );
?>
<aside class="author-box">
    <div class="author-box__avatar">
        <img src="<?php echo esc_url( $avatar ); ?>" alt="<?php echo esc_attr( $author_name ); ?>" width="64" height="64" loading="lazy">
    </div>
    <div class="author-box__info">
        <p class="author-box__label">この記事を書いた人</p>
        <a href="<?php echo esc_url( $author_url ); ?>" class="author-box__name"><?php echo esc_html( $author_name ); ?></a>
        <?php if ( $author_desc ) : ?>
            <p class="author-box__desc"><?php echo esc_html( $author_desc ); ?></p>
        <?php endif; ?>
    </div>
</aside>
