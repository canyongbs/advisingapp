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
        logger('TwilioInboundWebhookController');
        logger($data);

        $storeInboundWebhook->handle(
            InboundWebhookSource::TWILIO,
            $event,
            $request->url(),
            json_encode($data)
        );

        TwilioWebhookProcessor::dispatchToHandler($event, $data);

        $generatedResponse = TwilioWebhookProcessor::generateResponse($event);

        logger('generatedResponse');
        logger($generatedResponse);

        return $generatedResponse;
    }
}
