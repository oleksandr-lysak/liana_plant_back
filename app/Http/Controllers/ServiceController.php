<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Http\Services\ServiceService;
use App\Models\Service;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ServiceController extends Controller
{
    /**
     * @param GetServiceRequest $request
     * @param ServiceService $serviceService
     * @return AnonymousResourceCollection
     */
    public function index(GetServiceRequest $request, ServiceService $serviceService): AnonymousResourceCollection
    {
        $services = $serviceService->getServices($request);

        return ServiceResource::collection($services);
    }

    /**
     * @param $id
     * @return ServiceResource
     */
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
