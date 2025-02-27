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
        $startKey = "slot:{$masterId}:start:" . strtotime($startTime);
        $endKey = "slot:{$masterId}:end:" . strtotime($endTime);

        $data = json_encode([
            'master_id' => $masterId,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => $status,
        ]);

        // Зберігаємо стартовий ключ із TTL до моменту старту
        Redis::setex($startKey, strtotime($startTime) - time(), $data);

        // Зберігаємо кінцевий ключ із TTL до моменту завершення
        Redis::setex($endKey, strtotime($endTime) - time(), $data);
    }

}
