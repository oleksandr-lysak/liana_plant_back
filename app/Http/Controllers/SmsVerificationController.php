<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendSmsCodeRequest;
use App\Http\Services\SmsService;
use Illuminate\Http\JsonResponse;

class SmsVerificationController extends Controller
{
    /**
     * @param SendSmsCodeRequest $request
     * @param SmsService $smsService
     * @return JsonResponse
     */
    public function sendCode(SendSmsCodeRequest $request, SmsService $smsService): JsonResponse
    {
        $phone = $request->input('phone');
        $smsService->generateAndSendCode($phone);

        return response()->json([
            'message' => 'The code was sent successfully',
        ], 200);
    }
}
