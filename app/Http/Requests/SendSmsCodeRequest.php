<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendSmsCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => [
                'required',
                'regex:/^\+(?:\d{1,3})?\d{6,14}$/', // International phone number format
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'The phone field is required.',
            'phone.regex' => 'Wrong phone number format. Use international format. For example: +380501234567',
        ];
    }
}
