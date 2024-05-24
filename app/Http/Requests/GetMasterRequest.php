<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetMasterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'page' => 'required|numeric',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'zoom' => 'required|numeric',
        ];
    }
}
