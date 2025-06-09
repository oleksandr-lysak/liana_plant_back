<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetMasterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'zoom' => 'required|numeric',
            'page' => 'integer|min:1',
            'name' => 'string|nullable',
            'distance' => 'numeric|nullable',
            'service_id' => 'int|nullable',
            'rating' => 'numeric|min:1|max:5|nullable',
            'available' => 'boolean|nullable',
        ];
    }
}
