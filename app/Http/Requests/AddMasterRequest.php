<?php
namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Rules\Base64Image;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed $phone
 * @property mixed $country_code
 * @property mixed $latitude
 * @property mixed $longitude
 */
class AddMasterRequest extends FormRequest
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
            'description' => 'required|string',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'place_id' => 'required|string',
            'sms_code' => 'required|numeric',
            'photo' => ['required', new Base64Image()],
            'service_id' => 'required|numeric',
        ];
    }
}
