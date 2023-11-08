<?php

namespace Assist\IntegrationAwsSesEventHandling\Actions;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Twilio\TwiML\MessagingResponse;

class AwsSesWebhookProcessor
{
    public static function dispatchToHandler(string $event, array $data): void
    {
        Log::info('AWS SES event: ' . json_encode($data));

        // Leaving this here for now, we will eventually need to handle these events
        //match ($event) {
        //    'Bounce' => null,
        //    'Complaint' => null,
        //    'Delivery' => null,
        //    'Send' => null,
        //    'Reject' => null,
        //    'Open' => null,
        //    'Click' => null,
        //    'Rendering Failure' => null,
        //    'DeliveryDelay' => null,
        //    'Subscription' => null,
        //    default => throw new Exception('Unknown AWS SES event type'),
        //};
    }

    public static function generateResponse(string $event): MessagingResponse|Response
    {
        return response();
    }
}
