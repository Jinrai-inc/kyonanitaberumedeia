<?php
$settings = get_option( 'knt_settings', array() );
?>
<h2>レイアウト設定</h2>
<table class="form-table">
    <tr>
        <th><label>コンテンツ幅</label></th>
        <td>
            <select name="knt_settings[content_width]">
                <option value="720" <?php selected( $settings['content_width'] ?? '720', '720' ); ?>>720px（標準）</option>
                <option value="800" <?php selected( $settings['content_width'] ?? '720', '800' ); ?>>800px（やや広め）</option>
                <option value="960" <?php selected( $settings['content_width'] ?? '720', '960' ); ?>>960px（広め）</option>
            </select>
        </td>
    </tr>
    <tr>
        <th><label>カードグリッド列数</label></th>
        <td>
            <select name="knt_settings[grid_columns]">
                <option value="2" <?php selected( $settings['grid_columns'] ?? '3', '2' ); ?>>2列</option>
                <option value="3" <?php selected( $settings['grid_columns'] ?? '3', '3' ); ?>>3列（標準）</option>
                <option value="4" <?php selected( $settings['grid_columns'] ?? '3', '4' ); ?>>4列</option>
            </select>
        </td>
    </tr>
</table>

<h2 style="margin-top: 32px;">記事ページ</h2>
<?php knt_toggle( 'show_toc', '目次を自動生成', $settings, true ); ?>
<?php knt_toggle( 'show_related', '関連記事を表示', $settings, true ); ?>
<table class="form-table">
    <?php knt_text_field( 'related_count', '関連記事の表示件数', $settings, '3', '3' ); ?>
</table>
