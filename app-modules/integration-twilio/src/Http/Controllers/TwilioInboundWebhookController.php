<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\IntegrationTwilio\Http\Controllers;

use AdvisingApp\IntegrationTwilio\Actions\TwilioWebhookProcessor;
use AdvisingApp\IntegrationTwilio\DataTransferObjects\TwilioMessageReceivedData;
use AdvisingApp\IntegrationTwilio\DataTransferObjects\TwilioStatusCallbackData;
use AdvisingApp\Webhook\Actions\StoreInboundWebhook;
use AdvisingApp\Webhook\Enums\InboundWebhookSource;
use AdvisingApp\Webhook\Exceptions\UnknownInboundWebhookEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Twilio\TwiML\MessagingResponse;

class TwilioInboundWebhookController extends Controller
{
    public function __invoke(string $event, Request $request, StoreInboundWebhook $storeInboundWebhook): MessagingResponse|Response
    {
        $data = match ($event) {
            'message_received' => TwilioMessageReceivedData::fromRequest($request),
            'status_callback' => TwilioStatusCallbackData::fromRequest($request),
            default => throw new UnknownInboundWebhookEvent()
        };

        $storeInboundWebhook->handle(
            InboundWebhookSource::Twilio,
            $event,
            $request->url(),
            $data->toArray()
        );

        TwilioWebhookProcessor::dispatchToHandler($event, $data);

        return TwilioWebhookProcessor::generateResponse($event);
    }
}
