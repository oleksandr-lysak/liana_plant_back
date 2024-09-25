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

    /**
     * Stores a time slot with the given validated data.
     *
     * @param array $validated An array of validated data for the time slot.
     * @return array The stored time slot data.
     */
    public function storeTimeSlot(array $validated): array
    {
        $timeSlotIsFree = (TimeSlot::where('master_id', $validated['master_id'])
            ->where('date', $validated['date'])
            ->where('time', $validated['time'])
            ->first() == null);
        if ($timeSlotIsFree) {
            TimeSlot::updateOrCreate(
                [
                    'master_id' => $validated['master_id'],
                    'date' => $validated['date'],
                    'time' => $validated['time'],
                ],
                $validated
            );
            return ['status' => true ,'message' => 'Time slot updated successfully'];
        } else {
            return ['status' => false ,'message' => 'Time slot is already taken'];
        }
    }
}
