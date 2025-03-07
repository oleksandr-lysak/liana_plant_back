<?php

namespace App\Http\Resources;

use App\Http\Services\Master\MasterStatusService;
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
 * @property string $name
 * @property float $latitude
 * @property float $longitude
 * @property string $description
 * @property string $address
 * @property int $age
 * @property string $phone
 * @property mixed $services
 * @property string $photo
 * @property float $distance
 * @property int $main_service_id
 */
class MasterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * This method is used to transform the `Master` model into an array that can be returned as a JSON response.
     * It calculates the average rating of the master based on the reviews and returns an array containing the master's details.
     */
    public function toArray(Request $request): array
    {
        $statusService = app(MasterStatusService::class);

        return [
            'id' => (int) $this->id,
            'name' => (string) $this->name,
            'latitude' => (float) $this->latitude,
            'longitude' => (float) $this->longitude,
            'description' => $this->description,
            'address' => $this->getFormattedAddress($this->address),
            'age' => (int) $this->age,
            'phone' => $this->phone,
            'reviews_count' => (int) $this->reviews_count,
            'rating' => (float) round($this->rating, 1),
            'main_photo' => (string) 'storage/'.$this->photo,
            'distance' => (float) round($this->distance, 3),
            'main_service_id' => (int) $this->service_id,
            'available' => (bool) $statusService->isMasterFree($this->id),
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
