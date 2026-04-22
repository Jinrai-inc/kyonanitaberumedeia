<?php
$settings = get_option( 'knt_settings', array() );
?>
<h2>SNS設定</h2>
<table class="form-table">
    <?php knt_text_field( 'sns_twitter', 'X（Twitter）URL', $settings, '' ); ?>
    <?php knt_text_field( 'sns_instagram', 'Instagram URL', $settings, '' ); ?>
    <?php knt_text_field( 'sns_facebook', 'Facebook URL', $settings, '' ); ?>
    <?php knt_text_field( 'sns_line', 'LINE公式アカウントURL', $settings, '' ); ?>
    <?php knt_text_field( 'sns_youtube', 'YouTube URL', $settings, '' ); ?>
</table>
