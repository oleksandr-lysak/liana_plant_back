<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendSmsCodeRequest;
use App\Http\Services\SmsService;
use Illuminate\Http\JsonResponse;

class SmsVerificationController extends Controller
{
    public function sendCode(SendSmsCodeRequest $request, SmsService $smsService): JsonResponse
    {
        $phone = $request->input('phone');
        $code = $smsService->generateAndSendCode($phone);

        return response()->json([
            'message' => 'The code was sent successfully, code: '.$code,
        ], 200);
    }
}
