<?php

namespace App\Http\Controllers;

use App\Models\Master;
use Illuminate\Http\Request;

class RestController extends Controller
{
    public function showAllMasters(Request $request)
    {
        $lat = $request->get('lat');
        $long = $request->get('long');
        $zoom = $request->get('zoom');
        $masters = Master::limit(120)->get();
        $need_masters = [];
        $max_distance = 10;
        if ($zoom > 12){
            $max_distance = 5;
        }
        foreach ($masters as $master) {
            $distance = $this->distance($master->latitude,$master->longitude,$lat,$long,'K');
            //if ($distance<=$max_distance){
                $elem = [];
                $elem['name'] = $master->name;
                $elem['latitude'] = $master->latitude;
                $elem['longitude'] = $master->longitude;
                $elem['description'] = $master->description;
                $elem['address'] = $master->formattedAddress;
                $elem['free'] = $master->free;
                $elem['age'] = $master->age;
                $elem['phone'] = $master->phone;
                $elem['rating'] = $master->rating;
                $elem['id'] = $master->id;
                $elem['mainPhoto'] = $master->image;
                $elem['distance'] = round($distance,2);
                $elem['speciality'] = $master->speciality;
                $need_masters[]=$elem;
            //}
        }

        return response()->json($need_masters);
    }

    /*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
    /*::                                                                         :*/
    /*::  This routine calculates the distance between two points (given the     :*/
    /*::  latitude/longitude of those points). It is being used to calculate     :*/
    /*::  the distance between two locations using GeoDataSource(TM) Products    :*/
    /*::                                                                         :*/
    /*::  Definitions:                                                           :*/
    /*::    South latitudes are negative, east longitudes are positive           :*/
    /*::                                                                         :*/
    /*::  Passed to function:                                                    :*/
    /*::    lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees)  :*/
    /*::    lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees)  :*/
    /*::    unit = the unit you desire for results                               :*/
    /*::           where: 'M' is statute miles (default)                         :*/
    /*::                  'K' is kilometers                                      :*/
    /*::                  'N' is nautical miles                                  :*/
    /*::  Worldwide cities and other features databases with latitude longitude  :*/
    /*::  are available at https://www.geodatasource.com                         :*/
    /*::                                                                         :*/
    /*::  For enquiries, please contact sales@geodatasource.com                  :*/
    /*::                                                                         :*/
    /*::  Official Web site: https://www.geodatasource.com                       :*/
    /*::                                                                         :*/
    /*::         GeoDataSource.com (C) All Rights Reserved 2022                  :*/
    /*::                                                                         :*/
    /*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
    function distance($lat1, $lon1, $lat2, $lon2, $unit) {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        }
        else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);

            if ($unit == "K") {
                return ($miles * 1.609344);
            } else if ($unit == "N") {
                return ($miles * 0.8684);
            } else {
                return $miles;
            }
        }
    }
}
