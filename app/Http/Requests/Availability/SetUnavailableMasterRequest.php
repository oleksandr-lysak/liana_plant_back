<?php

namespace App\Http\Requests\Availability;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @property mixed $phone
 * @property mixed $country_code
 * @property mixed $latitude
 * @property mixed $longitude
 */
class SetUnavailableMasterRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
        ], 422));
    }

    public function rules(): array
    {
        return [];
    }
}
