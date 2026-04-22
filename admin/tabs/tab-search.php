<?php
$settings = get_option( 'knt_settings', array() );
?>
<h2>検索設定</h2>
<table class="form-table">
    <tr>
        <th><label>検索結果ページ</label></th>
        <td>
            <select name="knt_settings[search_result_style]">
                <option value="grid" <?php selected( $settings['search_result_style'] ?? 'grid', 'grid' ); ?>>グリッド表示</option>
                <option value="list" <?php selected( $settings['search_result_style'] ?? 'grid', 'list' ); ?>>リスト表示</option>
            </select>
        </td>
    </tr>
    <?php knt_text_field( 'search_placeholder', '検索ボックスのプレースホルダー', $settings, 'キーワードで記事を検索...' ); ?>
</table>
