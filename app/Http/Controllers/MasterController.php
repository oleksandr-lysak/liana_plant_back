<?php

namespace App\Http\Controllers;

use App\Helpers\AddressHelper;
use App\Http\Requests\AddMasterRequest;
use App\Http\Requests\GetMasterRequest;
use App\Http\Resources\MasterResource;
use App\Http\Services\MasterService;
use App\Models\Master;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MasterController extends Controller
{

    public function index(GetMasterRequest $request, MasterService $masterService): AnonymousResourceCollection
    {
        $lat = $request->get('lat');
        $lng = $request->get('lng');
        $zoom = $request->get('zoom');

        $masters = $masterService->getMastersOnDistance($request->get('page'), $lat, $lng, $zoom);

        return MasterResource::collection($masters);
    }

    public function getMaster($id): MasterResource
    {
        $master = Master::find($id);

        return new MasterResource($master);
    }

    public function addMaster(AddMasterRequest $request): MasterResource
    {
        dd($request);

        return new MasterResource($master);
    }

    public function fillPlaceId(): JsonResponse
    {
        $masters = Master::all();
        foreach ($masters as $master) {
            $master->place_id = AddressHelper::getPlaceId($master->latitude, $master->longitude);
            $master->save();
        }

        return response()->json(['message' => 'Place id filled',
            'masters' => [
                'count' => $masters->count(),
                'first' => $masters->first()->place_id,
                'last' => $masters->last()->place_id
            ]]);
    }
}
