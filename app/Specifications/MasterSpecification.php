<?php
namespace App\Specifications;

use App\Http\Services\WorkScheduleService;
use Illuminate\Database\Eloquent\Builder;

class MasterSpecification
{
    protected WorkScheduleService $workScheduleService;

    public function __construct(WorkScheduleService $workScheduleService)
    {
        $this->workScheduleService = $workScheduleService;
    }

    public function apply(Builder $query, array $filters): Builder
    {
        if (isset($filters['name'])) {
            $query->where('name', 'LIKE', '%' . $filters['name'] . '%');
        }

        if (isset($filters['distance'])) {
            $query->having('distance', '<=', $filters['distance']);
        }

        if (isset($filters['service_id'])) {
            $query->whereHas('services', function ($q) use ($filters) {
                $q->where('services.id', $filters['service_id']);
            });
        }

        if (isset($filters['rating'])) {
            $query->where('rating', '>=', $filters['rating']);
        }

        if (isset($filters['available'])) {
            $query->where(function($q) use ($filters) {
                $q->whereHas('workSchedules', function ($q) {
                    // In case master has work schedule for now
                    $q->where('start_time', '<=', now())->where('end_time', '>=', now());
                })
                    // Logic for checking if master is available now
                    ->orWhere(function($q) use ($filters) {
                        $this->workScheduleService->isMasterAvailableNow($q->master_id);
                    });
            });
        }

        return $query;
    }
}
