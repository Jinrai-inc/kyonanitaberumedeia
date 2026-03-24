<?php
/**
 * 404 template
 *
 * @package KNT_Media
 */

get_header();
?>

<div class="container--narrow">
    <div class="page-404">
        <div class="page-404__number">404</div>
        <h1 class="page-404__title">ページが見つかりません</h1>
        <p class="page-404__text">お探しのページは移動または削除された可能性があります。</p>

        <div style="max-width: 400px; margin: 0 auto 32px;">
            <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="search-form">
                <input type="search"
                       class="search-form__input"
                       placeholder="キーワードで検索..."
                       name="s"
                       aria-label="検索">
                <button type="submit" class="btn btn--primary search-form__submit">検索</button>
            </form>
        </div>

        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--secondary">トップページへ戻る</a>
    </div>
</div>

<?php get_footer(); ?>
