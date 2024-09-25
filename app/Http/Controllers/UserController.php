<?php

namespace App\Http\Controllers;

use App\Helpers\AddressHelper;
use App\Http\Requests\AddMasterRequest;
use App\Http\Requests\AddReviewRequest;
use App\Http\Requests\GetMasterRequest;
use App\Http\Requests\VerifyCodeRequest;
use App\Http\Resources\MasterResource;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\UserResource;
use App\Http\Services\MasterService;
use App\Http\Services\SmsService;
use App\Http\Services\UserService;
use App\Models\Client;
use App\Models\Master;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{


    public function verifyCode(VerifyCodeRequest $request, MasterService $masterService, SmsService $smsService, UserService $userService): JsonResponse
    {
        $data = $request->validated();

        if (!$smsService->verifyCode($data['phone'], $data['sms_code'])) {
            return response()->json(['error' => 'Wrong code'], 400);
        }

        $master = Master::where('phone', $data['phone'])->with('user')->first();
        if ($master == null) {
            $client = Client::where('phone', $data['phone'])->with('user')->first();
            if ($client == null) {
                return response()->json(['error' => 'User not found'], 400);
            }
            $user = $client->user;
        }else{
            $user = $master->user;
        }

        try {
            $token = JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token not created '], 500);
        }

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }
}
