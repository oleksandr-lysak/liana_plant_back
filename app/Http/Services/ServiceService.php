<?php

namespace App\Http\Services;

use App\Models\Master;
use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ServiceService
{
    public function getServices(Request $request): Collection|array|LengthAwarePaginator
    {
        $query = Service::query();

        $page = $request->input('page');

        return $query->paginate(100, ['*'], 'page', $page);

    }

    public function getServicesForMaster(int $masterId): Collection|array|LengthAwarePaginator
    {
        $master = Master::find($masterId);

        return $master->services;
    }
}
