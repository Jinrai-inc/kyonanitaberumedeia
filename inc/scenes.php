<?php
/**
 * シーン定義マスター
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'KNT_SCENES', array(
    'date' => array(
        'label'       => 'デート',
        'slug'        => 'date',
        'keywords'    => 'デート おしゃれ 雰囲気',
        'allowed_genres' => array( 'G006','G004','G002','G003','G005','G011','G017' ),
        'excluded_genres' => array( 'G013','G014','G012' ),
        'api_filters' => array( 'private_room' => 1 ),
        'title_suffix' => 'おしゃれな隠れ家を厳選',
        'description'  => 'デートの食事選びは雰囲気が大切。個室やテラス席があるお店、記念日にも使えるおしゃれなレストランを厳選しました。',
    ),
    'nomikai' => array(
        'label'       => '飲み会・宴会',
        'slug'        => 'nomikai',
        'keywords'    => '飲み会 宴会 飲み放題 大人数',
        'allowed_genres' => array( 'G001','G002','G008','G004','G007','G009','G014' ),
        'excluded_genres' => array( 'G015','G013' ),
        'api_filters' => array( 'free_drink' => 1 ),
        'title_suffix' => '飲み放題付きの人気店を厳選',
        'description'  => '飲み会・宴会の幹事必見！飲み放題付きコースがあるお店や大人数対応の個室があるお店を中心に厳選しました。',
    ),
    'settai' => array(
        'label'       => '接待・ビジネス',
        'slug'        => 'settai',
        'keywords'    => '接待 個室 高級 落ち着いた',
        'allowed_genres' => array( 'G004','G006','G003','G005' ),
        'excluded_genres' => array( 'G001','G013','G014','G012','G008' ),
        'api_filters' => array( 'private_room' => 1 ),
        'title_suffix' => '個室ありの格式あるお店を厳選',
        'description'  => '大切な取引先との会食にふさわしい、個室完備で落ち着いた雰囲気のお店を厳選しました。',
    ),
    'joshikai' => array(
        'label'       => '女子会',
        'slug'        => 'joshikai',
        'keywords'    => '女子会 おしゃれ かわいい インスタ映え',
        'allowed_genres' => array( 'G006','G015','G002','G003','G009','G010','G005' ),
        'excluded_genres' => array( 'G013','G008' ),
        'api_filters' => array(),
        'title_suffix' => 'おしゃれで楽しいお店を厳選',
        'description'  => '女子会にぴったりのおしゃれなお店を厳選！インスタ映えするメニューやかわいい内装のお店を集めました。',
    ),
    'kinenbi' => array(
        'label'       => '記念日・誕生日',
        'slug'        => 'anniversary',
        'keywords'    => '記念日 誕生日 サプライズ ケーキ 特別',
        'allowed_genres' => array( 'G006','G004','G003','G005' ),
        'excluded_genres' => array( 'G001','G013','G014','G012','G008','G007' ),
        'api_filters' => array( 'private_room' => 1 ),
        'title_suffix' => 'サプライズもできる特別なお店を厳選',
        'description'  => '大切な人の誕生日や記念日にふさわしい、特別感のあるレストランを厳選。サプライズ対応のお店もあります。',
    ),
    'hitorimeshi' => array(
        'label'       => '一人飯・ソロ',
        'slug'        => 'solo',
        'keywords'    => '一人 カウンター 気軽',
        'allowed_genres' => array( 'G013','G012','G004','G005','G007','G015','G001' ),
        'excluded_genres' => array(),
        'api_filters' => array(),
        'title_suffix' => 'カウンター席で気軽に入れるお店を厳選',
        'description'  => '一人でもサクッと入れるカウンター席のあるお店を中心に厳選。一人飯にぴったりのお店を集めました。',
    ),
    'family' => array(
        'label'       => '家族・子連れ',
        'slug'        => 'family',
        'keywords'    => '家族 子連れ キッズ 座敷',
        'allowed_genres' => array( 'G004','G006','G007','G005','G015','G008','G014' ),
        'excluded_genres' => array( 'G017','G001','G002' ),
        'api_filters' => array( 'child' => 1 ),
        'title_suffix' => 'キッズメニューありの安心なお店を厳選',
        'description'  => 'お子様連れでも安心して食事が楽しめるお店を厳選！座敷席やキッズメニューがあるお店を集めました。',
    ),
    'goukon' => array(
        'label'       => '合コン',
        'slug'        => 'goukon',
        'keywords'    => '合コン 個室 飲み放題 盛り上がる',
        'allowed_genres' => array( 'G001','G002','G006','G008','G009' ),
        'excluded_genres' => array( 'G013','G015','G012' ),
        'api_filters' => array( 'private_room' => 1, 'free_drink' => 1 ),
        'title_suffix' => '個室＆飲み放題ありの盛り上がるお店を厳選',
        'description'  => '合コンの幹事さん必見！個室完備で飲み放題付きコースがある、盛り上がれるお店を厳選しました。',
    ),
    'lunch' => array(
        'label'       => 'ランチ',
        'slug'        => 'lunch',
        'keywords'    => 'ランチ 昼 お得 コスパ',
        'allowed_genres' => array( 'G013','G012','G004','G006','G005','G007','G015','G014' ),
        'excluded_genres' => array( 'G017' ),
        'api_filters' => array( 'lunch' => 1 ),
        'title_suffix' => 'コスパ最強のランチを厳選',
        'description'  => 'ランチタイムにおすすめのお店を厳選！コスパ抜群のランチセットが楽しめるお店を集めました。',
    ),
    'shinya' => array(
        'label'       => '深夜メシ・シメ',
        'slug'        => 'late-night',
        'keywords'    => '深夜 夜遅く シメ 深夜営業',
        'allowed_genres' => array( 'G013','G001','G007','G012','G004' ),
        'excluded_genres' => array( 'G006','G015' ),
        'api_filters' => array( 'midnight' => 1 ),
        'title_suffix' => '深夜営業の頼れるお店を厳選',
        'description'  => '飲み会の後や仕事帰りの遅い夜に。深夜まで営業している、シメにぴったりのお店を厳選しました。',
    ),
    'tabehodai' => array(
        'label'       => '食べ放題',
        'slug'        => 'all-you-can-eat',
        'keywords'    => '食べ放題 ビュッフェ バイキング',
        'allowed_genres' => array( 'G008','G004','G007','G006','G009','G014','G015' ),
        'excluded_genres' => array( 'G013','G017' ),
        'api_filters' => array( 'free_food' => 1 ),
        'title_suffix' => 'お得な食べ放題プランがあるお店を厳選',
        'description'  => 'がっつり食べたい日に！食べ放題プランがあるコスパ抜群のお店を厳選しました。',
    ),
) );

/**
 * ジャンルコード→名前マッピング
 */
