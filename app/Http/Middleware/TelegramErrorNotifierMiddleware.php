<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Api\V1\TelegramController;
use Closure;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TelegramErrorNotifierMiddleware
{
    /**
     * @throws Exception
     */
    public function handle($request, Closure $next): mixed
    {
        try {
            $response = $next($request);
            if ($response->status() == 500) {
                $this->notifyTelegram($response->exception, $request);
            } elseif ($response->status() == 404) {
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

    private function notifyTelegram($exception, $request): void
    {
        TelegramController::toTelegram($exception, $request);
    }
}
