<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed $time_slot_id
 * @property mixed $client_id
 * @property mixed $comment
 * @property mixed $service_id
 */
class FreeTimeSlotRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }

    public function rules(): array
    {
        return [
            'date_time' => 'required|date_format:Y-m-d H:i:s',
            'master_id' => 'required|integer|exists:masters,id',
        ];
    }
}
