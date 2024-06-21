<?php

namespace App\Http\Services;

use App\Models\Master;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MasterService
{
    protected Master $model;

    public function __construct(Master $master)
    {
        $this->model = $master;
    }
    public function getMastersOnDistance(int $page, float $lat, float $lng, float $zoom): LengthAwarePaginator
    {
        $max_distance = 10;
        if ($zoom > 12) {
            $max_distance = 5;
        }

        $query = Master::query();
        $query->select('*');
        $query->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) as distance', [$lat, $lng, $lat]);
        $query->havingRaw('distance <= ?', [$max_distance]);
        $query->with('specialities');
        $query->with('reviews');

        return $query->paginate(100, ['*'], 'page', $page);

    }

    public function firstOrCreate(array $searchByData, array $data)
    {
        // Extract specialities and photo from the data array
        $specialities = $data['specialities'];
        $photo = $data['photo'];
        unset($data['specialities']);

        // Create or get the master
        $master = $this->model::updateOrCreate($searchByData, $data);

        // Sync the specialities
        $master->specialities()->sync($specialities);

        // Save the photo
        if ($photo) {
            $oldPhoto = $master->photo;
            if ($oldPhoto) {
                Storage::disk('public')->delete($oldPhoto);
            }
            $photoPath = $photo->store('photos', 'public');
            $master->update(['photo' => $photoPath]);
        }

        return $master;
    }

    public function addReview(mixed $data): Model
    {
        // Create the review
        $master = $this->model::find($data['master_id']);
        $data['user_id'] = 1;
        return $master->reviews()->create($data);
    }
}
