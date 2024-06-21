<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * MasterResource Class
 *
 * This class extends the JsonResource class provided by Laravel.
 * It is used to transform your resource into an array that can be returned as a JSON response.
 *
 * @property mixed $reviews
 * @property mixed $id
 * @property mixed $name
 * @property mixed $latitude
 * @property mixed $longitude
 * @property mixed $description
 * @property mixed $address
 * @property mixed $age
 * @property mixed $phone
 * @property mixed $specialities
 * @property mixed $photo
 * @property mixed $distance
 */
class MasterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * This method is used to transform the `Master` model into an array that can be returned as a JSON response.
     * It calculates the average rating of the master based on the reviews and returns an array containing the master's details.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $rating = 0;
        // Calculate the total rating by adding up all the review ratings
        $this->reviews->each(function ($review) use (&$rating) {
            $rating += $review->rating;
        });
        // Calculate the average rating
        $rating = $rating / ($this->reviews->count() ? $this->reviews->count() : 1);

        try {
            $address = (json_decode($this->address)->results[0]->formatted_address);
        } catch (\Exception $e) {
            $address = '';
        }
        // Return the master's details as an array
        return [
            'id' => $this->id,
            'name' => $this->name,
            'latitude' => (float)$this->latitude,
            'longitude' => (float)$this->longitude,
            'description' => $this->description,
            'address' => $address,
            'age' => (int)$this->age,
            'phone' => $this->phone,
            'reviews' => $this->reviews->map(function ($review) {
                return [
                    'id' => $review->id,
                    'review' => $review->review,
                    'rating' => $review->rating,
                ];
            }),
            'specialities' => $this->specialities->map(function ($speciality) {
                return [
                    'id' => $speciality->id,
                    'name' => $speciality->name,
                ];
            }),
            'rating' => $rating,
            'main_photo' => asset('storage/' . $this->photo),
            'distance' => round($this->distance, 3),
        ];
    }
}
