<?php

namespace App\Http\Controllers\Api\V1;

use App\DTO\AvailabilityInterval;
use App\Helpers\AddressHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddMasterRequest;
use App\Http\Requests\AddReviewRequest;
use App\Http\Requests\Availability\SetAvailableMasterRequest;
use App\Http\Requests\GetMasterRequest;
use App\Http\Resources\Api\V1\MasterResource;
use App\Http\Resources\Api\V1\ReviewResource;
use App\Http\Resources\Api\V1\UserResource;
use App\Http\Services\Appointment\AppointmentRedisService;
use App\Http\Services\Appointment\AppointmentService;
use App\Http\Services\FcmTokenService;
use App\Http\Services\Master\MasterService;
use App\Http\Services\SmsService;
use App\Http\Services\UserService;
use App\Models\Master;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

class MasterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  GetMasterRequest  $request  The request instance containing validation and authorization logic.
     * @param  MasterService  $masterService  The service instance to handle business logic related to masters.
     * @return AnonymousResourceCollection A collection of resources to be returned as a response.
     */
    public function index(GetMasterRequest $request, MasterService $masterService, FcmTokenService $fcmTokenService, AppointmentRedisService $appointmentRedisService): AnonymousResourceCollection
    {
        $validatedData = $request->validated();
        $lat = $validatedData['lat'];
        $lng = $validatedData['lng'];
        $zoom = $validatedData['zoom'];
        $page = $validatedData['page'] ?? 1;
        $fcmToken = $validatedData['fcm_token'];

        $filters = [
            'name' => $validatedData['name'] ?? null,
            'distance' => $validatedData['distance'] ?? null,
            'service_id' => $validatedData['service_id'] ?? null,
            'rating' => $validatedData['rating'] ?? null,
            'available' => $validatedData['available'] ?? null,
        ];
        $masters = $masterService->getMastersOnDistance($page, $lat, $lng, $zoom, $filters);
        // $masters->appends($filters);
        $fcmTokenService->saveMasterIdsToToken($fcmToken, $masters->pluck('id')->toArray());
        $availabilityMap = $appointmentRedisService->getAvailabilityForMany($masters->pluck('id')->all(), now());
        return MasterResource::collection($masters)->additional([
            'meta' => [
                'availability' => $availabilityMap
            ]
        ]);
    }

    /**
     * Retrieve the master resource by its ID.
     *
     * @param  int  $id  The ID of the master resource to retrieve.
     * @return MasterResource The master resource corresponding to the given ID.
     */
    public function getMaster(int $id): MasterResource
    {
        $master = Master::find($id);

        return new MasterResource($master);
    }

    /**
     * @throws Exception
     */
    public function verifyAndRegister(AddMasterRequest $request, MasterService $masterService, SmsService $smsService, UserService $userService): JsonResponse
    {
        $data = $request->validated();

        if (! $smsService->verifyCode($data['phone'], $data['sms_code'])) {
            return response()->json(['error' => 'Wrong code'], 400);
        }

        $master = $masterService->createOrUpdate($data);

        $user = $userService->createOrUpdateFromMaster($master);

        $token = JWTAuth::claims(['phone' => $user->phone])->fromUser($user);

        return response()->json([
            'master' => new MasterResource($master),
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    public function addReview(AddReviewRequest $request, MasterService $masterService): ReviewResource
    {
        $data = $request->validated();
        $user = JWTAuth::user();
        $data['user_id'] = $user->id;
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
                'last' => $masters->last()->place_id,
            ]]);
    }

    /**
     * Set the master as available.
     */
    public function setAvailable(SetAvailableMasterRequest $request, int $id, AppointmentRedisService $appointmentRedisService): JsonResponse
    {
        $data = $request->validated();

        $interval = new AvailabilityInterval($data['start_time'], $data['duration']);
        $appointmentRedisService->markAsFree($id, $interval->start, $interval->end);

        return response()->json(['message' => 'Master is available']);
    }
}
