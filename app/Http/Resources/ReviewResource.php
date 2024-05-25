<?php

namespace App\Http\Resources;

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
            'name' => $this->review,
            'rating' => $this->rating,
            'master_id' => $this->master_id,
        ];
    }
}
