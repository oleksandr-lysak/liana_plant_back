<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $name
 * @property mixed $review
 * @property mixed $rating
 * @property mixed $master_id
 */
class ReviewResource extends JsonResource
{
    public function toArray($request): array
    {
        
        return [
            'id' => $this->id,
            //'user_name' => $this->userName,
            'comment' => $this->review,
            'rating' => $this->rating,
            'master_id' => $this->master_id,
        ];
    }
}
