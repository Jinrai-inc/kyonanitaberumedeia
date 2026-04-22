<?php
$settings = get_option( 'knt_settings', array() );
?>
<h2>CTA設定</h2>
<h3>アプリバナー</h3>
<table class="form-table">
    <?php knt_text_field( 'app_banner_title', 'タイトル', $settings, '近くのお店をサクッと検索' ); ?>
    <?php knt_text_field( 'app_banner_text', '説明文', $settings, '' ); ?>
    <?php knt_text_field( 'app_banner_btn_text', 'ボタンテキスト', $settings, 'アプリを使ってみる' ); ?>
    <?php knt_text_field( 'app_banner_url', 'ボタンURL', $settings, 'https://kyou-nani-taberu.app' ); ?>
</table>

<h3>記事内CTA</h3>
<?php knt_toggle( 'show_app_cta_single', '記事内にアプリCTAを表示', $settings, true ); ?>
<table class="form-table">
    <?php knt_text_field( 'app_cta_text', 'CTA見出し', $settings, 'このエリアのお店をアプリで探す' ); ?>
</table>
