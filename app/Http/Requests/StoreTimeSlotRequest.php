<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTimeSlotRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'master_id' => 'required|exists:masters,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'is_booked' => 'required|boolean',
            'client_name' => 'nullable|string',
            'client_phone' => 'required|string',
            'service_id' => 'required|numeric',
            'source' => 'required|string',
            'duration' => 'required|numeric',
        ];
    }
}
