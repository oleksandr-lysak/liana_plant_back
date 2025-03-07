<?php

namespace App\Helpers;

class AddressHelper
{
    public static function getPlaceId($latitude, $longitude): string|bool
    {
        $url = 'https://maps.googleapis.com/maps/api/geocode/json';

        $params = [
            'latlng' => "$latitude,$longitude", // координати
            'key' => env('GOOGLE_API_KEY'), // ваш ключ API Google
        ];

        $query = http_build_query($params);
        $requestUrl = "$url?$query";

        $response = file_get_contents($requestUrl);

        $response_object = json_decode($response);
        if ($response_object->status == 'OK') {
            return $response;
        } else {
            return false;
        }
    }

    /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
    /* ::                                                                         : */
    /* ::  This routine calculates the distance between two points (given the     : */
    /* ::  latitude/longitude of those points). It is being used to calculate     : */
    /* ::  the distance between two locations using GeoDataSource(TM) Products    : */
    /* ::                                                                         : */
    /* ::  Definitions:                                                           : */
    /* ::    South latitudes are negative, east longitudes are positive           : */
    /* ::                                                                         : */
    /* ::  Passed to function:                                                    : */
    /* ::    lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees)  : */
    /* ::    lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees)  : */
    /* ::    unit = the unit you desire for results                               : */
    /* ::           where: 'M' is statute miles (default)                         : */
    /* ::                  'K' is kilometers                                      : */
    /* ::                  'N' is nautical miles                                  : */
    /* ::  Worldwide cities and other features databases with latitude longitude  : */
    /* ::  are available at https://www.geodatasource.com                         : */
    /* ::                                                                         : */
    /* ::  For enquiries, please contact sales@geodatasource.com                  : */
    /* ::                                                                         : */
    /* ::  Official Web site: https://www.geodatasource.com                       : */
    /* ::                                                                         : */
    /* ::         GeoDataSource.com (C) All Rights Reserved 2022                  : */
    /* ::                                                                         : */
    /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
    public function distance($lat1, $lon1, $lat2, $lon2, $unit): float
    {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        } else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);

            if ($unit == 'K') {
                return $miles * 1.609344;
            } elseif ($unit == 'N') {
                return $miles * 0.8684;
            } else {
                return $miles;
            }
        }
    }
}
