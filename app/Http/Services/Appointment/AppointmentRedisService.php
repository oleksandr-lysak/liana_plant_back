<?php
namespace App\Http\Services\Appointment;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;

class AppointmentRedisService
{
    public function getMasterBusyIntervalsKey(int $masterId): string
    {
        return "master:{$masterId}:busy_intervals";
    }

    public function clearExpiredIntervals(int $masterId): void
    {
        $nowTimestamp = now()->timestamp;
        // Delete all old intervals
        Redis::zremrangebyscore(
            $this->getMasterBusyIntervalsKey($masterId),
            '-inf',
            $nowTimestamp - 1 // exclude current moment of time
        );
    }
    public function markAsBusy(int $masterId, Carbon $startTime, Carbon $endTime): void
    {
        $this->clearExpiredIntervals($masterId);
        Redis::zadd(
            $this->getMasterBusyIntervalsKey($masterId),
            $startTime->timestamp, // score - start interval
            $endTime->timestamp // value - end interval
        );
    }

    public function markAsFree(int $masterId, Carbon $startTime, Carbon $endTime): void
    {
        Redis::zremrangebyscore(
            $this->getMasterBusyIntervalsKey($masterId),
            $startTime->timestamp,
            $endTime->timestamp
        );
    }

    public function isMasterAvailableAt(int $masterId, Carbon $checkTime): bool
    {
        $busyIntervals = Redis::zrangebyscore(
            $this->getMasterBusyIntervalsKey($masterId),
            '-inf',
            '+inf',
            'WITHSCORES'
        );

        for ($i = 0; $i < count($busyIntervals); $i += 2) {
            $busyStart = $busyIntervals[$i];
            $busyEnd = $busyIntervals[$i + 1];

            // Check if the check time falls within any busy interval
            if ($checkTime->timestamp >= $busyStart && $checkTime->timestamp < $busyEnd) {
                return false;
            }
        }

        return true;
    }

    public function getBusyIntervals(int $masterId, Carbon $startTime = null, Carbon $endTime = null): array
    {
        $min = $startTime ? $startTime->timestamp : '-inf';
        $max = $endTime ? $endTime->timestamp : '+inf';

        $busyIntervals = Redis::zrangebyscore(
            $this->getMasterBusyIntervalsKey($masterId),
            $min,
            $max,
            'WITHSCORES'
        );

        $result = [];
        for ($i = 0; $i < count($busyIntervals); $i += 2) {
            $result[] = [
                'start_time' => Carbon::createFromTimestamp($busyIntervals[$i]),
                'end_time' => Carbon::createFromTimestamp($busyIntervals[$i + 1]),
            ];
        }

        return $result;
    }
}
