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
 * @property mixed $services
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
        $reviews_count = $this->reviews->count();
        $rating = $reviews_count ? $this->reviews->avg('rating') : 0;

        $address = $this->getFormattedAddress($this->address);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'latitude' => (float)$this->latitude,
            'longitude' => (float)$this->longitude,
            'description' => $this->description,
            'address' => $address,
            'age' => (int)$this->age,
            'phone' => $this->phone,
            'reviews_count' => $reviews_count,
            'services' => $this->services->map(fn($service) => [
                'id' => $service->id,
                'name' => __('data.services.' . $service->name),
            ]),
            'rating' => round($rating,1),
            'main_photo' => 'storage/' . $this->photo,
            'distance' => round($this->distance, 3),
            'available' => $this->available,
        ];
    }

    private function getFormattedAddress($address)
    {
        try {
            return json_decode($address)->results[0]->formatted_address ?? ''; // Використання ?? для повернення пустого рядка
        } catch (\Exception $e) {
            return '';
        }
    }

}
