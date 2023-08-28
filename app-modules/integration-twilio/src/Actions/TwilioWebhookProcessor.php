<?php

namespace Assist\IntegrationTwilio\Actions;

class TwilioWebhookProcessor
{
    public static function dispatchToHandler(string $event, array $data): void
    {
        match ($event) {
            'message_received' => MessageReceived::dispatch($data),
            'status_update' => StatusUpdate::dispatch($data),
        };
    }
}
