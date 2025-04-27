<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetServiceRequest;
use App\Http\Resources\Api\V1\ServiceResource;
use App\Http\Services\ServiceService;
use App\Models\Service;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ServiceController extends Controller
{
    public function index(GetServiceRequest $request, ServiceService $serviceService): AnonymousResourceCollection
    {
        $services = $serviceService->getServices($request);

        return ServiceResource::collection($services);
    }

    public function getService($id): ServiceResource
    {
        $service = Service::find($id);

        return new ServiceResource($service);
    }

    public function getServicesForMaster($masterId, ServiceService $serviceService): AnonymousResourceCollection
    {
        $services = $serviceService->getServicesForMaster($masterId);

        return ServiceResource::collection($services);
    }
}
