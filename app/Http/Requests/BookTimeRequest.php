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
class BookTimeRequest extends FormRequest
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
            'master_id'  => 'required|integer',
            'start_time' => 'required|date',
            'duration'   => 'integer|min:5|max:480',
            'service_id' => 'nullable|integer',
            'comment' => 'nullable|string',
            'client_phone' => 'required|string',
        ];
    }
}
