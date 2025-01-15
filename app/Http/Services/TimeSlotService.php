<?php

namespace App\Http\Services;

use App\Enums\TimeSlotStatus;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TimeSlotService
{
    /**
     * Generate time slots for a given master between two dates
     */
    public function generateTimeSlots(int $masterId, Carbon $startDate, Carbon $endDate): void
    {
        // Example working hours for each day of the week
        $workingHours = [
            'Monday' => ['09:00', '18:00'],
            'Tuesday' => ['09:00', '18:00'],
            'Wednesday' => ['09:00', '18:00'],
            'Thursday' => ['09:00', '18:00'],
            'Friday' => ['09:00', '18:00'],
            'Saturday' => ['10:00', '14:00'],
            'Sunday' => null
        ];

        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            // check if the current day has working hours
            $dayOfWeek = $currentDate->format('l'); // Get the day of the week (e.g., 'Monday', 'Tuesday', etc.)

            if ($workingHours[$dayOfWeek]) {
                // if the day has working hours, create time slots for that day
                $existingSlots = TimeSlot::where('master_id', $masterId)
                    ->whereDate('date', $currentDate->toDateString())
                    ->exists();

                if (!$existingSlots) {
                    $this->createTimeSlotsForDay($masterId, $currentDate, $workingHours[$dayOfWeek]);
                }
            }

            // go to the next day
            $currentDate->addDay();
        }
    }

    /**
     * generate time slots for a given day
     */
    private function createTimeSlotsForDay(int $masterId, Carbon $date, array $workingHours): void
    {
        $startTime = Carbon::createFromFormat('H:i', $workingHours[0]);
        $endTime = Carbon::createFromFormat('H:i', $workingHours[1]);

        // generate time slots for the day with 1 hour interval
        while ($startTime < $endTime) {
            TimeSlot::create([
                'master_id' => $masterId,
                'date' => $date->toDateString(),
                'start_time' => $startTime->toTimeString(),
                'end_time' => $startTime->copy()->addHour()->toTimeString(),
                'status' => TimeSlotStatus::Booked,
            ]);

            // go to the next time slot
            $startTime->addHour();
        }
    }

    public function bookTimeSlot(array $data): void
    {
        DB::transaction(function () use ($data) {
            TimeSlot::where('id', $data['slot_id'])
                ->where('status', TimeSlotStatus::Free)
                ->update([
                    'status' => TimeSlotStatus::Booked,
                    'client_id' => $data['client_id'],
                    'service_id' => $data['service_id'],
                    'comment' => $data['comment'],
                ]);
        });
    }

    public function completeTimeSlot(int $slotId): void
    {
        TimeSlot::where('id', $slotId)
            ->where('status', TimeSlotStatus::Booked)
            ->update(['status' => TimeSlotStatus::Completed]);
    }

    public function setFreeTimeSlot(int $slotId): void
    {
        TimeSlot::where('id', $slotId)
            ->where('status', TimeSlotStatus::Booked)
            ->update([
                'status' => TimeSlotStatus::Free,
                'client_id' => null,
                'service_id' => null,
                'comment' => null,
            ]);
    }
}