function knt_genre_code_to_name( $code ) {
    $map = array(
        'G001' => '居酒屋', 'G002' => 'ダイニングバー', 'G003' => '創作料理',
        'G004' => '和食', 'G005' => '洋食', 'G006' => 'イタリアン・フレンチ',
        'G007' => '中華', 'G008' => '焼肉・ホルモン', 'G009' => '韓国料理',
        'G010' => 'アジア・エスニック', 'G011' => '各国料理', 'G012' => 'カレー',
        'G013' => 'ラーメン', 'G014' => 'お好み焼き', 'G015' => 'カフェ・スイーツ',
        'G016' => 'その他', 'G017' => 'バー・カクテル',
    );
    return $map[ $code ] ?? $code;
}

/**
 * シーンデータをJS用に整形
 */
function knt_scenes_for_js() {
    $result = array();
    foreach ( KNT_SCENES as $key => $scene ) {
        $result[ $key ] = array(
            'label'       => $scene['label'],
            'description' => $scene['description'],
            'api_filters' => $scene['api_filters'],
            'allowed_genres_labels' => array_map( 'knt_genre_code_to_name', $scene['allowed_genres'] ),
            'excluded_genres_labels' => array_map( 'knt_genre_code_to_name', $scene['excluded_genres'] ),
        );
    }
    return $result;
}
