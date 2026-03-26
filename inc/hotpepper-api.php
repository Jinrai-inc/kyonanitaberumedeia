<?php
/**
 * ホットペッパーグルメ API 連携クラス
 *
 * @package KNT_Media
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class KNT_HotPepper_API {

    const GOURMET_SEARCH_URL = 'https://webservice.recruit.co.jp/hotpepper/gourmet/v1/';

    private $api_key;

    public function __construct() {
        $this->api_key = get_option( 'knt_hotpepper_api_key', '' );
        if ( empty( $this->api_key ) && defined( 'KNT_HOTPEPPER_API_KEY' ) ) {
            $this->api_key = KNT_HOTPEPPER_API_KEY;
        }
    }

    /**
     * ホットペッパーAPIで有効な検索パラメータ一覧
     */
    const VALID_PARAMS = array(
        'keyword', 'lat', 'lng', 'range', 'genre', 'large_area', 'middle_area',
        'small_area', 'count', 'start', 'order', 'format', 'key', 'type',
        // 設備フィルター（ホットペッパーAPI対応済み）
        'private_room', 'free_drink', 'free_food', 'wifi', 'card',
        'non_smoking', 'parking', 'pet', 'child', 'lunch', 'midnight',
        'charter', 'tatami', 'horigotatsu', 'karaoke', 'band', 'tv',
        'english', 'barrier_free', 'sommelier', 'night_view', 'open_air',
        'show', 'equipment', 'ktai_coupon',
    );

    public function search_shops( $args = array() ) {
        $defaults = array(
            'keyword'     => '',
            'lat'         => '',
            'lng'         => '',
            'range'       => 3,
            'genre'       => '',
            'large_area'  => '',
            'middle_area' => '',
            'small_area'  => '',
            'count'       => 5,
            'start'       => 1,
            'order'       => 4,
            'format'      => 'json',
        );

        $params = wp_parse_args( $args, $defaults );
        $params['key'] = $this->api_key;

        // 空値を除去 + APIに有効なパラメータのみ送信
        $params = array_filter( $params, function( $v ) {
            return $v !== '' && $v !== null && $v !== 0;
        } );
        $params = array_intersect_key( $params, array_flip( self::VALID_PARAMS ) );

        $url = add_query_arg( $params, self::GOURMET_SEARCH_URL );

        $response = wp_remote_get( $url, array(
            'timeout' => 15,
            'headers' => array( 'Accept' => 'application/json' ),
        ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( ! isset( $data['results']['shop'] ) ) {
            return new WP_Error(
                'hotpepper_no_results',
                'ホットペッパーAPIから店舗データを取得できませんでした。',
                $data
            );
        }

        return $this->format_shops( $data['results']['shop'] );
    }

    private function format_shops( $shops ) {
        $formatted = array();

        foreach ( $shops as $shop ) {
            $formatted[] = array(
                'id'            => $shop['id'] ?? '',
                'name'          => $shop['name'] ?? '',
                'name_kana'     => $shop['name_kana'] ?? '',
                'address'       => $shop['address'] ?? '',
                'station'       => $shop['station_name'] ?? '',
                'lat'           => $shop['lat'] ?? '',
                'lng'           => $shop['lng'] ?? '',
                'genre'         => $shop['genre']['name'] ?? '',
                'genre_catch'   => $shop['genre']['catch'] ?? '',
                'sub_genre'     => $shop['sub_genre']['name'] ?? '',
                'budget'        => $shop['budget']['name'] ?? '',
                'budget_avg'    => $shop['budget']['average'] ?? '',
                'catch'         => $shop['catch'] ?? '',
                'open'          => $shop['open'] ?? '',
                'close'         => $shop['close'] ?? '',
                'access'        => $shop['access'] ?? '',
                'hotpepper_url' => $shop['urls']['pc'] ?? '',
                'coupon_url'    => isset( $shop['coupon_urls']['pc'] ) ? $shop['coupon_urls']['pc'] : '',
                'photo_l'       => $shop['photo']['pc']['l'] ?? '',
                'photo_m'       => $shop['photo']['pc']['m'] ?? '',
                'photo_s'       => $shop['photo']['pc']['s'] ?? '',
                'photo_mobile'  => $shop['photo']['mobile']['l'] ?? '',
                'private_room'  => $shop['private_room'] ?? '',
                'free_drink'    => $shop['free_drink'] ?? '',
                'free_food'     => $shop['free_food'] ?? '',
                'wifi'          => $shop['wifi'] ?? '',
                'card'          => $shop['card'] ?? '',
                'non_smoking'   => $shop['non_smoking'] ?? '',
                'parking'       => $shop['parking'] ?? '',
                'pet'           => $shop['pet'] ?? '',
                'child'         => $shop['child'] ?? '',
                'lunch'         => $shop['lunch'] ?? '',
                'midnight'      => $shop['midnight'] ?? '',
                'capacity'      => $shop['capacity'] ?? '',
                'party_capacity'=> $shop['party_capacity'] ?? '',
                'horigotatsu'   => $shop['horigotatsu'] ?? '',
                'tatami'        => $shop['tatami'] ?? '',
                'charter'       => $shop['charter'] ?? '',
                'barrier_free'  => $shop['barrier_free'] ?? '',
                'night_view'    => $shop['night_view'] ?? '',
                'open_air'      => $shop['open_air'] ?? '',
                'english'       => $shop['english'] ?? '',
                'sommelier'     => $shop['sommelier'] ?? '',
                'karaoke'       => $shop['karaoke'] ?? '',
                'cocktail'      => $shop['cocktail'] ?? '',
                'sake'          => $shop['sake'] ?? '',
                'wine'          => $shop['wine'] ?? '',
                'shop_detail_memo' => $shop['shop_detail_memo'] ?? '',
                'specials'      => array_map( function( $sp ) {
                    return array( 'name' => $sp['name'] ?? '', 'title' => $sp['title'] ?? '' );
                }, $shop['special'] ?? array() ),
                'gmap_url'      => sprintf(
                    'https://www.google.com/maps/search/?api=1&query=%s,%s',
                    $shop['lat'] ?? '',
                    $shop['lng'] ?? ''
                ),
                'gmap_embed'    => sprintf(
                    'https://maps.google.com/maps?q=%s&t=&z=16&ie=UTF8&iwloc=&output=embed',
                    urlencode( ( $shop['name'] ?? '' ) . ' ' . ( $shop['address'] ?? '' ) )
                ),
            );
        }

        return $formatted;
    }
}
