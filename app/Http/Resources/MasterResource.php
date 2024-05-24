<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MasterResource extends JsonResource
{
    public function toArray($request): array
    {
        $rating = 0;
        $this->reviews->each(function ($review) use (&$rating) {
            $rating += $review->rating;
        });
        $rating = $rating / ($this->reviews->count()? $this->reviews->count() : 1);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'latitude'=>(float)$this->latitude,
            'longitude' => (float)$this->longitude,
            'description' => $this->description,
            'address' => $this->address,
            //'free' => $this->free,
            'age' => (int)$this->age,
            'phone' => $this->phone,
            'reviews' => $this->reviews,
            'specialities' => $this->specialities,
            'rating' => $rating,
            'main_photo' => $this->photo,
            'distance' => round($this->distance,3),

        ];
    }
}
