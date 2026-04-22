<?php
$settings = get_option( 'knt_settings', array() );
?>
<h2>ヘッダー設定</h2>
<?php knt_toggle( 'header_cta_show', 'CTAボタンを表示', $settings, true ); ?>
<table class="form-table">
    <?php knt_text_field( 'header_cta_text', 'CTAボタンテキスト', $settings, 'アプリで探す' ); ?>
    <?php knt_text_field( 'header_cta_url', 'CTAボタンURL', $settings, 'https://kyou-nani-taberu.app' ); ?>
</table>
