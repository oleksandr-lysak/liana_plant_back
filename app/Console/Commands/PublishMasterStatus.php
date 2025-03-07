<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class PublishMasterStatus extends Command
{
    /**
     * Назва та підпис команди.
     *
     * @var string
     */
    protected $signature = 'master:publish-status {master_id} {status}';

    /**
     * Опис команди.
     *
     * @var string
     */
    protected $description = 'Публікує статус майстра в Redis для тестування';

    /**
     * Виконання команди.
     *
     * @return void
     */
    public function handle()
    {
        // Отримуємо параметри з консолі
        $masterId = $this->argument('master_id');
        $status = $this->argument('status');

        // Створюємо дані для публікації
        $statusData = [
            'master_id' => $masterId,
            'status' => $status,  // 'вільний' або 'зайнятий'
        ];

        // Публікуємо в Redis
        $res = Redis::publish('master_status_channel', json_encode($statusData));
        dump($res);
        // Виводимо повідомлення
        $this->info("Статус майстра $masterId (статус: $status) було опубліковано в Redis.");
    }
}
