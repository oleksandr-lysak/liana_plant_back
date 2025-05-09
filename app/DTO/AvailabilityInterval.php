<?php
namespace App\DTO;

use Illuminate\Support\Carbon;

class AvailabilityInterval
{
    public Carbon $start;
    public Carbon $end;

    /**
     * AvailabilityInterval constructor.
     *
     * @param string $startTime
     * @param int $durationMinutes
     */
    public function __construct(string $startTime, int $durationMinutes)
    {
        $this->start = Carbon::parse($startTime);
        $this->end = $this->start->copy()->addMinutes($durationMinutes);
    }
}
