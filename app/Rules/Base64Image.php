<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class Base64Image implements Rule
{
    public function passes($attribute, $value): bool
    {
        if (empty($value)) {
            return false;
        }
        // Check if the data is a valid base64 string
        if (base64_decode($value, true) === false) {
            return false;
        }

        // Validate the file
        $validator = Validator::make(['file' => $value], ['file' => 'image']);
        if ($validator->fails()) {
            error_log('The file is not an image');
            return false;
        }
        return true;
    }

    public function message(): string
    {
        return 'The :attribute must be a valid image.';
    }
}
