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
class UserResource extends JsonResource
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
        $master_data = null;
        $client_data = null;
        if ($this->master != null) {
            $master = $this->master;
            $master_data = [
                'id' => $master->id,
                'name' => $master->name,
                'phone' => $master->phone,
                'address' => $master->address,
                'latitude' => $master->latitude,
                'longitude' => $master->longitude,
                'description' => $master->description,
                'age' => $master->age,
                'specialities' => $master->specialities->pluck('id'),
                'photo' => $master->photo,
                'speciality_id' => $master->speciality_id,
            ];
        }
        if ($this->client != null) {
            $client = $this->client;
            $client_data = [
                'id' => $this->id,
                'phone' => $client->phone,
            ];
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'master_data' => $master_data,
            'client_data' => $client_data,
        ];
    }
}