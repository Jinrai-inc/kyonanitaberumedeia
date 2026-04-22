<?php
/**
 * リード文 + タイトルテンプレート
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'KNT_LEAD_TEMPLATES', array(
    'genre' => array(
        '{area}で美味しい{genre}を探していませんか？{area}は{genre}の激戦区で、こだわりの名店がひしめくエリアです。この記事では、口コミ評価の高い人気店の中から厳選した{count}店舗をご紹介します。すべて駅から徒歩圏内で、予約リンク付き。今日のお店選びにお役立てください。',
        '「今日は{area}で{genre}が食べたい！」そんなあなたにぴったりの{count}店舗を厳選しました。地元で愛される名店からSNSで話題のお店まで、ハズレなしのラインナップでお届けします。',
        '{area}で{genre}を食べるならどこがいい？そんな悩みを一発で解決！{area}で本当に美味しい{genre}が食べられるお店を{count}店舗ピックアップしました。アクセス・予算・営業時間まですべて網羅しています。',
        '{year}年、{area}の{genre}シーンがアツい！人気店を{count}店舗厳選しました。初めて訪れる方もリピーターの方も、きっとお気に入りの一杯が見つかるはずです。',
        '{area}は{genre}の名店が集まるグルメエリア。定番の人気店から隠れた実力派まで、{count}店舗を厳選してご紹介します。各店舗の写真・予算・アクセス情報に加えて、ネット予約リンクも掲載しています。',
    ),
    'scene' => array(
        '{area}で{scene}にぴったりのお店をお探しですか？雰囲気・メニュー・設備までこだわって厳選した{count}店舗をご紹介します。{scene_desc}',
        '「{area}で{scene}、どこにしよう？」と悩んでいる方へ。{area}で{scene}に使えるお店を{count}店舗まとめました。すべて予約可能なお店です。',
        '{area}で{scene}に最適なお店を探すなら、この記事をブックマーク！{area}で{scene}にぴったりの{count}店舗を厳選しました。',
        '{year}年版、{area}で{scene}に使えるお店ガイド。好アクセスのお店を中心に、{count}店舗を厳選しました。予約リンク付きです。',
        '{area}で{scene}のお店選びに迷ったら、この記事で解決！{scene_desc} 厳選{count}店舗の情報をお届けします。',
    ),
    'station' => array(
        '{station}で美味しい{genre}をお探しですか？{station}周辺は{genre}の名店が揃うグルメスポットです。徒歩圏内で行ける人気{count}店舗を厳選してご紹介します。',
        '「{station}で{genre}が食べたい！」そんな時にすぐ使える、{station}から歩いて行けるおすすめ{count}店舗をまとめました。',
        '{station}周辺の{genre}はレベルが高い！改札を出てすぐ行ける人気店から実力店まで、厳選{count}店舗をお届けします。すべて予約リンク付きです。',
        '{station}でランチやディナーに{genre}はいかがですか？{station}徒歩圏内で味わえる、本当に美味しいお店を{count}店舗ご紹介します。',
        '{year}年、{station}周辺で人気の{genre}店を徹底調査！改札からすぐ行ける名店を{count}店舗厳選しました。',
    ),
) );

define( 'KNT_TITLE_TEMPLATES', array(
    'genre'         => '【{year}年最新】{area}で人気の{genre}おすすめ{count}選｜実力店を厳選',
    'genre_no_st'   => '【{year}年最新】{area}で人気の{genre}おすすめ{count}選｜実力店を厳選',
    'scene'         => '【{year}年】{area}で{scene}におすすめのお店{count}選｜{suffix}',
    'scene_no_st'   => '【{year}年】{area}で{scene}におすすめのお店{count}選｜{suffix}',
    'station_genre' => '【{year}年最新】{station}周辺で美味しい{genre}おすすめ{count}選',
    'station_scene' => '【{year}年】{station}周辺で{scene}におすすめのお店{count}選｜{suffix}',
) );

/**
 * リード文を自動生成
 */
function knt_generate_lead( $type, $vars ) {
    $templates = KNT_LEAD_TEMPLATES[ $type ] ?? KNT_LEAD_TEMPLATES['genre'];
    $template = $templates[ array_rand( $templates ) ];

    $replacements = array(
        '{area}'       => $vars['area'] ?? '',
        '{stations}'   => $vars['stations'] ?? '',
        '{station}'    => $vars['station'] ?? '',
        '{genre}'      => $vars['genre'] ?? '',
        '{scene}'      => $vars['scene'] ?? '',
        '{scene_desc}' => $vars['scene_desc'] ?? '',
        '{count}'      => $vars['count'] ?? 5,
        '{year}'       => date( 'Y' ),
    );

    return str_replace( array_keys( $replacements ), array_values( $replacements ), $template );
}

/**
 * タイトルを自動生成
 */
function knt_generate_title( $type, $vars ) {
    $template = KNT_TITLE_TEMPLATES[ $type ] ?? KNT_TITLE_TEMPLATES['genre_no_st'];

    $replacements = array(
        '{year}'     => date( 'Y' ),
        '{area}'     => $vars['area'] ?? '',
        '{stations}' => $vars['stations'] ?? '',
        '{station}'  => $vars['station'] ?? '',
        '{genre}'    => $vars['genre'] ?? '',
        '{scene}'    => $vars['scene'] ?? '',
        '{suffix}'   => $vars['suffix'] ?? '',
        '{count}'    => $vars['count'] ?? 5,
    );

    return str_replace( array_keys( $replacements ), array_values( $replacements ), $template );
}
