<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportExternalMasterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'address' => 'required|string',
            'description' => 'nullable|string',
            'phone' => 'required|string',
            'main_photo' => 'required|url',
            'reviews' => 'array',
            'reviews.*.text' => 'required|string',
            'reviews.*.rating' => 'required|string',
            'reviews.*.author' => 'required|string',
            'rating' => 'nullable|string',
            'place_id' => 'nullable|string',
            'coordinates.lat' => 'required|numeric',
            'coordinates.lng' => 'required|numeric',
            'service_id' => 'nullable|numeric',
        ];
    }
} 