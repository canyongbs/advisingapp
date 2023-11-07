<?php

namespace Assist\IntegrationAwsSesEventHandling\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Assist\IntegrationAwsSesEventHandling\Actions\AwsSesWebhookProcessor;

class AwsSesInboundWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = json_decode(json_decode($request->getContent(), true)['Message'], true);

        $event = $data['eventType'];

        AwsSesWebhookProcessor::dispatchToHandler($event, $data);

        return AwsSesWebhookProcessor::generateResponse($event);
    }
}
