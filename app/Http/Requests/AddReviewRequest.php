<?php
namespace App\Http\Requests;

use App\Rules\Base64Image;
use Illuminate\Foundation\Http\FormRequest;

class AddReviewRequest extends FormRequest
{


    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'review' => 'required|string',
            'rating' => 'required|numeric',
            'master_id' => 'required|numeric',
        ];
    }
}
