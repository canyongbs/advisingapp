<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
