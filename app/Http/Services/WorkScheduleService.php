<?php
namespace App\Http\Services;

use App\Models\TimeSlot;
use App\Models\WorkSchedule;
use App\Models\Master;
use Illuminate\Support\Carbon;

/**
 * Class WorkScheduleService
 * @package App\Http\Services
 *
 * Service class to handle work schedule logic.
 */
class WorkScheduleService
{
    protected array $defaultWorkSchedule = [
        0 => ['start' => '09:00', 'end' => '18:00'],
        1 => ['start' => '09:00', 'end' => '18:00'],
        2 => ['start' => '09:00', 'end' => '18:00'],
        3 => ['start' => '09:00', 'end' => '18:00'],
        4 => ['start' => '09:00', 'end' => '18:00'],
        5 => ['start' => '09:00', 'end' => '18:00'],
        6 => ['start' => null, 'end' => null],
    ];

    /**
     * Update or create the work schedule for the master.
     *
     * @param  Master $provider
     * @param  array $scheduleData
     * @return void
     */
    public function updateWorkSchedule(Master $provider, array $scheduleData): void
    {
        foreach ($scheduleData as $dayOfWeek => $times) {
            // check if the work schedule is different from the default schedule
            if ($times['start'] !== config('work_schedule.default_schedule')[$dayOfWeek]['start'] ||
                $times['end'] !== config('work_schedule.default_schedule')[$dayOfWeek]['end']) {

                // Update or create the work schedule in database
                WorkSchedule::updateOrCreate(
                    [
                        'provider_id' => $provider->id,
                        'day_of_week' => $dayOfWeek
                    ],
                    [
                        'start_time' => $times['start'],
                        'end_time' => $times['end']
                    ]
                );
            }
        }
    }

    /**
     * Get the work schedule for the master on the given day of the week.
     *
     * @param  Master $master
     * @param  int $dayOfWeek
     * @return array
     */
    public function getWorkSchedule(Master $master, int $dayOfWeek): array
    {
        // get custom work schedule if it exists
        $customSchedule = $master->workSchedules()
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if ($customSchedule) {
            return ['start' => $customSchedule->start_time, 'end' => $customSchedule->end_time];
        }

        // return the default work schedule
        return $this->defaultWorkSchedule[$dayOfWeek] ?? [];
    }

    /**
     * Check if the master is available for booking now.
     */
    public function isMasterAvailableNow(Master $master): bool
    {
        // get the current date and time
        $now = Carbon::now();
        $currentDayOfWeek = $now->dayOfWeek; // day of the week (0 = Sunday, 1 = Monday, etc.)
        $currentTime = $now->format('H:i'); // current time in HH:MM format

        // get the work schedule for the current day of the week
        $workSchedule = $this->getWorkSchedule($master, $currentDayOfWeek);

        if (!$workSchedule || !$workSchedule['start'] || !$workSchedule['end']) {
            // the master does not work on the current day of the week
            return false;
        }

        // check if the current time is within the working hours
        if ($currentTime < $workSchedule['start'] || $currentTime > $workSchedule['end']) {
            return false;
        }

        // check if the master has any appointments at the current time
        $date = $now->copy()->toDateString();
        $startTime = $now->copy()->format('H:i:s');
        $duration = 60; // Duration of the appointment in minutes
        $endTime = date('H:i:s', strtotime($startTime) + ($duration * 60));

        $isSlotReserved = TimeSlot::where('master_id', $master->id)
            ->where('date', $date) // check date
            ->where(function($query) use ($startTime, $endTime) {
                // check if the current time slot overlaps with the existing time slots
                $query->where(function($q) use ($startTime, $endTime) {
                    $q->where('time', '<', $endTime) // the booking time end before the existing time slot
                    ->where('time', '>=', $startTime); // the booking time start after the existing time slot
                });
            })
            ->exists();

        return !$isSlotReserved;
    }
}
