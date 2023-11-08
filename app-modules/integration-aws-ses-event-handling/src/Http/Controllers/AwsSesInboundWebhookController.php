<?php

namespace Assist\IntegrationAwsSesEventHandling\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class AwsSesInboundWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = json_decode(json_decode($request->getContent(), true)['Message'], true);

        $event = $data['eventType'];

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

        return response(status: 200);
    }
}
