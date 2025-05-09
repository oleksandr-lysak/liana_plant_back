<?php

namespace App\Observers;

use App\Http\Services\Appointment\AppointmentRedisService;
use App\Models\Appointment;
use Illuminate\Support\Carbon;

class AppointmentObserver
{
    protected AppointmentRedisService $appointmentRedisService;

    public function __construct()
    {
        $appointmentRedisService = app(AppointmentRedisService::class);
        $this->appointmentRedisService = $appointmentRedisService;
    }
    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        $this->appointmentRedisService->markAsBusy(
            $appointment->master_id,
            $appointment->start_time,
            $appointment->end_time
        );
    }

    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void
    {
        // Get the original values
        $original = $appointment->getOriginal();

        // Check if the master_id, start_time, or end_time has changed
        if ($appointment->isDirty(['master_id', 'start_time', 'end_time'])) {
            // Delete the old appointment
            $this->appointmentRedisService->markAsFree(
                $original['master_id'],
                Carbon::parse($original['start_time']),
                Carbon::parse($original['end_time'])
            );

            // Add the new appointment
            $this->appointmentRedisService->markAsBusy(
                $appointment->master_id,
                $appointment->start_time,
                $appointment->end_time
            );
        }
    }

    /**
     * Handle the Appointment "deleted" event.
     */
    public function deleted(Appointment $appointment): void
    {
        $this->appointmentRedisService->markAsFree(
            $appointment->master_id,
            $appointment->start_time,
            $appointment->end_time
        );
    }

    /**
     * Handle the Appointment "restored" event.
     */
    public function restored(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "force deleted" event.
     */
    public function forceDeleted(Appointment $appointment): void
    {
        //
    }
}
