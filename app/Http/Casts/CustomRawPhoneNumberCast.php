<?php

namespace App\Http\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Propaganistas\LaravelPhone\PhoneNumber;

class CustomRawPhoneNumberCast implements CastsAttributes
{
    protected string $defaultCountry;

    public function __construct($defaultCountry = 'INTERNATIONAL')
    {
        $this->defaultCountry = $defaultCountry;
    }

    public function get($model, $key, $value, $attributes)
    {
        return $value;
    }

    public function set($model, $key, $value, $attributes)
    {
        try {
            $phone = new PhoneNumber($value, $this->defaultCountry);
        } catch (\Exception $e) {
            $phone = $value;
        }
        return $phone->formatE164();
    }
}
