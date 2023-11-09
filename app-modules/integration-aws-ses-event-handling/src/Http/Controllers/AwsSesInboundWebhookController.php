<?php

namespace Assist\IntegrationAwsSesEventHandling\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Assist\IntegrationAwsSesEventHandling\Events\SesOpenEvent;
use Assist\IntegrationAwsSesEventHandling\Events\SesSendEvent;
use Assist\IntegrationAwsSesEventHandling\Events\SesClickEvent;
use Assist\IntegrationAwsSesEventHandling\Events\SesBounceEvent;
use Assist\IntegrationAwsSesEventHandling\Events\SesRejectEvent;
use Assist\IntegrationAwsSesEventHandling\Events\SesDeliveryEvent;
use Assist\IntegrationAwsSesEventHandling\Events\SesComplaintEvent;
use Assist\IntegrationAwsSesEventHandling\Events\SesSubscriptionEvent;
use Assist\IntegrationAwsSesEventHandling\Events\SesDeliveryDelayEvent;
use Assist\IntegrationAwsSesEventHandling\Events\SesRenderingFailureEvent;
use Assist\IntegrationAwsSesEventHandling\DataTransferObjects\SesEventData;

class AwsSesInboundWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = SesEventData::fromRequest($request);

        match ($data->eventType) {
            'Bounce' => SesBounceEvent::dispatch($data),
            'Complaint' => SesComplaintEvent::dispatch($data),
            'Delivery' => SesDeliveryEvent::dispatch($data),
            'Send' => SesSendEvent::dispatch($data),
            'Reject' => SesRejectEvent::dispatch($data),
            'Open' => SesOpenEvent::dispatch($data),
            'Click' => SesClickEvent::dispatch($data),
            'Rendering Failure' => SesRenderingFailureEvent::dispatch($data),
            'DeliveryDelay' => SesDeliveryDelayEvent::dispatch($data),
            'Subscription' => SesSubscriptionEvent::dispatch($data),
            default => throw new Exception('Unknown AWS SES event type'),
        };

        return response(status: 200);
    }
}
