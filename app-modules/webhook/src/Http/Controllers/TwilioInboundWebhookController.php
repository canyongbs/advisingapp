<?php

namespace Assist\Webhook\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Assist\Webhook\Enums\InboundWebhookSource;
use Assist\Webhook\Actions\StoreInboundWebhook;
use Twilio\TwiML\MessagingResponse;

class TwilioInboundWebhookController extends Controller
{
    // https://www.twilio.com/docs/usage/webhooks/getting-started-twilio-webhooks
    public function __invoke(string $event, Request $request, StoreInboundWebhook $storeInboundWebhook): JsonResponse
    {
        // TODO Determine exactly how we need to process the incoming data
        $data = json_decode($request->getContent(), true);

        $storeInboundWebhook->handle(
            InboundWebhookSource::TWILIO,
            $event,
            $request->url(),
            json_encode($data)
        );

        // TODO Process the event in some way, shape, or form

        new MessagingResponse();
        return response()->json();
    }
}
