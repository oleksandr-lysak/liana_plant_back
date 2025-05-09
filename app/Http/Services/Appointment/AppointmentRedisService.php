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

    public function getMasterFreeIntervalsKey(int $masterId): string
    {
        return "master:{$masterId}:free_intervals";
    }

    public function clearExpiredIntervals(int $masterId): void
    {
        Redis::zremrangebyscore(
            $this->getMasterBusyIntervalsKey($masterId),
            '-inf',
            now()->timestamp - 3600 * 24
        );
        Redis::zremrangebyscore(
            $this->getMasterFreeIntervalsKey($masterId),
            '-inf',
            now()->timestamp - 3600 * 24 // наприклад, очищає інтервали старші 24 год
        );
    }
    public function markAsBusy(int $masterId, Carbon $startTime, Carbon $endTime): void
    {
        $this->clearExpiredIntervals($masterId);
        Redis::zadd(
            $this->getMasterBusyIntervalsKey($masterId),
            $startTime->timestamp,
            json_encode([
                'start' => $startTime->timestamp,
                'end' => $endTime->timestamp
            ])
        );
    }

    public function markAsFree(int $masterId, Carbon $startTime, Carbon $endTime): void
    {
        $this->clearExpiredIntervals($masterId);
        Redis::zadd(
            $this->getMasterFreeIntervalsKey($masterId),
            $startTime->timestamp,
            json_encode([
                'start' => $startTime->timestamp,
                'end' => $endTime->timestamp
            ])
        );
    }

    public function isMasterAvailableAt(int $masterId, Carbon $checkTime): bool
    {
        $freeIntervals = Redis::zrangebyscore(
            $this->getMasterFreeIntervalsKey($masterId),
            '-inf',
            '+inf',
            'WITHSCORES'
        );

        return $this->isTimestampInFreeIntervals($freeIntervals, $checkTime->timestamp);
    }


    public function getAvailabilityForMany(array $masterIds, Carbon $checkTime): array
    {
        $keys = [];
        $results = Redis::pipeline(function ($pipe) use ($masterIds, &$keys) {
            foreach ($masterIds as $masterId) {
                $key = $this->getMasterFreeIntervalsKey($masterId);
                $keys[] = $masterId;
                $pipe->zrangebyscore($key, '-inf', '+inf', ['WITHSCORES' => true]);
            }
        });

        $availability = [];

        foreach ($results as $index => $freeIntervals) {
            $masterId = $keys[$index];
            $availability[$masterId] = $this->isTimestampInFreeIntervals($freeIntervals, $checkTime->timestamp);
        }

        return $availability;
    }

    private function isTimestampInFreeIntervals(array $freeIntervals, int $timestamp): bool
    {
        for ($i = 0; $i < count($freeIntervals); $i += 2) {
            $intervalJson = $freeIntervals[$i];
            $interval = json_decode($intervalJson, true);

            if (!$interval || !isset($interval['start'], $interval['end'])) {
                continue;
            }

            $start = (int) $interval['start'];
            $end = (int) $interval['end'];

            if ($timestamp >= $start && $timestamp < $end) {
                return true;
            }
        }

        return false;
    }

}
