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
        $availability = [];
        $timestamp = $checkTime->timestamp;

        $allFreeIntervals = [];

        $results = Redis::pipeline(function ($pipe) use ($masterIds) {
            foreach ($masterIds as $masterId) {
                $pipe->zrangebyscore(
                    $this->getMasterFreeIntervalsKey($masterId),
                    '-inf',
                    '+inf',
                    ['withscores' => true]
                );
            }
        });

        // Прив’язуємо результати до masterId
        foreach ($masterIds as $index => $masterId) {
            $allFreeIntervals[$masterId] = $results[$index];
        }

        foreach ($masterIds as $index => $masterId) {
            $freeIntervals = $allFreeIntervals[$masterId] ?? [];
            // ❗ Тут просто викликаємо ту ж логіку
            $availability[$masterId] = $this->isTimestampInFreeIntervals($freeIntervals, $timestamp);
        }

        return $availability;
    }





    private function isTimestampInFreeIntervals(array $intervals, int $timestamp): bool
    {
        foreach ($intervals as $key => $value) {
            // Якщо індексований — $key буде числовим (0, 1, 2...), і парний (json, score, json, score...)
            // Якщо асоціативний — $key = json, $value = score

            if (is_numeric($key)) {
                if ($key % 2 !== 0) {
                    continue; // Пропускаємо score
                }

                $interval = json_decode($value, true);
            } else {
                $interval = json_decode($key, true);
            }

            if (!$interval || !isset($interval['start'], $interval['end'])) {
                continue;
            }

            if ($timestamp >= $interval['start'] && $timestamp < $interval['end']) {
                return true;
            }
        }

        return false;
    }


}
