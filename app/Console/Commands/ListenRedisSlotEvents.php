<?php

namespace App\Console\Commands;

use App\Events\SlotStatusChanged;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ListenRedisSlotEvents extends Command
{
    protected $signature = 'redis:listen-slots';

    protected $description = 'Listens to Redis events for time slot start and end';

    public function handle(): void
    {
        Redis::subscribe(['master_status_channel'], function ($message) {
            // Обробка отриманого повідомлення
            // Наприклад, перевірка статусу майстра і відправка push-повідомлення
            $data = json_decode($message, true);
            dump($data);
            //            $this->sendFCMNotification($data['master_id'], $data['status']);
        });

        exit();
        // Enable Keyspace Notifications, if not already enabled
        Redis::command('config', ['set', 'notify-keyspace-events', 'Ex']);

        Redis::psubscribe(['__keyevent@0__:expired'], function ($message) {
            if (str_contains($message, 'slot:')) {
                $this->processSlotEvent($message);
            }
        });
    }

    private function processSlotEvent(string $key): void
    {
        preg_match('/slot:(\d+):(start|end):(\d+)/', $key, $matches);
        if (! $matches) {
            return;
        }

        $masterId = $matches[1];
        $eventType = $matches[2]; // start or end
        $timestamp = $matches[3];

        Log::info("Redis event: Timeslot $eventType for master $masterId at $timestamp");

        // Send Laravel event
        event(new SlotStatusChanged($masterId, $eventType));
    }
}
