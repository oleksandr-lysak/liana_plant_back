<?php

namespace App\Http\Controllers;

use App\Helpers\AddressHelper;
use App\Http\Requests\AddMasterRequest;
use App\Http\Requests\AddReviewRequest;
use App\Http\Requests\GetMasterRequest;
use App\Http\Resources\MasterResource;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\UserResource;
use App\Http\Services\MasterService;
use App\Http\Services\SmsService;
use App\Http\Services\UserService;
use App\Models\Master;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class MasterController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param GetMasterRequest $request The request instance containing validation and authorization logic.
     * @param MasterService $masterService The service instance to handle business logic related to masters.
     * @return AnonymousResourceCollection A collection of resources to be returned as a response.
     */
    public function index(GetMasterRequest $request, MasterService $masterService): AnonymousResourceCollection
    {
        $lat = $request->get('lat');
        $lng = $request->get('lng');
        $zoom = $request->get('zoom');

        $masters = $masterService->getMastersOnDistance($request->get('page'), $lat, $lng, $zoom);

        return MasterResource::collection($masters);
    }

    /**
     * Retrieve the master resource by its ID.
     *
     * @param int $id The ID of the master resource to retrieve.
     * @return MasterResource The master resource corresponding to the given ID.
     */
    public function getMaster($id): MasterResource
    {
        $master = Master::find($id);

        return new MasterResource($master);
    }

    public function verifyAndRegister(AddMasterRequest $request, MasterService $masterService, SmsService $smsService, UserService $userService): JsonResponse
    {
        $data = $request->validated();

        if (!$smsService->verifyCode($data['phone'], $data['sms_code'])) {
            return response()->json(['error' => 'Wrong code'], 400);
        }

        $master = $masterService->createOrUpdate($data);

        $user = $userService->createOrUpdateFromMaster($master);

        try {
            $token = JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token not created '], 500);
        }

        return response()->json([
            'master' => new MasterResource($master),
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    public function addReview(AddReviewRequest $request, MasterService $masterService): ReviewResource
    {
        $data = $request->validated();
        $review = $masterService->addReview($data);

        return new ReviewResource($review);
    }

    public function fillPlaceId(): JsonResponse
    {
        $masters = Master::all();
        foreach ($masters as $master) {
            $master->address = AddressHelper::getPlaceId($master->latitude, $master->longitude);
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
