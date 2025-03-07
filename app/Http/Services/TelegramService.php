<?php

namespace App\Http\Services;

use JsonException;
use NotificationChannels\Telegram\Exceptions\CouldNotSendNotification;
use NotificationChannels\Telegram\TelegramMessage;

/**
 * Class TelegramService
 *
 * This service class provides functionality to send messages to a Telegram chat using the Telegram Bot API.
 */
class TelegramService
{
    /**
     * Sends a message to a specified Telegram chat.
     *
     * @param  string  $message  The message content to be sent.
     *
     * @throws CouldNotSendNotification If the notification could not be sent.
     * @throws JsonException If there is an error encoding the message to JSON.
     */
    public static function sendTelegramMessage(string $message): void
    {
        TelegramMessage::create()
            ->token(config('services.telegram-bot-api.token'))
            ->to(config('services.telegram-bot-api.chat_id'))
            ->content($message)
            ->send();
    }
}
