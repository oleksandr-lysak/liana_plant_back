<?php

namespace App\Http\Services\Master;

use App\Http\Services\AppointmentService;
use Illuminate\Support\Facades\Redis;

class MasterStatusService
{
    public function updateSlotStatusWithTimeRange(int $masterId, string $startTime, string $endTime, string $status): void
    {
        $startKey = "slot:{$masterId}:start:".strtotime($startTime);
        $endKey = "slot:{$masterId}:end:".strtotime($endTime);

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

    public function isMasterFree(int $id): bool
    {
        $appointmentService = new AppointmentService();
        return $appointmentService->isMasterBusy($id, now()->toDateTimeString());
    }
}
