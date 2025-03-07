<?php

namespace App\Http\Services;

use Daaner\TurboSMS\Facades\TurboSMS;
use Illuminate\Support\Facades\Cache;

class SmsService
{
    public function generateAndSendCode(string $phone): string
    {
        $code = rand(1000, 9999);
        Cache::put('sms_code_'.$phone, $code, now()->addMinutes(10));

        TurboSMS::sendMessages($phone, "$code");

        return "$code";
    }

    public function verifyCode(string $phone, string $inputCode): bool
    {
        $cachedCode = Cache::get('sms_code_'.$phone);

        return $cachedCode == $inputCode;
    }
}
