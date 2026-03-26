<?php
$settings = get_option( 'knt_settings', array() );
?>
<h2>表示速度最適化</h2>
<?php knt_toggle( 'lazy_load', '画像の遅延読み込み', $settings, true ); ?>
<?php knt_toggle( 'google_fonts', 'Google Fontsを読み込む', $settings, true ); ?>
<?php knt_toggle( 'emoji_disable', 'WordPress絵文字スクリプトを無効化', $settings, false ); ?>
