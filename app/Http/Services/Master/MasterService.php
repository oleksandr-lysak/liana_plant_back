<?php

namespace App\Http\Services\Master;

use App\Http\Services\PaginatorService;
use App\Models\Master;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class MasterService
{
    protected Master $model;

    protected PaginatorService $paginatorService;

    protected MasterSearchService $masterSearchService;

    public function __construct(Master $master, PaginatorService $paginatorService, MasterSearchService $masterSearchService)
    {
        $this->model = $master;
        $this->paginatorService = $paginatorService;
        $this->masterSearchService = $masterSearchService;
    }

    public function getMastersOnDistance(
        int $page,
        float $lat,
        float $lng,
        float $zoom,
        array $filters
    ): LengthAwarePaginator {
        $perPage = 1000;

        // get masters
        $masters = $this->masterSearchService->getMastersOnDistance($lat, $lng, $zoom, $filters, $perPage, $page);

        // get general count of masters
        $totalMasters = count($masters);

        // create paginator
        return $this->paginatorService->paginate($masters, $totalMasters, $perPage, $page);
    }

    /**
     * @throws Exception
     */
    public function createOrUpdate(array $data): Master
    {
        $photo = $data['photo'];
        $master = Master::updateOrCreate(['phone' => $data['phone']], $data);

        $this->handlePhoto($master, $photo);

        return $master;
    }

    /**
     * @throws Exception
     */
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
                $photoName = uniqid().'.'.$extension;
                Storage::disk('public')->put('photos/'.$photoName, $photo);
                $master->update(['photo' => 'photos/'.$photoName]);
            } else {
                throw new Exception('The provided photo is not a valid Base64 image.');
            }
        }
    }

    public function addReview(mixed $data): Model
    {
        $master = $this->model::find($data['master_id']);

        return $master->reviews()->create($data);
    }
}
