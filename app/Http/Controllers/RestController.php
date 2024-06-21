<?php

namespace App\Http\Controllers;

use App\Models\Master;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @method distance($latitude, $longitude, mixed $lat, mixed $long, string $string)
 */
class RestController extends Controller
{
    /**
     * Summary of showAllMasters
     * @param Request $request
     * @return JsonResponse
     */
    public function showAllMasters(Request $request): JsonResponse
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
            if ($distance<=$max_distance){
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
            }
        }

        return response()->json($need_masters);
    }
}
