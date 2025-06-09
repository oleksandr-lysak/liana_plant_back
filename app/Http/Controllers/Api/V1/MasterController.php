<?php

namespace App\Http\Controllers\Api\V1;

use App\DTO\AvailabilityInterval;
use App\Helpers\AddressHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddMasterRequest;
use App\Http\Requests\AddReviewRequest;
use App\Http\Requests\Availability\SetAvailableMasterRequest;
use App\Http\Requests\GetMasterRequest;
use App\Http\Requests\ImportExternalMasterRequest;
use App\Http\Resources\Api\V1\MasterResource;
use App\Http\Resources\Api\V1\ReviewResource;
use App\Http\Resources\Api\V1\UserResource;
use App\Http\Services\Appointment\AppointmentRedisService;
use App\Http\Services\ClientService;
use App\Http\Services\Master\MasterFetcher;
use App\Http\Services\Master\MasterService;
use App\Http\Services\SmsService;
use App\Http\Services\UserService;
use App\Models\Master;
use Exception;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

class MasterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param GetMasterRequest $request The request instance containing validation and authorization logic.
     * @param MasterFetcher $masterFetcher
     * @return JsonResponse
     */
    public function index(GetMasterRequest $request, MasterFetcher $masterFetcher): JsonResponse
    {
        return $masterFetcher->fetch($request->validated());
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
    public function setAvailable(SetAvailableMasterRequest $request, $id, AppointmentRedisService $appointmentRedisService): JsonResponse
    {
        $data = $request->validated();
        $id = (int) $id;

        $interval = new AvailabilityInterval($data['start_time'], $data['duration']);
        $appointmentRedisService->markAsFree($id, $interval->start, $interval->end);

        return response()->json(['message' => 'Master is available']);
    }

    public function storeFromExternal(int $serviceId, ImportExternalMasterRequest $request, MasterService $masterService, ClientService $clientService) 
    {
        $master = $masterService->importFromExternal($serviceId, $request->validated(), $clientService);
        return new MasterResource($master);
    }
}
