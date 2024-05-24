<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetSpecialityRequest;
use App\Http\Resources\SpecialityResource;
use App\Http\Services\SpecialityService;
use App\Models\Speciality;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SpecialityController extends Controller
{
    /**
     * @param GetSpecialityRequest $request
     * @param SpecialityService $specialityService
     * @return AnonymousResourceCollection
     */
    public function index(GetSpecialityRequest $request, SpecialityService $specialityService): AnonymousResourceCollection
    {
        $specialities = $specialityService->getSpecialities($request);

        return SpecialityResource::collection($specialities);
    }

    /**
     * @param $id
     * @return SpecialityResource
     */
    public function getSpeciality($id): SpecialityResource
    {
        $speciality = Speciality::find($id);

        return new SpecialityResource($speciality);
    }
}
