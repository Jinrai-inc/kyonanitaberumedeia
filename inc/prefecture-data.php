<?php
/**
 * KNT Prefecture Data
 *
 * 47都道府県を8地方でグルーピングしたデータソース。
 * プレフェクチャーピッカー / エリアゲートで共通利用。
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 47都道府県のマスターデータを返す。
 * [ code, name, region, dish ] の配列。
 */
function knt_get_prefectures() {
    static $cache = null;
    if ( $cache !== null ) {
        return $cache;
    }

    $cache = array(
        // 北海道
        array( 'code' => '01', 'name' => '北海道',  'region' => 'hokkaido', 'dish' => '海鮮丼' ),
        // 東北
        array( 'code' => '02', 'name' => '青森県',  'region' => 'tohoku',   'dish' => 'せんべい汁' ),
        array( 'code' => '03', 'name' => '岩手県',  'region' => 'tohoku',   'dish' => 'わんこそば' ),
        array( 'code' => '04', 'name' => '宮城県',  'region' => 'tohoku',   'dish' => '牛タン' ),
        array( 'code' => '05', 'name' => '秋田県',  'region' => 'tohoku',   'dish' => 'きりたんぽ' ),
        array( 'code' => '06', 'name' => '山形県',  'region' => 'tohoku',   'dish' => '芋煮' ),
        array( 'code' => '07', 'name' => '福島県',  'region' => 'tohoku',   'dish' => '喜多方ラーメン' ),
        // 関東
        array( 'code' => '08', 'name' => '茨城県',  'region' => 'kanto',    'dish' => 'あんこう鍋' ),
        array( 'code' => '09', 'name' => '栃木県',  'region' => 'kanto',    'dish' => '餃子' ),
        array( 'code' => '10', 'name' => '群馬県',  'region' => 'kanto',    'dish' => '水沢うどん' ),
        array( 'code' => '11', 'name' => '埼玉県',  'region' => 'kanto',    'dish' => '武蔵野うどん' ),
        array( 'code' => '12', 'name' => '千葉県',  'region' => 'kanto',    'dish' => '落花生' ),
        array( 'code' => '13', 'name' => '東京都',  'region' => 'kanto',    'dish' => '江戸前寿司' ),
        array( 'code' => '14', 'name' => '神奈川県','region' => 'kanto',    'dish' => '家系ラーメン' ),
        // 中部
        array( 'code' => '15', 'name' => '新潟県',  'region' => 'chubu',    'dish' => 'へぎそば' ),
        array( 'code' => '16', 'name' => '富山県',  'region' => 'chubu',    'dish' => 'ます寿司' ),
        array( 'code' => '17', 'name' => '石川県',  'region' => 'chubu',    'dish' => '治部煮' ),
        array( 'code' => '18', 'name' => '福井県',  'region' => 'chubu',    'dish' => '越前そば' ),
        array( 'code' => '19', 'name' => '山梨県',  'region' => 'chubu',    'dish' => 'ほうとう' ),
        array( 'code' => '20', 'name' => '長野県',  'region' => 'chubu',    'dish' => '信州そば' ),
        array( 'code' => '21', 'name' => '岐阜県',  'region' => 'chubu',    'dish' => '飛騨牛' ),
        array( 'code' => '22', 'name' => '静岡県',  'region' => 'chubu',    'dish' => '静岡おでん' ),
        array( 'code' => '23', 'name' => '愛知県',  'region' => 'chubu',    'dish' => '味噌カツ' ),
        // 関西
        array( 'code' => '24', 'name' => '三重県',  'region' => 'kansai',   'dish' => '伊勢海老' ),
        array( 'code' => '25', 'name' => '滋賀県',  'region' => 'kansai',   'dish' => '近江牛' ),
        array( 'code' => '26', 'name' => '京都府',  'region' => 'kansai',   'dish' => '湯豆腐' ),
        array( 'code' => '27', 'name' => '大阪府',  'region' => 'kansai',   'dish' => 'たこ焼き' ),
        array( 'code' => '28', 'name' => '兵庫県',  'region' => 'kansai',   'dish' => '神戸牛' ),
        array( 'code' => '29', 'name' => '奈良県',  'region' => 'kansai',   'dish' => '柿の葉寿司' ),
        array( 'code' => '30', 'name' => '和歌山県','region' => 'kansai',   'dish' => '和歌山ラーメン' ),
        // 中国
        array( 'code' => '31', 'name' => '鳥取県',  'region' => 'chugoku',  'dish' => '松葉ガニ' ),
        array( 'code' => '32', 'name' => '島根県',  'region' => 'chugoku',  'dish' => '出雲そば' ),
        array( 'code' => '33', 'name' => '岡山県',  'region' => 'chugoku',  'dish' => 'ままかり' ),
        array( 'code' => '34', 'name' => '広島県',  'region' => 'chugoku',  'dish' => 'お好み焼き' ),
        array( 'code' => '35', 'name' => '山口県',  'region' => 'chugoku',  'dish' => 'ふぐ' ),
        // 四国
        array( 'code' => '36', 'name' => '徳島県',  'region' => 'shikoku',  'dish' => '徳島ラーメン' ),
        array( 'code' => '37', 'name' => '香川県',  'region' => 'shikoku',  'dish' => '讃岐うどん' ),
        array( 'code' => '38', 'name' => '愛媛県',  'region' => 'shikoku',  'dish' => '鯛めし' ),
        array( 'code' => '39', 'name' => '高知県',  'region' => 'shikoku',  'dish' => 'カツオのたたき' ),
        // 九州
        array( 'code' => '40', 'name' => '福岡県',  'region' => 'kyushu',   'dish' => 'もつ鍋' ),
        array( 'code' => '41', 'name' => '佐賀県',  'region' => 'kyushu',   'dish' => '呼子イカ' ),
        array( 'code' => '42', 'name' => '長崎県',  'region' => 'kyushu',   'dish' => 'ちゃんぽん' ),
        array( 'code' => '43', 'name' => '熊本県',  'region' => 'kyushu',   'dish' => '馬刺し' ),
        array( 'code' => '44', 'name' => '大分県',  'region' => 'kyushu',   'dish' => 'とり天' ),
        array( 'code' => '45', 'name' => '宮崎県',  'region' => 'kyushu',   'dish' => 'チキン南蛮' ),
        array( 'code' => '46', 'name' => '鹿児島県','region' => 'kyushu',   'dish' => '黒豚しゃぶ' ),
        array( 'code' => '47', 'name' => '沖縄県',  'region' => 'kyushu',   'dish' => '沖縄そば' ),
    );
    return $cache;
}

/**
 * 8地方のラベル（表示順）
 */
function knt_get_regions() {
    return array(
        'hokkaido' => '北海道',
        'tohoku'   => '東北',
        'kanto'    => '関東',
        'chubu'    => '中部',
        'kansai'   => '関西',
        'chugoku'  => '中国',
        'shikoku'  => '四国',
        'kyushu'   => '九州・沖縄',
    );
}

/**
 * 都道府県のアーカイブURL（area-XX カテゴリがあればそのリンク、無ければ検索フォールバック）
 */
function knt_prefecture_url( $pref_code, $pref_name ) {
    $slug = 'area-' . $pref_code;
    $term = get_term_by( 'slug', $slug, 'category' );
    if ( $term && ! is_wp_error( $term ) ) {
        return get_category_link( $term->term_id );
    }
    return home_url( '/?s=' . urlencode( $pref_name ) );
}

/**
 * 都道府県の記事数
 */
function knt_prefecture_count( $pref_code ) {
    $slug = 'area-' . $pref_code;
    $term = get_term_by( 'slug', $slug, 'category' );
    return ( $term && ! is_wp_error( $term ) ) ? intval( $term->count ) : 0;
}
