<?php
namespace App\Http\Requests;

use App\Rules\Base64Image;
use Illuminate\Foundation\Http\FormRequest;
use Propaganistas\LaravelPhone\PhoneNumber;

/**
 * @property mixed $phone
 * @property mixed $country_code
 */
class AddMasterRequest extends FormRequest
{


    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->country_code) {
            $phone = preg_replace('/[^0-9]/', '', $this->phone);
            $phone = preg_replace('/[^0-9]/', '', phone($phone, $this->country_code)->formatNational());
            $phone = phone($phone, $this->country_code)->formatE164();
            $this->merge([
                'phone' => new PhoneNumber($phone)
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'country_code' => 'required|string',
            'phone' => 'required|phone:mobile',
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
