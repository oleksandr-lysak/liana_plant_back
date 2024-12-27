<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

            return [
                'work_schedule' => ['required', 'array'],
                'work_schedule.*.start' => ['nullable', 'date_format:H:i'],
                'work_schedule.*.end' => ['nullable', 'date_format:H:i', 'after:work_schedule.*.start'],
            ];

    }
    public function messages()
    {
        return [
            'work_schedule.required' => 'Графік роботи є обов’язковим.',
            'work_schedule.*.start.date_format' => 'Час початку має бути у форматі ГГ:ХХ.',
            'work_schedule.*.end.date_format' => 'Час закінчення має бути у форматі ГГ:ХХ.',
            'work_schedule.*.end.after' => 'Час закінчення має бути пізніше часу початку.',
        ];
    }
}
