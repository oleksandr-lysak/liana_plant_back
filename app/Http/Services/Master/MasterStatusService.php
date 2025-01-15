<?php

namespace App\Http\Services\Master;

use App\Enums\TimeSlotStatus;
use App\Models\TimeSlot;
use Illuminate\Support\Facades\Redis;

class MasterStatusService
{
    const REDIS_KEY_PREFIX = 'master_status:';
    const REDIS_SLOT_PREFIX = 'master_slots:';

    public function updateStatus(int $masterId, string $status): void
    {
        Redis::set(self::REDIS_KEY_PREFIX . $masterId, $status);
        Redis::publish('master_status_channel', json_encode([
            'master_id' => $masterId,
            'status' => $status,
        ]));
    }

    public function updateSlotStatusWithTTL(int $slotId, ?int $clientId = null): void
    {
        $slot = TimeSlot::find($slotId);
        $masterId = $slot->master_id;
        $slotKey = self::REDIS_SLOT_PREFIX . $slotId;
        $slotStatus = $clientId === null ? TimeSlotStatus::Free->value : TimeSlotStatus::Booked->value;

        $slotData = [
            'status' => $slotStatus,
            'client_id' => $clientId,
        ];

        $ttl = $this->calculateTTL($slot);
        Redis::setex($slotKey, $ttl, json_encode($slotData));

        $this->updateMasterStatus($masterId);
    }

    private function calculateTTL(TimeSlot $slot): int
    {
        $currentTimestamp = now()->timestamp;
        $endTimestamp = strtotime($slot->end_time);
        return max(0, $endTimestamp - $currentTimestamp);
    }

    public function updateMasterStatus(int $masterId): void
    {
        $slots = TimeSlot::where('master_id', $masterId)->get();
        $status = TimeSlotStatus::Free->value;

        foreach ($slots as $slot) {
            $slotKey = self::REDIS_SLOT_PREFIX . $slot->id;
            $slotData = Redis::get($slotKey);

            if ($slotData) {
                $slotData = json_decode($slotData, true);
                if ($slotData['status'] === TimeSlotStatus::Booked->value) {
                    $status = TimeSlotStatus::Booked->value;
                    break;
                }
            }
        }

        $this->updateStatus($masterId, $status);
    }

    public function isMasterFree(int $masterId): bool
    {
        $status = Redis::get(self::REDIS_KEY_PREFIX . $masterId);

        if ($status === null) {
            return false;
        }

        return $status === TimeSlotStatus::Free->value;
    }
}
