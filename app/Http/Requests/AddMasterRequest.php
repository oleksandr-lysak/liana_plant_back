<?php
namespace App\Http\Requests;

use App\Rules\Base64Image;
use Illuminate\Foundation\Http\FormRequest;

class AddMasterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => 'required|string',
            'name' => 'required|string',
            'password' => 'required|string',
            'age' => 'required|numeric',
            'description' => 'required|string',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'specialities' => 'required|array',
            'specialities.*' => 'required',
            'photo' => ['required', new Base64Image()],
            'speciality_id' => 'required|numeric',
        ];
    }
}
