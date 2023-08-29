<?php

namespace Assist\IntegrationTwilio\Actions;

use Illuminate\Http\Response;
use Twilio\TwiML\MessagingResponse;

class TwilioWebhookProcessor
{
    public static function dispatchToHandler(string $event, array $data): void
    {
        match ($event) {
            'message_received' => MessageReceived::dispatch($data),
            'status_callback' => StatusCallback::dispatch($data),
        };
    }

    public static function generateResponse(string $event): MessagingResponse|Response
    {
        return match ($event) {
            'message_received' => new MessagingResponse(),
            'status_callback' => response(),
        };
    }
}
