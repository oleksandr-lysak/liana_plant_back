<?php
namespace App\Helpers;
class AddressHelper
{
    /**
     * @param $latitude
     * @param $longitude
     * @return string|boolean
     */
    public static function getPlaceId($latitude, $longitude): string|bool
    {
        $url = "https://maps.googleapis.com/maps/api/geocode/json";

        $params = [
            'latlng' => "{$latitude},{$longitude}", // координати
            'key' => env('GOOGLE_API_KEY'), // ваш ключ API Google
        ];

        $query = http_build_query($params);
        $requestUrl = "{$url}?{$query}";

        $response = file_get_contents($requestUrl);

        $response_object = json_decode($response);
        if ($response_object->status=='OK'){
            return $response;
        }else{
            return false;
        }
    }
}
