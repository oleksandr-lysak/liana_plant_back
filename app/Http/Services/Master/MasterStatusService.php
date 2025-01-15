<?php

namespace App\Http\Services\Master;

use App\Enums\TimeSlotStatus;
use App\Models\TimeSlot;
use Illuminate\Support\Facades\Redis;

class MasterStatusService
{
    const REDIS_KEY_PREFIX = 'master_status:';

    public function updateSlotStatusWithTimeRange(int $masterId, string $startTime, string $endTime, string $status): void
    {
        $key = self::REDIS_KEY_PREFIX . $masterId;

        // Зберігаємо початок як score, JSON як значення
        $data = [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => $status,
        ];
        Redis::zadd($key, strtotime($startTime), json_encode($data));
    }

    public function getMasterStatus(int $masterId): string
    {
        $key = self::REDIS_KEY_PREFIX . $masterId;

        // Отримуємо всі активні часові діапазони
        $currentTimestamp = now()->timestamp;
        $activeSlots = Redis::zrangebyscore($key, $currentTimestamp, '+inf');

        foreach ($activeSlots as $slotData) {
            $slot = json_decode($slotData, true);
            if ($slot['status'] === TimeSlotStatus::Free->value) {
                return TimeSlotStatus::Free->value;
            }
        }

        return TimeSlotStatus::Booked->value;
    }

    public function clearExpiredSlots(int $masterId): void
    {
        $key = self::REDIS_KEY_PREFIX . $masterId;

        // Видаляємо всі записи, термін дії яких завершився
        $currentTimestamp = now()->timestamp;
        Redis::zremrangebyscore($key, '-inf', $currentTimestamp);
    }

    public function isMasterFree(int $masterId): bool
    {   $this->clearExpiredSlots($masterId);
        $status = $this->getMasterStatus($masterId);

        return $status === TimeSlotStatus::Free->value;
    }

    public function rebuildCacheForMaster(int $masterId): void
    {
        $key = self::REDIS_KEY_PREFIX . $masterId;
        if (!Redis::exists($key)) {
            $timeSlots = TimeSlot::where('master_id', $masterId)
                ->where('status', '!=', TimeSlotStatus::Free->value)
                ->get();

            $redisKey = 'master_status:' . $masterId;
            Redis::del($redisKey); // Очистка старих даних

            foreach ($timeSlots as $slot) {
                $data = [
                    'start_time' => $slot->start_time,
                    'end_time' => $slot->end_time,
                    'status' => $slot->status,
                ];
                Redis::zadd($redisKey, strtotime($slot->start_time), json_encode($data));
            }
        }
    }
}
