<?php

namespace Assist\IntegrationTwilio\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Twilio\TwiML\MessagingResponse;
use App\Http\Controllers\Controller;
use Assist\Webhook\Enums\InboundWebhookSource;
use Assist\Webhook\Actions\StoreInboundWebhook;
use Assist\IntegrationTwilio\Actions\TwilioWebhookProcessor;

class TwilioInboundWebhookController extends Controller
{
    public function __invoke(string $event, Request $request, StoreInboundWebhook $storeInboundWebhook): MessagingResponse|Response
    {
        $data = $request->all();

        $storeInboundWebhook->handle(
            InboundWebhookSource::Twilio,
            $event,
            $request->url(),
            json_encode($data)
        );

        TwilioWebhookProcessor::dispatchToHandler($event, $data);

        return TwilioWebhookProcessor::generateResponse($event);
    }
}
