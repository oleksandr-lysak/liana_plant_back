<?php
namespace App\Http\Controllers;

use App\Http\Requests\VerifyCodeRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\SmsService;
use App\Http\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function verifyCode(VerifyCodeRequest $request, SmsService $smsService, UserService $userService): JsonResponse
    {
        $data = $request->validated();

        // Check if the code is correct
        if (!$smsService->verifyCode($data['phone'], $data['sms_code'])) {
            return response()->json(['error' => 'Wrong code'], 400);
        }

        // Search for the user by phone
        $user = $userService->findUserByPhone($data['phone']);
        if ($user == null) {
            return response()->json(['error' => 'User not found'], 400);
        }

        // Create a token for the user
        $token = $userService->createTokenForUser($user);
        if ($token == null) {
            return response()->json(['error' => 'Token not created'], 500);
        }

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }
}
