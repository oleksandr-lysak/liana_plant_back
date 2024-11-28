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
 * @property int $id
 * @property String $name
 * @property double $latitude
 * @property double $longitude
 * @property String $description
 * @property String $address
 * @property int $age
 * @property String $phone
 * @property mixed $services
 * @property String $photo
 * @property double $distance
 * @property int $main_service_id
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

        return [
            'id' => (int)$this->id,
            'name' => (String)$this->name,
            'latitude' => (float)$this->latitude,
            'longitude' => (float)$this->longitude,
            'description' => $this->description,
            'address' => $this->getFormattedAddress($this->address),
            'age' => (int)$this->age,
            'phone' => $this->phone,
            'reviews_count' => (int)$this->reviews_count,
            'rating' => (float)round($this->rating, 1),
            'main_photo' => (String)'storage/' . $this->photo,
            'distance' => (float)round($this->distance, 3),
            'main_service_id' => (int)$this->main_service_id,
            'available' => (bool)$this->available,
        ];

    }

    private function getFormattedAddress($address)
    {
        try {
            return json_decode($address)->results[0]->formatted_address ?? '';
        } catch (\Exception $e) {
            return '';
        }
    }

}
