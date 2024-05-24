<?php

namespace App\Http\Services;

use App\Helpers\AddressHelper;
use App\Models\Master;
use App\Models\Speciality;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class MasterService
{
    public function getMastersOnDistance(int $page, float $lat, float $lng, float $zoom)
    {
        $max_distance = 10;
        if ($zoom > 12) {
            $max_distance = 5;
        }

        $query = Master::query();
        $query->select('*');
        $query->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) as distance', [$lat, $lng, $lat]);
        $query->havingRaw('distance <= ?', [$max_distance]);
        $query->with('specialities');
        $query->with('reviews');

        return $query->paginate(100, ['*'], 'page', $page);

    }
}
