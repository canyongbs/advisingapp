<?php

namespace Assist\IntegrationTwilio\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Twilio\TwiML\MessagingResponse;
use App\Http\Controllers\Controller;
use Assist\Webhook\Enums\InboundWebhookSource;
use Assist\Webhook\Actions\StoreInboundWebhook;
use Assist\IntegrationTwilio\Actions\TwilioWebhookProcessor;

class TwilioInboundWebhookController extends Controller
{
    // https://www.twilio.com/docs/usage/webhooks/getting-started-twilio-webhooks
    public function __invoke(string $event, Request $request, StoreInboundWebhook $storeInboundWebhook): JsonResponse
    {
        $data = $request->all();

        $storeInboundWebhook->handle(
            InboundWebhookSource::TWILIO,
            $event,
            $request->url(),
            json_encode($data)
        );

        TwilioWebhookProcessor::dispatchToHandler($event, $data);

        // TODO We need a method to generate the response based on the event,
        // because it might be different - for example, Message requires TwiML
        // While a status update might just require a 200 OK
        return new MessagingResponse();
    }
}
