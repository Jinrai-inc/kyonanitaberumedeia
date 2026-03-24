<?php
/**
 * Custom search form
 *
 * @package KNT_Media
 */
?>
<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="search-form">
    <input type="search"
           class="search-form__input"
           placeholder="キーワードで検索..."
           value="<?php echo esc_attr( get_search_query() ); ?>"
           name="s"
           aria-label="検索">
    <button type="submit" class="btn btn--primary search-form__submit">検索</button>
</form>
