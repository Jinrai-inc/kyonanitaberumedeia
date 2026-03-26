<?php
/**
 * Tab: 表示設定 -- フロントページセクションのON/OFF
 */
$settings = get_option( 'knt_settings', array() );
?>
<h2>フロントページ セクション</h2>
<p class="description">各セクションの表示/非表示を切り替えます。</p>

<?php
knt_toggle( 'fp_content_slider_show', 'コンテンツスライダー', $settings, true );
knt_toggle( 'fp_japan_map_show', '日本地図エリア検索', $settings, true );
knt_toggle( 'fp_popular_show', '人気記事', $settings, true );
knt_toggle( 'fp_pr_show', 'PR店舗', $settings, true );
knt_toggle( 'fp_latest_show', '新着記事', $settings, true );
knt_toggle( 'fp_category_sections_show', 'カテゴリセクション', $settings, true );
knt_toggle( 'fp_app_banner_show', 'アプリバナー', $settings, true );
?>

<h2 style="margin-top: 32px;">スライダー設定</h2>
<table class="form-table">
    <?php knt_text_field( 'slider_count', 'スライダー表示件数', $settings, '5', '5' ); ?>
</table>

<h2 style="margin-top: 32px;">日本地図セクション</h2>
<table class="form-table">
    <?php knt_text_field( 'japan_map_title', '地図セクション タイトル', $settings, '食べたいエリアを選んでね' ); ?>
    <?php knt_text_field( 'japan_map_subtitle', '地図セクション サブタイトル', $settings, '地図をタップ or 下のメニューから選択できるよ' ); ?>
</table>

<h2 style="margin-top: 32px;">人気記事</h2>
<table class="form-table">
    <?php knt_text_field( 'popular_title', 'セクションタイトル', $settings, '人気記事' ); ?>
    <?php knt_text_field( 'popular_count', '表示件数', $settings, '5', '5' ); ?>
</table>

<h2 style="margin-top: 32px;">新着記事</h2>
<table class="form-table">
    <?php knt_text_field( 'latest_title', 'セクションタイトル', $settings, '新着記事' ); ?>
    <?php knt_text_field( 'latest_count', '表示件数', $settings, '6', '6' ); ?>
</table>
