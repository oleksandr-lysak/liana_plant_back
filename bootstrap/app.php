<?php

use App\Console\Commands\ClearExpiredAppointmentsRedis;
use App\Console\Commands\GenerateSlugForMasters;
use App\Console\Commands\SyncAppointmentsRedis;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\SetLocaleWeb;
use App\Http\Services\TelegramService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api_v1.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //        $middleware->api(append: [
        //            JwtMiddleware::class,
        //        ]);
        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
            SetLocale::class,
        ]);
        $middleware->api(append: [
            SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function (\Throwable $e) {
            if (app()->bound(TelegramService::class) && app()->environment('production')) {
                app(TelegramService::class)->report($e);
            }
        });
    })
    ->withSchedule(
        function (Schedule $schedule) {
            $schedule->command('app:clear-expired-appointments-redis')->everyTwoHours();
        }
    )
    ->withCommands(
        [
            SyncAppointmentsRedis::class,
            ClearExpiredAppointmentsRedis::class,
            GenerateSlugForMasters::class,
        ]
    )
    ->create();
