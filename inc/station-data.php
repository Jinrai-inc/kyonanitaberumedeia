<?php
/**
 * 主要駅の緯度経度データ
 * カテゴリID → 駅配列のマッピング
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'KNT_STATION_DATA', array(
    // 千代田区(99)
    99 => array(
        array( 'name' => '東京駅', 'lat' => 35.6812, 'lng' => 139.7671, 'lines' => 'JR各線・丸ノ内線' ),
        array( 'name' => '秋葉原駅', 'lat' => 35.6984, 'lng' => 139.7731, 'lines' => 'JR各線・日比谷線・TX' ),
        array( 'name' => '神田駅', 'lat' => 35.6918, 'lng' => 139.7709, 'lines' => 'JR各線・銀座線' ),
        array( 'name' => '御茶ノ水駅', 'lat' => 35.6993, 'lng' => 139.7653, 'lines' => 'JR各線・丸ノ内線' ),
        array( 'name' => '神保町駅', 'lat' => 35.6960, 'lng' => 139.7578, 'lines' => '半蔵門線・都営三田線・新宿線' ),
        array( 'name' => '大手町駅', 'lat' => 35.6866, 'lng' => 139.7638, 'lines' => '丸ノ内線・東西線・千代田線・半蔵門線' ),
    ),
    // 中央区(100)
    100 => array(
        array( 'name' => '銀座駅', 'lat' => 35.6717, 'lng' => 139.7637, 'lines' => '銀座線・丸ノ内線・日比谷線' ),
        array( 'name' => '日本橋駅', 'lat' => 35.6824, 'lng' => 139.7741, 'lines' => '銀座線・東西線・都営浅草線' ),
        array( 'name' => '築地駅', 'lat' => 35.6676, 'lng' => 139.7717, 'lines' => '日比谷線' ),
        array( 'name' => '人形町駅', 'lat' => 35.6866, 'lng' => 139.7822, 'lines' => '日比谷線・都営浅草線' ),
        array( 'name' => '月島駅', 'lat' => 35.6627, 'lng' => 139.7835, 'lines' => '有楽町線・都営大江戸線' ),
    ),
    // 港区(101)
    101 => array(
        array( 'name' => '六本木駅', 'lat' => 35.6627, 'lng' => 139.7312, 'lines' => '日比谷線・都営大江戸線' ),
        array( 'name' => '新橋駅', 'lat' => 35.6662, 'lng' => 139.7584, 'lines' => 'JR各線・銀座線・都営浅草線' ),
        array( 'name' => '品川駅', 'lat' => 35.6308, 'lng' => 139.7401, 'lines' => 'JR各線・京急線' ),
        array( 'name' => '表参道駅', 'lat' => 35.6653, 'lng' => 139.7122, 'lines' => '銀座線・千代田線・半蔵門線' ),
        array( 'name' => '赤坂駅', 'lat' => 35.6728, 'lng' => 139.7373, 'lines' => '千代田線' ),
        array( 'name' => '麻布十番駅', 'lat' => 35.6555, 'lng' => 139.7370, 'lines' => '南北線・都営大江戸線' ),
    ),
    // 新宿区(102)
    102 => array(
        array( 'name' => '新宿駅', 'lat' => 35.6896, 'lng' => 139.7006, 'lines' => 'JR各線・小田急・京王・丸ノ内線' ),
        array( 'name' => '新大久保駅', 'lat' => 35.7012, 'lng' => 139.7001, 'lines' => 'JR山手線' ),
        array( 'name' => '高田馬場駅', 'lat' => 35.7128, 'lng' => 139.7038, 'lines' => 'JR山手線・西武新宿線・東西線' ),
        array( 'name' => '神楽坂駅', 'lat' => 35.7033, 'lng' => 139.7410, 'lines' => '東西線' ),
    ),
    // 文京区(103)
    103 => array(
        array( 'name' => '後楽園駅', 'lat' => 35.7075, 'lng' => 139.7520, 'lines' => '丸ノ内線・南北線' ),
        array( 'name' => '本郷三丁目駅', 'lat' => 35.7072, 'lng' => 139.7601, 'lines' => '丸ノ内線・都営大江戸線' ),
    ),
    // 台東区(104)
    104 => array(
        array( 'name' => '上野駅', 'lat' => 35.7141, 'lng' => 139.7774, 'lines' => 'JR各線・銀座線・日比谷線' ),
        array( 'name' => '浅草駅', 'lat' => 35.7117, 'lng' => 139.7966, 'lines' => '銀座線・都営浅草線・東武' ),
        array( 'name' => '御徒町駅', 'lat' => 35.7078, 'lng' => 139.7745, 'lines' => 'JR山手線・京浜東北線' ),
    ),
    // 墨田区(105)
    105 => array(
        array( 'name' => '錦糸町駅', 'lat' => 35.6966, 'lng' => 139.8143, 'lines' => 'JR総武線・半蔵門線' ),
    ),
    // 江東区(106)
    106 => array(
        array( 'name' => '豊洲駅', 'lat' => 35.6536, 'lng' => 139.7965, 'lines' => '有楽町線・ゆりかもめ' ),
        array( 'name' => '門前仲町駅', 'lat' => 35.6733, 'lng' => 139.7961, 'lines' => '東西線・都営大江戸線' ),
        array( 'name' => '亀戸駅', 'lat' => 35.6967, 'lng' => 139.8266, 'lines' => 'JR総武線・東武亀戸線' ),
    ),
    // 品川区(107)
    107 => array(
        array( 'name' => '大井町駅', 'lat' => 35.6067, 'lng' => 139.7349, 'lines' => 'JR京浜東北線・東急大井町線・りんかい線' ),
        array( 'name' => '五反田駅', 'lat' => 35.6262, 'lng' => 139.7234, 'lines' => 'JR山手線・東急池上線・都営浅草線' ),
        array( 'name' => '目黒駅', 'lat' => 35.6340, 'lng' => 139.7158, 'lines' => 'JR山手線・南北線・都営三田線' ),
    ),
    // 目黒区(108)
    108 => array(
        array( 'name' => '中目黒駅', 'lat' => 35.6444, 'lng' => 139.6987, 'lines' => '日比谷線・東急東横線' ),
        array( 'name' => '自由が丘駅', 'lat' => 35.6077, 'lng' => 139.6698, 'lines' => '東急東横線・大井町線' ),
        array( 'name' => '学芸大学駅', 'lat' => 35.6289, 'lng' => 139.6854, 'lines' => '東急東横線' ),
    ),
    // 大田区(109)
    109 => array(
        array( 'name' => '蒲田駅', 'lat' => 35.5627, 'lng' => 139.7160, 'lines' => 'JR京浜東北線・東急池上線・多摩川線' ),
        array( 'name' => '大森駅', 'lat' => 35.5884, 'lng' => 139.7276, 'lines' => 'JR京浜東北線' ),
    ),
    // 世田谷区(110)
    110 => array(
        array( 'name' => '三軒茶屋駅', 'lat' => 35.6436, 'lng' => 139.6705, 'lines' => '東急田園都市線・世田谷線' ),
        array( 'name' => '下北沢駅', 'lat' => 35.6611, 'lng' => 139.6671, 'lines' => '小田急線・京王井の頭線' ),
        array( 'name' => '二子玉川駅', 'lat' => 35.6115, 'lng' => 139.6269, 'lines' => '東急田園都市線・大井町線' ),
        array( 'name' => '成城学園前駅', 'lat' => 35.6410, 'lng' => 139.5996, 'lines' => '小田急線' ),
    ),
    // 渋谷区(111)
    111 => array(
        array( 'name' => '渋谷駅', 'lat' => 35.6580, 'lng' => 139.7016, 'lines' => 'JR各線・東急・京王・銀座線・半蔵門線・副都心線' ),
        array( 'name' => '恵比寿駅', 'lat' => 35.6467, 'lng' => 139.7100, 'lines' => 'JR山手線・埼京線・日比谷線' ),
        array( 'name' => '原宿駅', 'lat' => 35.6702, 'lng' => 139.7027, 'lines' => 'JR山手線' ),
        array( 'name' => '代官山駅', 'lat' => 35.6488, 'lng' => 139.7034, 'lines' => '東急東横線' ),
        array( 'name' => '神泉駅', 'lat' => 35.6562, 'lng' => 139.6942, 'lines' => '京王井の頭線' ),
    ),
    // 中野区(112)
    112 => array(
        array( 'name' => '中野駅', 'lat' => 35.7072, 'lng' => 139.6654, 'lines' => 'JR中央線・東西線' ),
    ),
    // 杉並区(113)
    113 => array(
        array( 'name' => '荻窪駅', 'lat' => 35.7040, 'lng' => 139.6200, 'lines' => 'JR中央線・丸ノ内線' ),
        array( 'name' => '高円寺駅', 'lat' => 35.7054, 'lng' => 139.6496, 'lines' => 'JR中央線' ),
        array( 'name' => '阿佐ヶ谷駅', 'lat' => 35.7049, 'lng' => 139.6354, 'lines' => 'JR中央線' ),
    ),
    // 豊島区(114)
    114 => array(
        array( 'name' => '池袋駅', 'lat' => 35.7295, 'lng' => 139.7109, 'lines' => 'JR各線・東武東上線・西武池袋線・丸ノ内線・有楽町線・副都心線' ),
        array( 'name' => '大塚駅', 'lat' => 35.7315, 'lng' => 139.7283, 'lines' => 'JR山手線' ),
        array( 'name' => '巣鴨駅', 'lat' => 35.7334, 'lng' => 139.7394, 'lines' => 'JR山手線・都営三田線' ),
    ),
    // 北区(115)
    115 => array(
        array( 'name' => '赤羽駅', 'lat' => 35.7779, 'lng' => 139.7210, 'lines' => 'JR各線' ),
        array( 'name' => '王子駅', 'lat' => 35.7529, 'lng' => 139.7379, 'lines' => 'JR京浜東北線・南北線' ),
        array( 'name' => '十条駅', 'lat' => 35.7638, 'lng' => 139.7187, 'lines' => 'JR埼京線' ),
    ),
    // 足立区(119)
    119 => array(
        array( 'name' => '北千住駅', 'lat' => 35.7494, 'lng' => 139.8048, 'lines' => 'JR常磐線・東武・日比谷線・千代田線・TX' ),
        array( 'name' => '西新井駅', 'lat' => 35.7754, 'lng' => 139.7876, 'lines' => '東武スカイツリーライン' ),
    ),
    // 八王子市(122)
    122 => array(
        array( 'name' => '八王子駅', 'lat' => 35.6558, 'lng' => 139.3389, 'lines' => 'JR中央線・横浜線・八高線' ),
    ),
    // 立川市(123)
    123 => array(
        array( 'name' => '立川駅', 'lat' => 35.6981, 'lng' => 139.4139, 'lines' => 'JR中央線・南武線・青梅線' ),
    ),
    // 武蔵野市(124)
    124 => array(
        array( 'name' => '吉祥寺駅', 'lat' => 35.7030, 'lng' => 139.5796, 'lines' => 'JR中央線・京王井の頭線' ),
    ),
    // 町田市(126)
    126 => array(
        array( 'name' => '町田駅', 'lat' => 35.5423, 'lng' => 139.4456, 'lines' => 'JR横浜線・小田急線' ),
    ),
    // 横浜市(128)
    128 => array(
        array( 'name' => '横浜駅', 'lat' => 35.4660, 'lng' => 139.6226, 'lines' => 'JR各線・京急線・東急東横線・相鉄線' ),
        array( 'name' => '関内駅', 'lat' => 35.4437, 'lng' => 139.6362, 'lines' => 'JR根岸線・横浜市営地下鉄' ),
        array( 'name' => '桜木町駅', 'lat' => 35.4510, 'lng' => 139.6310, 'lines' => 'JR根岸線・横浜市営地下鉄' ),
        array( 'name' => '新横浜駅', 'lat' => 35.5073, 'lng' => 139.6172, 'lines' => 'JR横浜線・新幹線・地下鉄' ),
        array( 'name' => '中華街駅', 'lat' => 35.4424, 'lng' => 139.6471, 'lines' => 'みなとみらい線' ),
    ),
    // 川崎市(129)
    129 => array(
        array( 'name' => '川崎駅', 'lat' => 35.5309, 'lng' => 139.6968, 'lines' => 'JR各線' ),
        array( 'name' => '武蔵小杉駅', 'lat' => 35.5764, 'lng' => 139.6596, 'lines' => 'JR各線・東急東横線・目黒線' ),
        array( 'name' => '溝の口駅', 'lat' => 35.6004, 'lng' => 139.6103, 'lines' => '東急田園都市線・JR南武線' ),
    ),
    // 鎌倉市(138)
    138 => array(
        array( 'name' => '鎌倉駅', 'lat' => 35.3191, 'lng' => 139.5504, 'lines' => 'JR横須賀線・江ノ電' ),
    ),
    // 大阪市(231)
    231 => array(
        array( 'name' => '梅田駅', 'lat' => 34.7048, 'lng' => 135.4987, 'lines' => '御堂筋線・阪急線・阪神線' ),
        array( 'name' => '難波駅', 'lat' => 34.6629, 'lng' => 135.5013, 'lines' => '御堂筋線・南海線・近鉄線' ),
        array( 'name' => '心斎橋駅', 'lat' => 34.6745, 'lng' => 135.5009, 'lines' => '御堂筋線・長堀鶴見緑地線' ),
        array( 'name' => '天王寺駅', 'lat' => 34.6462, 'lng' => 135.5131, 'lines' => 'JR各線・御堂筋線・谷町線' ),
        array( 'name' => '新大阪駅', 'lat' => 34.7334, 'lng' => 135.5001, 'lines' => 'JR各線・御堂筋線' ),
    ),
) );

function knt_get_stations_by_category( $category_id ) {
    return KNT_STATION_DATA[ $category_id ] ?? array();
}

function knt_get_station_names( $category_id ) {
    return array_map( function( $s ) { return $s['name']; }, knt_get_stations_by_category( $category_id ) );
}

function knt_format_station_names( $category_id, $max = 3 ) {
    $stations = knt_get_stations_by_category( $category_id );
    if ( empty( $stations ) ) return '';
    $names = array_slice( array_map( function( $s ) { return $s['name']; }, $stations ), 0, $max );
    return implode( '・', $names );
}
