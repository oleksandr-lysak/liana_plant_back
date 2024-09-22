<?php
namespace App\Http\Services;

use App\Models\Master;
use App\Models\TimeSlot;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Services\TimeSlotServiceInterface;

class TimeSlotService implements TimeSlotServiceInterface
{
    public function getSlotsForMaster(Master $master, String $startDate): array|Collection
    {
        return TimeSlot::where('master_id', $master->id)
            ->where('date', '>=', $startDate)
            ->get();
    }
}
