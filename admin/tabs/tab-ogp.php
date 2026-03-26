<?php
$settings = get_option( 'knt_settings', array() );
?>
<h2>OGP設定</h2>
<table class="form-table">
    <?php knt_text_field( 'ogp_image', 'デフォルトOGP画像URL', $settings, '', 'https://...' ); ?>
    <?php knt_text_field( 'twitter_handle', 'X（Twitter）アカウント', $settings, '', '@example' ); ?>
</table>
