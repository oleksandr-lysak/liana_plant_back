<?php
namespace App\Http\Services\Appointment;

use App\Models\Appointment;
use App\Models\Client;
use Illuminate\Support\Carbon;

class AppointmentService
{
    private $redisService;
    private $clock;

    public function __construct(AppointmentRedisService $redisService, $clock = null)
    {
        $this->redisService = $redisService;
        $this->clock = $clock ?: function () { return now(); };
    }

    /**
     * Check if the master is busy at the given time.
     */
    public function isMasterBusy(int $masterId, string $dateTime): bool
    {
        return Appointment::where('master_id', $masterId)
            ->where('start_time', '<=', $dateTime)
            ->where('end_time', '>', $dateTime)
            ->exists();
    }

    /**
     * Get all booked slots for the given master and date.
     */
    public function getBookedSlots(int $masterId, string $date): array
    {
        return Appointment::where('master_id', $masterId)
            ->whereDate('start_time', $date)
            ->orderBy('start_time')
            ->get(['start_time', 'end_time'])
            ->toArray();
    }

    /**
     * Book a slot for the given master.
     */
    public function bookSlot(int $masterId, string $clientPhone, string $startTime, int $service_id, string $comment = '', int $duration = 30): ?Appointment
    {
        $start = Carbon::parse($startTime);
        $end = (clone $start)->addMinutes($duration);
        $client = Client::where('phone', $clientPhone)->first();
        if ($client) {
            $clientId = $client->id;
        } else {
            $clientId = Client::create(['phone' => $clientPhone])->id;
        }
        // Check if the slot is available
        $available = $this->redisService->isMasterAvailableAt($masterId, $start);
        if ($available) {
            return Appointment::create([
                'master_id' => $masterId,
                'service_id' => $service_id,
                'client_id' => $clientId,
                'start_time' => $start,
                'end_time' => $end,
                'comment' => $comment,
            ]);
        }

        return null; // Slot is not available
    }

    /**
     * Check if the slot is available for booking.
     */
    private function isSlotAvailable(int $masterId, Carbon $start, Carbon $end): bool
    {
        return !Appointment::where('master_id', $masterId)
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_time', [$start, $end])
                    ->orWhereBetween('end_time', [$start, $end])
                    ->orWhere(function ($query) use ($start, $end) {
                        $query->where('start_time', '<', $start)
                            ->where('end_time', '>', $end);
                    });
            })
            ->exists();
    }
}
