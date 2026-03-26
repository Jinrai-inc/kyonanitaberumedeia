<?php
$settings = get_option( 'knt_settings', array() );
?>
<h2>追尾バナー設定</h2>
<?php knt_toggle( 'sticky_banner_show', '追尾バナーを表示', $settings, false ); ?>
<table class="form-table">
    <?php knt_text_field( 'sticky_banner_text', 'バナーテキスト', $settings, '', '今すぐアプリをダウンロード' ); ?>
    <?php knt_text_field( 'sticky_banner_url', 'バナーリンクURL', $settings, '' ); ?>
    <?php knt_text_field( 'sticky_banner_bg', '背景色', $settings, '#C9553E', '#C9553E' ); ?>
</table>
