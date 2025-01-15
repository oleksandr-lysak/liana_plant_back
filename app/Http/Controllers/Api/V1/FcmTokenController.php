<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Services\FcmTokenService;
use App\Models\FcmToken;
use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
    public function store(Request $request, FcmTokenService $fcmTokenService)
    {
        $request->validate([
            'token' => 'required|string|unique:fcm_tokens',
        ]);

        $fcmTokenService->createOrUpdate([
            'user_id' => auth()->id(),
            'token' => $request->token,
        ]);

        return response()->json(['message' => 'Token saved successfully'], 200);
    }
}
