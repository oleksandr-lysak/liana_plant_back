<?php

use App\Console\Commands\SyncRedisCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('sync:redis', function () {
    $this->call(SyncRedisCommand::class);
});
