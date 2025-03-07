<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 'page' => 'required|numeric',
        ];
    }
}
