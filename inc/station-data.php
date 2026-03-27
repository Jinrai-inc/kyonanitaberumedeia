<?php
/**
 * 主要駅の緯度経度データ
 * カテゴリslug → 駅配列 のマッピング（カテゴリIDに依存しない）
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 駅データマスター（カテゴリslugキー）
 */
function knt_get_all_station_data() {
    static $data = null;
    if ( $data !== null ) return $data;

    $data = array(
        // ===== 北海道 =====
        'area-01-札幌市' => array(
            array( 'name' => '札幌駅', 'lat' => 43.0687, 'lng' => 141.3508, 'lines' => 'JR各線・南北線' ),
            array( 'name' => 'すすきの駅', 'lat' => 43.0554, 'lng' => 141.3531, 'lines' => '南北線' ),
            array( 'name' => '大通駅', 'lat' => 43.0607, 'lng' => 141.3563, 'lines' => '南北線・東西線・東豊線' ),
        ),
        'area-01-函館市' => array(
            array( 'name' => '函館駅', 'lat' => 41.7738, 'lng' => 140.7268, 'lines' => 'JR函館本線' ),
        ),
        'area-01-旭川市' => array(
            array( 'name' => '旭川駅', 'lat' => 43.7630, 'lng' => 142.3580, 'lines' => 'JR各線' ),
        ),

        // ===== 埼玉県 =====
        'area-11-さいたま市' => array(
            array( 'name' => '大宮駅', 'lat' => 35.9063, 'lng' => 139.6238, 'lines' => 'JR各線・東武野田線・ニューシャトル' ),
            array( 'name' => '浦和駅', 'lat' => 35.8585, 'lng' => 139.6567, 'lines' => 'JR京浜東北線・宇都宮線・高崎線' ),
            array( 'name' => 'さいたま新都心駅', 'lat' => 35.8938, 'lng' => 139.6312, 'lines' => 'JR京浜東北線・宇都宮線' ),
            array( 'name' => '武蔵浦和駅', 'lat' => 35.8445, 'lng' => 139.6384, 'lines' => 'JR埼京線・武蔵野線' ),
            array( 'name' => '北浦和駅', 'lat' => 35.8726, 'lng' => 139.6452, 'lines' => 'JR京浜東北線' ),
        ),
        'area-11-川越市' => array(
            array( 'name' => '川越駅', 'lat' => 35.9076, 'lng' => 139.4856, 'lines' => 'JR川越線・東武東上線' ),
            array( 'name' => '本川越駅', 'lat' => 35.9189, 'lng' => 139.4838, 'lines' => '西武新宿線' ),
        ),
        'area-11-川口市' => array(
            array( 'name' => '川口駅', 'lat' => 35.8069, 'lng' => 139.7207, 'lines' => 'JR京浜東北線' ),
        ),
        'area-11-所沢市' => array(
            array( 'name' => '所沢駅', 'lat' => 35.7868, 'lng' => 139.4685, 'lines' => '西武池袋線・新宿線' ),
        ),
        'area-11-越谷市' => array(
            array( 'name' => '南越谷駅', 'lat' => 35.8631, 'lng' => 139.7910, 'lines' => 'JR武蔵野線' ),
        ),
        'area-11-春日部市' => array(
            array( 'name' => '春日部駅', 'lat' => 35.9762, 'lng' => 139.7524, 'lines' => '東武スカイツリーライン・野田線' ),
        ),

        // ===== 千葉県 =====
        'area-12-千葉市' => array(
            array( 'name' => '千葉駅', 'lat' => 35.6131, 'lng' => 140.1131, 'lines' => 'JR各線・千葉都市モノレール' ),
            array( 'name' => '海浜幕張駅', 'lat' => 35.6488, 'lng' => 140.0390, 'lines' => 'JR京葉線' ),
        ),
        'area-12-船橋市' => array(
            array( 'name' => '船橋駅', 'lat' => 35.7015, 'lng' => 139.9854, 'lines' => 'JR総武線・東武野田線' ),
        ),
        'area-12-松戸市' => array(
            array( 'name' => '松戸駅', 'lat' => 35.7831, 'lng' => 139.9008, 'lines' => 'JR常磐線・新京成線' ),
        ),
        'area-12-柏市' => array(
            array( 'name' => '柏駅', 'lat' => 35.8580, 'lng' => 139.9723, 'lines' => 'JR常磐線・東武野田線' ),
        ),

        // ===== 東京都（既存 + 追加） =====
        'area-13-千代田区' => array(
            array( 'name' => '東京駅', 'lat' => 35.6812, 'lng' => 139.7671, 'lines' => 'JR各線・丸ノ内線' ),
            array( 'name' => '秋葉原駅', 'lat' => 35.6984, 'lng' => 139.7731, 'lines' => 'JR各線・日比谷線・TX' ),
            array( 'name' => '神田駅', 'lat' => 35.6918, 'lng' => 139.7709, 'lines' => 'JR各線・銀座線' ),
            array( 'name' => '御茶ノ水駅', 'lat' => 35.6993, 'lng' => 139.7653, 'lines' => 'JR各線・丸ノ内線' ),
            array( 'name' => '神保町駅', 'lat' => 35.6960, 'lng' => 139.7578, 'lines' => '半蔵門線・都営三田線・新宿線' ),
            array( 'name' => '大手町駅', 'lat' => 35.6866, 'lng' => 139.7638, 'lines' => '丸ノ内線・東西線・千代田線・半蔵門線' ),
        ),
        'area-13-中央区' => array(
            array( 'name' => '銀座駅', 'lat' => 35.6717, 'lng' => 139.7637, 'lines' => '銀座線・丸ノ内線・日比谷線' ),
            array( 'name' => '日本橋駅', 'lat' => 35.6824, 'lng' => 139.7741, 'lines' => '銀座線・東西線・都営浅草線' ),
            array( 'name' => '築地駅', 'lat' => 35.6676, 'lng' => 139.7717, 'lines' => '日比谷線' ),
            array( 'name' => '人形町駅', 'lat' => 35.6866, 'lng' => 139.7822, 'lines' => '日比谷線・都営浅草線' ),
            array( 'name' => '月島駅', 'lat' => 35.6627, 'lng' => 139.7835, 'lines' => '有楽町線・都営大江戸線' ),
        ),
        'area-13-港区' => array(
            array( 'name' => '六本木駅', 'lat' => 35.6627, 'lng' => 139.7312, 'lines' => '日比谷線・都営大江戸線' ),
            array( 'name' => '新橋駅', 'lat' => 35.6662, 'lng' => 139.7584, 'lines' => 'JR各線・銀座線・都営浅草線' ),
            array( 'name' => '品川駅', 'lat' => 35.6308, 'lng' => 139.7401, 'lines' => 'JR各線・京急線' ),
            array( 'name' => '表参道駅', 'lat' => 35.6653, 'lng' => 139.7122, 'lines' => '銀座線・千代田線・半蔵門線' ),
            array( 'name' => '赤坂駅', 'lat' => 35.6728, 'lng' => 139.7373, 'lines' => '千代田線' ),
            array( 'name' => '麻布十番駅', 'lat' => 35.6555, 'lng' => 139.7370, 'lines' => '南北線・都営大江戸線' ),
        ),
        'area-13-新宿区' => array(
            array( 'name' => '新宿駅', 'lat' => 35.6896, 'lng' => 139.7006, 'lines' => 'JR各線・小田急・京王・丸ノ内線' ),
            array( 'name' => '新大久保駅', 'lat' => 35.7012, 'lng' => 139.7001, 'lines' => 'JR山手線' ),
            array( 'name' => '高田馬場駅', 'lat' => 35.7128, 'lng' => 139.7038, 'lines' => 'JR山手線・西武新宿線・東西線' ),
            array( 'name' => '神楽坂駅', 'lat' => 35.7033, 'lng' => 139.7410, 'lines' => '東西線' ),
        ),
        'area-13-文京区' => array(
            array( 'name' => '後楽園駅', 'lat' => 35.7075, 'lng' => 139.7520, 'lines' => '丸ノ内線・南北線' ),
            array( 'name' => '本郷三丁目駅', 'lat' => 35.7072, 'lng' => 139.7601, 'lines' => '丸ノ内線・都営大江戸線' ),
        ),
        'area-13-台東区' => array(
            array( 'name' => '上野駅', 'lat' => 35.7141, 'lng' => 139.7774, 'lines' => 'JR各線・銀座線・日比谷線' ),
            array( 'name' => '浅草駅', 'lat' => 35.7117, 'lng' => 139.7966, 'lines' => '銀座線・都営浅草線・東武' ),
            array( 'name' => '御徒町駅', 'lat' => 35.7078, 'lng' => 139.7745, 'lines' => 'JR山手線・京浜東北線' ),
        ),
        'area-13-墨田区' => array(
            array( 'name' => '錦糸町駅', 'lat' => 35.6966, 'lng' => 139.8143, 'lines' => 'JR総武線・半蔵門線' ),
        ),
        'area-13-江東区' => array(
            array( 'name' => '豊洲駅', 'lat' => 35.6536, 'lng' => 139.7965, 'lines' => '有楽町線・ゆりかもめ' ),
            array( 'name' => '門前仲町駅', 'lat' => 35.6733, 'lng' => 139.7961, 'lines' => '東西線・都営大江戸線' ),
            array( 'name' => '亀戸駅', 'lat' => 35.6967, 'lng' => 139.8266, 'lines' => 'JR総武線・東武亀戸線' ),
        ),
        'area-13-品川区' => array(
            array( 'name' => '大井町駅', 'lat' => 35.6067, 'lng' => 139.7349, 'lines' => 'JR京浜東北線・東急大井町線・りんかい線' ),
            array( 'name' => '五反田駅', 'lat' => 35.6262, 'lng' => 139.7234, 'lines' => 'JR山手線・東急池上線・都営浅草線' ),
            array( 'name' => '目黒駅', 'lat' => 35.6340, 'lng' => 139.7158, 'lines' => 'JR山手線・南北線・都営三田線' ),
        ),
        'area-13-目黒区' => array(
            array( 'name' => '中目黒駅', 'lat' => 35.6444, 'lng' => 139.6987, 'lines' => '日比谷線・東急東横線' ),
            array( 'name' => '自由が丘駅', 'lat' => 35.6077, 'lng' => 139.6698, 'lines' => '東急東横線・大井町線' ),
            array( 'name' => '学芸大学駅', 'lat' => 35.6289, 'lng' => 139.6854, 'lines' => '東急東横線' ),
        ),
        'area-13-大田区' => array(
            array( 'name' => '蒲田駅', 'lat' => 35.5627, 'lng' => 139.7160, 'lines' => 'JR京浜東北線・東急池上線・多摩川線' ),
            array( 'name' => '大森駅', 'lat' => 35.5884, 'lng' => 139.7276, 'lines' => 'JR京浜東北線' ),
        ),
        'area-13-世田谷区' => array(
            array( 'name' => '三軒茶屋駅', 'lat' => 35.6436, 'lng' => 139.6705, 'lines' => '東急田園都市線・世田谷線' ),
            array( 'name' => '下北沢駅', 'lat' => 35.6611, 'lng' => 139.6671, 'lines' => '小田急線・京王井の頭線' ),
            array( 'name' => '二子玉川駅', 'lat' => 35.6115, 'lng' => 139.6269, 'lines' => '東急田園都市線・大井町線' ),
            array( 'name' => '成城学園前駅', 'lat' => 35.6410, 'lng' => 139.5996, 'lines' => '小田急線' ),
        ),
        'area-13-渋谷区' => array(
            array( 'name' => '渋谷駅', 'lat' => 35.6580, 'lng' => 139.7016, 'lines' => 'JR各線・東急・京王・銀座線・半蔵門線・副都心線' ),
            array( 'name' => '恵比寿駅', 'lat' => 35.6467, 'lng' => 139.7100, 'lines' => 'JR山手線・埼京線・日比谷線' ),
            array( 'name' => '原宿駅', 'lat' => 35.6702, 'lng' => 139.7027, 'lines' => 'JR山手線' ),
            array( 'name' => '代官山駅', 'lat' => 35.6488, 'lng' => 139.7034, 'lines' => '東急東横線' ),
            array( 'name' => '神泉駅', 'lat' => 35.6562, 'lng' => 139.6942, 'lines' => '京王井の頭線' ),
        ),
        'area-13-中野区' => array(
            array( 'name' => '中野駅', 'lat' => 35.7072, 'lng' => 139.6654, 'lines' => 'JR中央線・東西線' ),
        ),
        'area-13-杉並区' => array(
            array( 'name' => '荻窪駅', 'lat' => 35.7040, 'lng' => 139.6200, 'lines' => 'JR中央線・丸ノ内線' ),
            array( 'name' => '高円寺駅', 'lat' => 35.7054, 'lng' => 139.6496, 'lines' => 'JR中央線' ),
            array( 'name' => '阿佐ヶ谷駅', 'lat' => 35.7049, 'lng' => 139.6354, 'lines' => 'JR中央線' ),
        ),
        'area-13-豊島区' => array(
            array( 'name' => '池袋駅', 'lat' => 35.7295, 'lng' => 139.7109, 'lines' => 'JR各線・東武東上線・西武池袋線・丸ノ内線・有楽町線・副都心線' ),
            array( 'name' => '大塚駅', 'lat' => 35.7315, 'lng' => 139.7283, 'lines' => 'JR山手線' ),
            array( 'name' => '巣鴨駅', 'lat' => 35.7334, 'lng' => 139.7394, 'lines' => 'JR山手線・都営三田線' ),
        ),
        'area-13-北区' => array(
            array( 'name' => '赤羽駅', 'lat' => 35.7779, 'lng' => 139.7210, 'lines' => 'JR各線' ),
            array( 'name' => '王子駅', 'lat' => 35.7529, 'lng' => 139.7379, 'lines' => 'JR京浜東北線・南北線' ),
            array( 'name' => '十条駅', 'lat' => 35.7638, 'lng' => 139.7187, 'lines' => 'JR埼京線' ),
        ),
        'area-13-足立区' => array(
            array( 'name' => '北千住駅', 'lat' => 35.7494, 'lng' => 139.8048, 'lines' => 'JR常磐線・東武・日比谷線・千代田線・TX' ),
            array( 'name' => '西新井駅', 'lat' => 35.7754, 'lng' => 139.7876, 'lines' => '東武スカイツリーライン' ),
        ),
        'area-13-八王子市' => array(
            array( 'name' => '八王子駅', 'lat' => 35.6558, 'lng' => 139.3389, 'lines' => 'JR中央線・横浜線・八高線' ),
        ),
        'area-13-立川市' => array(
            array( 'name' => '立川駅', 'lat' => 35.6981, 'lng' => 139.4139, 'lines' => 'JR中央線・南武線・青梅線' ),
        ),
        'area-13-武蔵野市' => array(
            array( 'name' => '吉祥寺駅', 'lat' => 35.7030, 'lng' => 139.5796, 'lines' => 'JR中央線・京王井の頭線' ),
        ),
        'area-13-町田市' => array(
            array( 'name' => '町田駅', 'lat' => 35.5423, 'lng' => 139.4456, 'lines' => 'JR横浜線・小田急線' ),
        ),

        // ===== 神奈川県 =====
        'area-14-横浜市' => array(
            array( 'name' => '横浜駅', 'lat' => 35.4660, 'lng' => 139.6226, 'lines' => 'JR各線・京急線・東急東横線・相鉄線' ),
            array( 'name' => '関内駅', 'lat' => 35.4437, 'lng' => 139.6362, 'lines' => 'JR根岸線・横浜市営地下鉄' ),
            array( 'name' => '桜木町駅', 'lat' => 35.4510, 'lng' => 139.6310, 'lines' => 'JR根岸線・横浜市営地下鉄' ),
            array( 'name' => '新横浜駅', 'lat' => 35.5073, 'lng' => 139.6172, 'lines' => 'JR横浜線・新幹線・地下鉄' ),
            array( 'name' => '中華街駅', 'lat' => 35.4424, 'lng' => 139.6471, 'lines' => 'みなとみらい線' ),
        ),
        'area-14-川崎市' => array(
            array( 'name' => '川崎駅', 'lat' => 35.5309, 'lng' => 139.6968, 'lines' => 'JR各線' ),
            array( 'name' => '武蔵小杉駅', 'lat' => 35.5764, 'lng' => 139.6596, 'lines' => 'JR各線・東急東横線・目黒線' ),
            array( 'name' => '溝の口駅', 'lat' => 35.6004, 'lng' => 139.6103, 'lines' => '東急田園都市線・JR南武線' ),
        ),
        'area-14-相模原市' => array(
            array( 'name' => '相模大野駅', 'lat' => 35.5305, 'lng' => 139.4369, 'lines' => '小田急線' ),
            array( 'name' => '橋本駅', 'lat' => 35.5948, 'lng' => 139.3463, 'lines' => 'JR横浜線・相模線・京王相模原線' ),
        ),
        'area-14-藤沢市' => array(
            array( 'name' => '藤沢駅', 'lat' => 35.3384, 'lng' => 139.4876, 'lines' => 'JR東海道線・小田急線・江ノ電' ),
        ),
        'area-14-鎌倉市' => array(
            array( 'name' => '鎌倉駅', 'lat' => 35.3191, 'lng' => 139.5504, 'lines' => 'JR横須賀線・江ノ電' ),
        ),

        // ===== 愛知県 =====
        'area-23-名古屋市' => array(
            array( 'name' => '名古屋駅', 'lat' => 35.1709, 'lng' => 136.8815, 'lines' => 'JR各線・名鉄・近鉄・地下鉄東山線・桜通線' ),
            array( 'name' => '栄駅', 'lat' => 35.1688, 'lng' => 136.9068, 'lines' => '地下鉄東山線・名城線' ),
            array( 'name' => '金山駅', 'lat' => 35.1458, 'lng' => 136.9005, 'lines' => 'JR各線・名鉄・地下鉄名城線・名港線' ),
            array( 'name' => '伏見駅', 'lat' => 35.1696, 'lng' => 136.8960, 'lines' => '地下鉄東山線・鶴舞線' ),
            array( 'name' => '大須観音駅', 'lat' => 35.1607, 'lng' => 136.8968, 'lines' => '地下鉄鶴舞線' ),
        ),

        // ===== 京都府 =====
        'area-26-京都市' => array(
            array( 'name' => '京都駅', 'lat' => 34.9858, 'lng' => 135.7588, 'lines' => 'JR各線・近鉄・地下鉄烏丸線' ),
            array( 'name' => '河原町駅', 'lat' => 35.0032, 'lng' => 135.7696, 'lines' => '阪急京都線' ),
            array( 'name' => '烏丸駅', 'lat' => 35.0040, 'lng' => 135.7598, 'lines' => '阪急京都線' ),
            array( 'name' => '三条駅', 'lat' => 35.0087, 'lng' => 135.7708, 'lines' => '京阪本線' ),
            array( 'name' => '祇園四条駅', 'lat' => 35.0038, 'lng' => 135.7712, 'lines' => '京阪本線' ),
            array( 'name' => '北大路駅', 'lat' => 35.0444, 'lng' => 135.7576, 'lines' => '地下鉄烏丸線' ),
        ),

        // ===== 大阪府 =====
        'area-27-大阪市' => array(
            array( 'name' => '梅田駅', 'lat' => 34.7048, 'lng' => 135.4987, 'lines' => '御堂筋線・阪急線・阪神線' ),
            array( 'name' => '難波駅', 'lat' => 34.6629, 'lng' => 135.5013, 'lines' => '御堂筋線・南海線・近鉄線' ),
            array( 'name' => '心斎橋駅', 'lat' => 34.6745, 'lng' => 135.5009, 'lines' => '御堂筋線・長堀鶴見緑地線' ),
            array( 'name' => '天王寺駅', 'lat' => 34.6462, 'lng' => 135.5131, 'lines' => 'JR各線・御堂筋線・谷町線' ),
            array( 'name' => '新大阪駅', 'lat' => 34.7334, 'lng' => 135.5001, 'lines' => 'JR各線・御堂筋線' ),
            array( 'name' => '天満駅', 'lat' => 34.7047, 'lng' => 135.5115, 'lines' => 'JR大阪環状線' ),
            array( 'name' => '鶴橋駅', 'lat' => 34.6674, 'lng' => 135.5299, 'lines' => 'JR大阪環状線・近鉄線・千日前線' ),
        ),
        'area-27-堺市' => array(
            array( 'name' => '堺駅', 'lat' => 34.5763, 'lng' => 135.4607, 'lines' => '南海本線' ),
            array( 'name' => '堺東駅', 'lat' => 34.5716, 'lng' => 135.4838, 'lines' => '南海高野線' ),
        ),
        'area-27-東大阪市' => array(
            array( 'name' => '布施駅', 'lat' => 34.6660, 'lng' => 135.5611, 'lines' => '近鉄大阪線・奈良線' ),
        ),
        'area-27-豊中市' => array(
            array( 'name' => '豊中駅', 'lat' => 34.7879, 'lng' => 135.4698, 'lines' => '阪急宝塚線' ),
        ),
        'area-27-枚方市' => array(
            array( 'name' => '枚方市駅', 'lat' => 34.8112, 'lng' => 135.6501, 'lines' => '京阪本線・交野線' ),
        ),

        // ===== 兵庫県 =====
        'area-28-神戸市' => array(
            array( 'name' => '三宮駅', 'lat' => 34.6951, 'lng' => 135.1979, 'lines' => 'JR各線・阪急・阪神・地下鉄・ポートライナー' ),
            array( 'name' => '元町駅', 'lat' => 34.6906, 'lng' => 135.1897, 'lines' => 'JR各線・阪神線' ),
            array( 'name' => '神戸駅', 'lat' => 34.6809, 'lng' => 135.1734, 'lines' => 'JR各線' ),
            array( 'name' => '新神戸駅', 'lat' => 34.7030, 'lng' => 135.1945, 'lines' => '新幹線・地下鉄西神山手線' ),
        ),
        'area-28-姫路市' => array(
            array( 'name' => '姫路駅', 'lat' => 34.8264, 'lng' => 134.6913, 'lines' => 'JR各線・山陽電鉄' ),
        ),
        'area-28-尼崎市' => array(
            array( 'name' => '尼崎駅', 'lat' => 34.7335, 'lng' => 135.4275, 'lines' => 'JR各線' ),
        ),
        'area-28-西宮市' => array(
            array( 'name' => '西宮北口駅', 'lat' => 34.7434, 'lng' => 135.3563, 'lines' => '阪急神戸線・今津線' ),
        ),

        // ===== 福岡県 =====
        'area-40-福岡市' => array(
            array( 'name' => '博多駅', 'lat' => 33.5898, 'lng' => 130.4207, 'lines' => 'JR各線・新幹線・地下鉄空港線' ),
            array( 'name' => '天神駅', 'lat' => 33.5916, 'lng' => 130.3986, 'lines' => '地下鉄空港線・西鉄天神大牟田線' ),
            array( 'name' => '中洲川端駅', 'lat' => 33.5940, 'lng' => 130.4072, 'lines' => '地下鉄空港線・箱崎線' ),
            array( 'name' => '西新駅', 'lat' => 33.5859, 'lng' => 130.3604, 'lines' => '地下鉄空港線' ),
        ),
        'area-40-北九州市' => array(
            array( 'name' => '小倉駅', 'lat' => 33.8866, 'lng' => 130.8825, 'lines' => 'JR各線・新幹線・北九州モノレール' ),
        ),

        // ===== 宮城県 =====
        'area-04-仙台市' => array(
            array( 'name' => '仙台駅', 'lat' => 38.2601, 'lng' => 140.8821, 'lines' => 'JR各線・新幹線・地下鉄南北線・東西線' ),
            array( 'name' => '勾当台公園駅', 'lat' => 38.2680, 'lng' => 140.8712, 'lines' => '地下鉄南北線' ),
        ),

        // ===== 広島県 =====
        'area-34-広島市' => array(
            array( 'name' => '広島駅', 'lat' => 34.3984, 'lng' => 132.4753, 'lines' => 'JR各線・新幹線・広島電鉄' ),
            array( 'name' => '紙屋町駅', 'lat' => 34.3930, 'lng' => 132.4570, 'lines' => '広島電鉄・アストラムライン' ),
        ),

        // ===== 青森県 =====
        'area-02-青森市' => array(
            array( 'name' => '青森駅', 'lat' => 40.8282, 'lng' => 140.7382, 'lines' => 'JR奥羽本線・津軽線' ),
        ),
        'area-02-弘前市' => array(
            array( 'name' => '弘前駅', 'lat' => 40.5950, 'lng' => 140.4878, 'lines' => 'JR奥羽本線・弘南鉄道' ),
        ),
        'area-02-八戸市' => array(
            array( 'name' => '八戸駅', 'lat' => 40.5125, 'lng' => 141.4889, 'lines' => 'JR東北新幹線・八戸線' ),
            array( 'name' => '本八戸駅', 'lat' => 40.5121, 'lng' => 141.5000, 'lines' => 'JR八戸線' ),
        ),

        // ===== 岩手県 =====
        'area-03-盛岡市' => array(
            array( 'name' => '盛岡駅', 'lat' => 39.7013, 'lng' => 141.1364, 'lines' => 'JR各線・新幹線・IGRいわて銀河鉄道' ),
        ),

        // ===== 秋田県 =====
        'area-05-秋田市' => array(
            array( 'name' => '秋田駅', 'lat' => 39.7186, 'lng' => 140.1277, 'lines' => 'JR各線・新幹線' ),
        ),

        // ===== 山形県 =====
        'area-06-山形市' => array(
            array( 'name' => '山形駅', 'lat' => 38.2481, 'lng' => 140.3282, 'lines' => 'JR各線・新幹線' ),
        ),

        // ===== 福島県 =====
        'area-07-福島市' => array(
            array( 'name' => '福島駅', 'lat' => 37.7543, 'lng' => 140.4597, 'lines' => 'JR各線・新幹線・阿武隈急行・福島交通' ),
        ),
        'area-07-郡山市' => array(
            array( 'name' => '郡山駅', 'lat' => 37.3948, 'lng' => 140.3880, 'lines' => 'JR各線・新幹線' ),
        ),
        'area-07-いわき市' => array(
            array( 'name' => 'いわき駅', 'lat' => 37.0513, 'lng' => 140.8878, 'lines' => 'JR常磐線・磐越東線' ),
        ),

        // ===== 茨城県 =====
        'area-08-水戸市' => array(
            array( 'name' => '水戸駅', 'lat' => 36.3707, 'lng' => 140.4764, 'lines' => 'JR常磐線・水郡線・鹿島臨海鉄道' ),
        ),
        'area-08-つくば市' => array(
            array( 'name' => 'つくば駅', 'lat' => 36.0826, 'lng' => 140.1116, 'lines' => 'つくばエクスプレス' ),
        ),

        // ===== 栃木県 =====
        'area-09-宇都宮市' => array(
            array( 'name' => '宇都宮駅', 'lat' => 36.5594, 'lng' => 139.8982, 'lines' => 'JR各線・新幹線' ),
        ),

        // ===== 群馬県 =====
        'area-10-前橋市' => array(
            array( 'name' => '前橋駅', 'lat' => 36.3823, 'lng' => 139.0714, 'lines' => 'JR両毛線' ),
        ),
        'area-10-高崎市' => array(
            array( 'name' => '高崎駅', 'lat' => 36.3222, 'lng' => 139.0127, 'lines' => 'JR各線・新幹線' ),
        ),

        // ===== 神奈川県（追加分） =====
        'area-14-藤沢市' => array(
            array( 'name' => '藤沢駅', 'lat' => 35.3388, 'lng' => 139.4872, 'lines' => 'JR東海道線・小田急線・江ノ電' ),
        ),
        'area-14-横須賀市' => array(
            array( 'name' => '横須賀中央駅', 'lat' => 35.2790, 'lng' => 139.6700, 'lines' => '京急本線' ),
        ),
        'area-14-相模原市' => array(
            array( 'name' => '相模大野駅', 'lat' => 35.5309, 'lng' => 139.4370, 'lines' => '小田急線' ),
            array( 'name' => '橋本駅', 'lat' => 35.5946, 'lng' => 139.3445, 'lines' => 'JR横浜線・相模線・京王相模原線' ),
        ),
        'area-14-平塚市' => array(
            array( 'name' => '平塚駅', 'lat' => 35.3290, 'lng' => 139.3500, 'lines' => 'JR東海道線' ),
        ),
        'area-14-厚木市' => array(
            array( 'name' => '本厚木駅', 'lat' => 35.4385, 'lng' => 139.3649, 'lines' => '小田急線' ),
        ),
        'area-14-小田原市' => array(
            array( 'name' => '小田原駅', 'lat' => 35.2564, 'lng' => 139.1544, 'lines' => 'JR各線・新幹線・小田急線・箱根登山鉄道' ),
        ),

        // ===== 新潟県 =====
        'area-15-新潟市' => array(
            array( 'name' => '新潟駅', 'lat' => 37.9106, 'lng' => 139.0628, 'lines' => 'JR各線・新幹線' ),
        ),
        'area-15-長岡市' => array(
            array( 'name' => '長岡駅', 'lat' => 37.4494, 'lng' => 138.8530, 'lines' => 'JR各線・新幹線' ),
        ),

        // ===== 富山県 =====
        'area-16-富山市' => array(
            array( 'name' => '富山駅', 'lat' => 36.7013, 'lng' => 137.2137, 'lines' => 'JR各線・新幹線・あいの風とやま鉄道・富山地方鉄道' ),
        ),

        // ===== 石川県 =====
        'area-17-金沢市' => array(
            array( 'name' => '金沢駅', 'lat' => 36.5782, 'lng' => 136.6483, 'lines' => 'JR各線・新幹線・IRいしかわ鉄道' ),
            array( 'name' => '片町', 'lat' => 36.5626, 'lng' => 136.6562, 'lines' => '繁華街エリア' ),
        ),

        // ===== 福井県 =====
        'area-18-福井市' => array(
            array( 'name' => '福井駅', 'lat' => 36.0625, 'lng' => 136.2234, 'lines' => 'JR各線・新幹線・えちぜん鉄道・福井鉄道' ),
        ),

        // ===== 山梨県 =====
        'area-19-甲府市' => array(
            array( 'name' => '甲府駅', 'lat' => 35.6677, 'lng' => 138.5688, 'lines' => 'JR中央本線・身延線' ),
        ),

        // ===== 長野県 =====
        'area-20-長野市' => array(
            array( 'name' => '長野駅', 'lat' => 36.6432, 'lng' => 138.1889, 'lines' => 'JR各線・新幹線・しなの鉄道・長野電鉄' ),
        ),
        'area-20-松本市' => array(
            array( 'name' => '松本駅', 'lat' => 36.2311, 'lng' => 137.9688, 'lines' => 'JR各線・アルピコ交通' ),
        ),

        // ===== 岐阜県 =====
        'area-21-岐阜市' => array(
            array( 'name' => '岐阜駅', 'lat' => 35.4099, 'lng' => 136.7589, 'lines' => 'JR各線・名鉄' ),
        ),

        // ===== 静岡県 =====
        'area-22-静岡市' => array(
            array( 'name' => '静岡駅', 'lat' => 34.9718, 'lng' => 138.3890, 'lines' => 'JR各線・新幹線・静岡鉄道' ),
        ),
        'area-22-浜松市' => array(
            array( 'name' => '浜松駅', 'lat' => 34.7040, 'lng' => 137.7352, 'lines' => 'JR各線・新幹線・遠州鉄道' ),
        ),
        'area-22-沼津市' => array(
            array( 'name' => '沼津駅', 'lat' => 35.0960, 'lng' => 138.8638, 'lines' => 'JR各線' ),
        ),

        // ===== 愛知県 =====
        'area-23-名古屋市' => array(
            array( 'name' => '名古屋駅', 'lat' => 35.1709, 'lng' => 136.8815, 'lines' => 'JR各線・新幹線・名鉄・近鉄・地下鉄東山線・桜通線' ),
            array( 'name' => '栄駅', 'lat' => 35.1685, 'lng' => 136.9087, 'lines' => '地下鉄東山線・名城線' ),
            array( 'name' => '金山駅', 'lat' => 35.1461, 'lng' => 136.9001, 'lines' => 'JR各線・名鉄・地下鉄名城線・名港線' ),
            array( 'name' => '大須観音駅', 'lat' => 35.1583, 'lng' => 136.8972, 'lines' => '地下鉄鶴舞線' ),
            array( 'name' => '今池駅', 'lat' => 35.1728, 'lng' => 136.9337, 'lines' => '地下鉄東山線・桜通線' ),
        ),
        'area-23-豊田市' => array(
            array( 'name' => '豊田市駅', 'lat' => 35.0830, 'lng' => 137.1565, 'lines' => '名鉄三河線・愛知環状鉄道' ),
        ),
        'area-23-豊橋市' => array(
            array( 'name' => '豊橋駅', 'lat' => 34.7635, 'lng' => 137.3825, 'lines' => 'JR各線・新幹線・名鉄・豊橋鉄道' ),
        ),
        'area-23-岡崎市' => array(
            array( 'name' => '岡崎駅', 'lat' => 34.9262, 'lng' => 137.1704, 'lines' => 'JR東海道線・愛知環状鉄道' ),
        ),

        // ===== 三重県 =====
        'area-24-津市' => array(
            array( 'name' => '津駅', 'lat' => 34.7330, 'lng' => 136.5096, 'lines' => 'JR紀勢本線・近鉄名古屋線' ),
        ),
        'area-24-四日市市' => array(
            array( 'name' => '近鉄四日市駅', 'lat' => 34.9659, 'lng' => 136.6247, 'lines' => '近鉄各線・あすなろう鉄道' ),
        ),

        // ===== 滋賀県 =====
        'area-25-大津市' => array(
            array( 'name' => '大津駅', 'lat' => 35.0028, 'lng' => 135.8587, 'lines' => 'JR東海道線' ),
        ),
        'area-25-草津市' => array(
            array( 'name' => '草津駅', 'lat' => 35.0166, 'lng' => 135.9606, 'lines' => 'JR東海道線・草津線' ),
        ),

        // ===== 京都府 =====
        'area-26-京都市' => array(
            array( 'name' => '京都駅', 'lat' => 34.9858, 'lng' => 135.7588, 'lines' => 'JR各線・新幹線・近鉄・地下鉄烏丸線' ),
            array( 'name' => '河原町駅', 'lat' => 35.0037, 'lng' => 135.7695, 'lines' => '阪急京都線' ),
            array( 'name' => '烏丸駅', 'lat' => 35.0034, 'lng' => 135.7595, 'lines' => '阪急京都線' ),
            array( 'name' => '烏丸御池駅', 'lat' => 35.0097, 'lng' => 135.7620, 'lines' => '地下鉄烏丸線・東西線' ),
            array( 'name' => '祇園四条駅', 'lat' => 35.0039, 'lng' => 135.7717, 'lines' => '京阪本線' ),
            array( 'name' => '三条駅', 'lat' => 35.0083, 'lng' => 135.7716, 'lines' => '京阪本線・地下鉄東西線' ),
            array( 'name' => '二条駅', 'lat' => 35.0104, 'lng' => 135.7441, 'lines' => 'JR嵯峨野線・地下鉄東西線' ),
            array( 'name' => '出町柳駅', 'lat' => 35.0305, 'lng' => 135.7736, 'lines' => '京阪鴨東線・叡山電鉄' ),
        ),
        'area-26-宇治市' => array(
            array( 'name' => '宇治駅', 'lat' => 34.8884, 'lng' => 135.7998, 'lines' => 'JR奈良線' ),
        ),

        // ===== 大阪府（追加分） =====
        'area-27-堺市' => array(
            array( 'name' => '堺駅', 'lat' => 34.5760, 'lng' => 135.4636, 'lines' => '南海本線' ),
            array( 'name' => '堺東駅', 'lat' => 34.5729, 'lng' => 135.4844, 'lines' => '南海高野線' ),
            array( 'name' => '中百舌鳥駅', 'lat' => 34.5496, 'lng' => 135.5034, 'lines' => '南海高野線・地下鉄御堂筋線・泉北高速鉄道' ),
        ),
        'area-27-東大阪市' => array(
            array( 'name' => '布施駅', 'lat' => 34.6673, 'lng' => 135.5587, 'lines' => '近鉄大阪線・奈良線' ),
        ),
        'area-27-豊中市' => array(
            array( 'name' => '豊中駅', 'lat' => 34.7840, 'lng' => 135.4699, 'lines' => '阪急宝塚線' ),
        ),
        'area-27-枚方市' => array(
            array( 'name' => '枚方市駅', 'lat' => 34.8144, 'lng' => 135.6505, 'lines' => '京阪本線・交野線' ),
        ),
        'area-27-吹田市' => array(
            array( 'name' => '江坂駅', 'lat' => 34.7650, 'lng' => 135.4932, 'lines' => '地下鉄御堂筋線・北大阪急行' ),
        ),
        'area-27-高槻市' => array(
            array( 'name' => '高槻駅', 'lat' => 34.8468, 'lng' => 135.6175, 'lines' => 'JR東海道線' ),
            array( 'name' => '高槻市駅', 'lat' => 34.8504, 'lng' => 135.6188, 'lines' => '阪急京都線' ),
        ),

        // ===== 兵庫県 =====
        'area-28-神戸市' => array(
            array( 'name' => '三宮駅', 'lat' => 34.6943, 'lng' => 135.1975, 'lines' => 'JR各線・阪急・阪神・地下鉄・ポートライナー' ),
            array( 'name' => '元町駅', 'lat' => 34.6900, 'lng' => 135.1888, 'lines' => 'JR各線・阪神' ),
            array( 'name' => '新神戸駅', 'lat' => 34.7019, 'lng' => 135.1978, 'lines' => '新幹線・地下鉄西神山手線' ),
            array( 'name' => '神戸駅', 'lat' => 34.6793, 'lng' => 135.1779, 'lines' => 'JR各線' ),
            array( 'name' => 'ハーバーランド駅', 'lat' => 34.6768, 'lng' => 135.1790, 'lines' => '地下鉄海岸線' ),
        ),
        'area-28-姫路市' => array(
            array( 'name' => '姫路駅', 'lat' => 34.8263, 'lng' => 134.6917, 'lines' => 'JR各線・新幹線・山陽電鉄' ),
        ),
        'area-28-尼崎市' => array(
            array( 'name' => '尼崎駅', 'lat' => 34.7334, 'lng' => 135.4284, 'lines' => 'JR各線' ),
        ),
        'area-28-西宮市' => array(
            array( 'name' => '西宮北口駅', 'lat' => 34.7448, 'lng' => 135.3612, 'lines' => '阪急神戸線・今津線' ),
        ),
        'area-28-明石市' => array(
            array( 'name' => '明石駅', 'lat' => 34.6470, 'lng' => 134.9876, 'lines' => 'JR各線・山陽電鉄' ),
        ),

        // ===== 奈良県 =====
        'area-29-奈良市' => array(
            array( 'name' => '近鉄奈良駅', 'lat' => 34.6815, 'lng' => 135.8302, 'lines' => '近鉄奈良線' ),
            array( 'name' => 'JR奈良駅', 'lat' => 34.6775, 'lng' => 135.8204, 'lines' => 'JR各線' ),
        ),

        // ===== 和歌山県 =====
        'area-30-和歌山市' => array(
            array( 'name' => '和歌山駅', 'lat' => 34.2326, 'lng' => 135.1903, 'lines' => 'JR各線・和歌山電鉄' ),
        ),

        // ===== 岡山県 =====
        'area-33-岡山市' => array(
            array( 'name' => '岡山駅', 'lat' => 34.6655, 'lng' => 133.9185, 'lines' => 'JR各線・新幹線・岡山電気軌道' ),
        ),
        'area-33-倉敷市' => array(
            array( 'name' => '倉敷駅', 'lat' => 34.5998, 'lng' => 133.7713, 'lines' => 'JR各線' ),
        ),

        // ===== 山口県 =====
        'area-35-下関市' => array(
            array( 'name' => '下関駅', 'lat' => 33.9508, 'lng' => 130.9218, 'lines' => 'JR各線' ),
        ),

        // ===== 香川県 =====
        'area-37-高松市' => array(
            array( 'name' => '高松駅', 'lat' => 34.3499, 'lng' => 134.0467, 'lines' => 'JR各線・ことでん' ),
        ),

        // ===== 愛媛県 =====
        'area-38-松山市' => array(
            array( 'name' => '松山駅', 'lat' => 33.8426, 'lng' => 132.7589, 'lines' => 'JR予讃線' ),
            array( 'name' => '松山市駅', 'lat' => 33.8386, 'lng' => 132.7645, 'lines' => '伊予鉄道' ),
            array( 'name' => '大街道駅', 'lat' => 33.8426, 'lng' => 132.7693, 'lines' => '伊予鉄道市内線' ),
        ),

        // ===== 高知県 =====
        'area-39-高知市' => array(
            array( 'name' => '高知駅', 'lat' => 33.5676, 'lng' => 133.5433, 'lines' => 'JR各線・とさでん交通' ),
        ),

        // ===== 福岡県（追加分） =====
        'area-40-久留米市' => array(
            array( 'name' => '久留米駅', 'lat' => 33.3171, 'lng' => 130.5122, 'lines' => 'JR各線・新幹線' ),
            array( 'name' => '西鉄久留米駅', 'lat' => 33.3159, 'lng' => 130.5213, 'lines' => '西鉄天神大牟田線' ),
        ),

        // ===== 佐賀県 =====
        'area-41-佐賀市' => array(
            array( 'name' => '佐賀駅', 'lat' => 33.2634, 'lng' => 130.3020, 'lines' => 'JR各線' ),
        ),

        // ===== 長崎県 =====
        'area-42-長崎市' => array(
            array( 'name' => '長崎駅', 'lat' => 32.7514, 'lng' => 129.8695, 'lines' => 'JR各線・新幹線・長崎電気軌道' ),
        ),

        // ===== 熊本県 =====
        'area-43-熊本市' => array(
            array( 'name' => '熊本駅', 'lat' => 32.7910, 'lng' => 130.6882, 'lines' => 'JR各線・新幹線・熊本市電' ),
            array( 'name' => '通町筋駅', 'lat' => 32.8032, 'lng' => 130.7082, 'lines' => '熊本市電（繁華街）' ),
        ),

        // ===== 大分県 =====
        'area-44-大分市' => array(
            array( 'name' => '大分駅', 'lat' => 33.2326, 'lng' => 131.6070, 'lines' => 'JR各線' ),
        ),
        'area-44-別府市' => array(
            array( 'name' => '別府駅', 'lat' => 33.2787, 'lng' => 131.5001, 'lines' => 'JR日豊本線' ),
        ),

        // ===== 宮崎県 =====
        'area-45-宮崎市' => array(
            array( 'name' => '宮崎駅', 'lat' => 31.9160, 'lng' => 131.4253, 'lines' => 'JR各線' ),
        ),

        // ===== 鹿児島県 =====
        'area-46-鹿児島市' => array(
            array( 'name' => '鹿児島中央駅', 'lat' => 31.5839, 'lng' => 130.5419, 'lines' => 'JR各線・新幹線・鹿児島市電' ),
            array( 'name' => '天文館通駅', 'lat' => 31.5888, 'lng' => 130.5542, 'lines' => '鹿児島市電（繁華街）' ),
        ),

        // ===== 沖縄県 =====
        'area-47-那覇市' => array(
            array( 'name' => '県庁前駅', 'lat' => 26.2150, 'lng' => 127.6793, 'lines' => 'ゆいレール' ),
            array( 'name' => '牧志駅', 'lat' => 26.2162, 'lng' => 127.6884, 'lines' => 'ゆいレール' ),
            array( 'name' => 'おもろまち駅', 'lat' => 26.2231, 'lng' => 127.6948, 'lines' => 'ゆいレール' ),
        ),
    );

    return $data;
}

/**
 * カテゴリIDから駅一覧を取得（slugベースで検索）
 */
function knt_get_stations_by_category( $category_id ) {
    // まず旧IDベースデータを確認（後方互換）
    if ( defined( 'KNT_STATION_DATA' ) && isset( KNT_STATION_DATA[ $category_id ] ) ) {
        return KNT_STATION_DATA[ $category_id ];
    }
    // slugベースで検索
    $cat = get_category( $category_id );
    if ( ! $cat ) return array();

    $all = knt_get_all_station_data();

    // 直接slug一致
    if ( isset( $all[ $cat->slug ] ) ) {
        return $all[ $cat->slug ];
    }

    // area-XX-市区町村名 形式で検索
    $parent = $cat->parent ? get_category( $cat->parent ) : null;
    if ( $parent ) {
        $search_key = $parent->slug . '-' . $cat->name;
        if ( isset( $all[ $search_key ] ) ) {
            return $all[ $search_key ];
        }
    }

    return array();
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
