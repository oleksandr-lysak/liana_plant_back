<?php
namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed $phone
 * @property mixed $name
 */
class RegisterClientRequest extends FormRequest
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
            'phone' => 'required|string',
            'name' => 'required|string',
        ];
    }
}
