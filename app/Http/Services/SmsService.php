<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Cache;
use Daaner\TurboSMS\Facades\TurboSMS;

class SmsService
{
    /**
     * @param string $phone
     * @return void
     */
    public function generateAndSendCode(string $phone): void
    {
        $code = rand(1000, 9999);
        Cache::put('sms_code_' . $phone, $code, now()->addMinutes(10));

        TurboSMS::sendMessages($phone, "$code");
    }

    /**
     * @param string $phone
     * @param string $inputCode
     * @return bool
     */
    public function verifyCode(string $phone, string $inputCode): bool
    {
        $cachedCode = Cache::get('sms_code_' . $phone);
        return $cachedCode === $inputCode;
    }
}