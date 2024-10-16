<?php

namespace App\Http\Middleware;

use App\Http\Controllers\TelegramController;
use Closure;
use Exception;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Telegram\TelegramMessage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Telegram\Bot\Laravel\Facades\Telegram;
use TelegramHelper;

class TelegramErrorNotifierMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle($request, Closure $next): mixed
    {
        try {
            $response = $next($request);
            if ($response->status() == 500) {
                $this->notifyTelegram($response->exception, $request);
            } else if ($response->status() == 404) {
                $this->notifyTelegram($response->exception, $request);
            }
            return $response;
        } catch (NotFoundHttpException $exception) {
            $this->notifyTelegram($exception, $request);
            throw $exception;
        } catch (Exception $exception) {
            $this->notifyTelegram($exception, $request);
            throw $exception;
        }
    }

    /**
     * @param $exception
     * @return void
     */
    private function notifyTelegram($exception, $request): void
    {
        TelegramController::toTelegram($exception, $request);
    }
}
