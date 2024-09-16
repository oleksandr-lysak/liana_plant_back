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
        $max_distance = 100;
        if ($zoom > 12) {
            $max_distance = 5;
        }

        $query = Master::query();
        $query->select('*');
        $query->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) as distance', [$lat, $lng, $lat]);
        $query->havingRaw('distance <= ?', [$max_distance]);
        $query->with('specialities');
        $query
            //->with('reviews')
            ->withCount('reviews');
        $query->orderBy('distance');


        return $query->paginate(100, ['*'], 'page', $page);

    }
    public function createOrUpdate(array $data)
    {
        $photo = $data['photo'];
        $master = Master::updateOrCreate(['phone' => $data['phone']], $data);

        $this->handlePhoto($master, $photo);

        return $master;
    }

    protected function handlePhoto(Master $master, $photo)
    {
        if ($photo) {
            $oldPhoto = $master->photo;
            if ($oldPhoto) {
                Storage::disk('public')->delete($oldPhoto);
            }

            if (preg_match('/^data:image\/(\w+);base64,/', $photo, $matches)) {
                $extension = $matches[1];
                $photo = base64_decode(substr($photo, strpos($photo, ',') + 1));
                $photoName = uniqid() . '.' . $extension;
                Storage::disk('public')->put('photos/' . $photoName, $photo);
                $master->update(['photo' => 'photos/' . $photoName]);
            } else {
                throw new \Exception('The provided photo is not a valid Base64 image.');
            }
        }
    }


    public function addReview(mixed $data): Model
    {
        // Create the review
        $master = $this->model::find($data['master_id']);
        $data['user_id'] = 1;
        return $master->reviews()->create($data);
    }
}
