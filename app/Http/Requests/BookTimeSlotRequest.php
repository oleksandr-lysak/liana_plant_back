<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @property mixed $time_slot_id
 * @property mixed $client_id
 * @property mixed $comment
 * @property mixed $service_id
 */
class BookTimeSlotRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
        ], 422));
    }

    public function rules(): array
    {
        return [
            'slot_id' => 'required|numeric',
            'client_id' => 'nullable|numeric',
            'comment' => 'nullable|string',
            'service_id' => 'nullable|numeric',
        ];
    }
}
