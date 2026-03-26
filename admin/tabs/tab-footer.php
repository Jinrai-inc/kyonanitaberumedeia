<?php
$settings = get_option( 'knt_settings', array() );
?>
<h2>フッター設定</h2>
<table class="form-table">
    <tr>
        <th><label>フッタースタイル</label></th>
        <td>
            <select name="knt_settings[footer_style]">
                <option value="dark" <?php selected( $settings['footer_style'] ?? 'dark', 'dark' ); ?>>ダーク</option>
                <option value="light" <?php selected( $settings['footer_style'] ?? 'dark', 'light' ); ?>>ライト</option>
            </select>
        </td>
    </tr>
    <?php knt_text_field( 'company_name', '運営会社名', $settings, '株式会社仁頼' ); ?>
    <?php knt_text_field( 'company_url', '運営会社URL', $settings, 'https://jinrai.co.jp' ); ?>
    <?php knt_text_field( 'copyright', 'コピーライト', $settings, '' ); ?>
    <?php knt_text_field( 'footer_description', 'サイト説明文', $settings, '' ); ?>
    <?php knt_text_field( 'privacy_url', 'プライバシーポリシーURL', $settings, '' ); ?>
    <?php knt_text_field( 'terms_url', '利用規約URL', $settings, '' ); ?>
</table>
