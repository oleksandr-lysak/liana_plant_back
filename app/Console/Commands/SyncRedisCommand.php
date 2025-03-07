<?php

namespace App\Console\Commands;

use App\Http\Services\Master\MasterStatusService;
use App\Models\Master;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncRedisCommand extends Command
{
    protected $signature = 'sync:redis';

    protected $description = 'Synchronize Redis with MySQL data for master statuses';

    public function handle()
    {
        $masterStatusService = new MasterStatusService;
        $masters = Master::all();

        foreach ($masters as $master) {
            $masterStatusService->rebuildCacheForMaster($master->id);
        }
        Log::debug('Redis synchronization completed.');
        $this->info('Redis synchronization completed.');
    }
}
