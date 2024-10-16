<?php

namespace App\Http\Services;

use App\Models\Master;
use App\Models\TimeSlot;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Services\TimeSlotServiceInterface;

class TimeSlotService implements TimeSlotServiceInterface
{
    /**
     * Retrieve available time slots for a given master starting from a specific date.
     *
     * @param Master $master The master for whom the time slots are being retrieved.
     * @param string $startDate The start date from which to retrieve the time slots.
     * @return array|Collection The available time slots for the master.
     */
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


    public function validateUserForMaster($user, $masterId)
    {
        if ($user == null && $masterId == 0) {
            return ['status' => false, 'message' => 'Invalid token'];
        }

        if ($user != null && $user->master == null && $masterId == 0) {
            return ['status' => false, 'message' => 'User is not a master'];
        }

        if ($masterId != 0) {
            $master = Master::find($masterId);
        } else {
            $master = $user->master;
        }

        return ['status' => true, 'master' => $master];
    }

}
