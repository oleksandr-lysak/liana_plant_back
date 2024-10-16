<?php

namespace App\Http\Services;

use App\Models\Master;
use App\Specifications\MasterSpecification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MasterService
{
    protected Master $model;
    protected MasterSpecification $specification;

    public function __construct(Master $master,  MasterSpecification $specification)
    {
        $this->model = $master;
        $this->specification = $specification;
    }

    private static function calculateSearchRadius(int $zoom): float
    {
        $earthRadiusKm = 20037.5; // екваторіальний радіус Землі
        return $earthRadiusKm / pow(2, $zoom);  // радіус на поточному зумі
    }

    public function getMastersOnDistance(int $page, float $lat, float $lng, float $zoom, array $filters): LengthAwarePaginator
    {
        $max_distance = $this::calculateSearchRadius($zoom);

        $query = Master::query();
        $query->select('*');
        $query->selectRaw(
            '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) as distance',
            [$lat, $lng, $lat]
        );
        $query->withCount(['time_slots as available' => function ($q) {
            $now = now();
            $currentDate = $now->toDateString();
            $currentTime = $now->format('H:i:s');

            $q->whereDate('date', '=', $currentDate)
                ->where('is_booked', false) // only free slots
                ->where('time', '<', $currentTime) // slots witch start time is greater than current time
                ->whereRaw('ADDDATE(CONCAT(date, " ", time), INTERVAL duration MINUTE) > ?', [$now]); // check if slot is not ended

            $q->orderBy('time', 'desc');
        }]);
        $query = $this->specification->apply($query, $filters);
        $query->with(['services']);
        $query->whereRaw('6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))) <= ?', [$lat, $lng, $lat, $max_distance]);
        $query->with('services');
        $query->with('reviews');
        $query->orderBy('available', 'desc');
        $query->orderBy('distance','asc');
        $query->orderBy('id', 'asc');

        return $query->paginate(300, ['*'], 'page', $page);

    }
    public function createOrUpdate(array $data): Master
    {
        $photo = $data['photo'];
        $master = Master::updateOrCreate(['phone' => $data['phone']], $data);

        $this->handlePhoto($master, $photo);

        return $master;
    }

    protected function handlePhoto(Master $master, $photo): void
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
