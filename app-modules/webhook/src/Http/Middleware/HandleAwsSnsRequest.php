<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Webhook\Http\Middleware;

use AdvisingApp\Webhook\Actions\StoreInboundWebhook;
use AdvisingApp\Webhook\DataTransferObjects\SnsMessage;
use AdvisingApp\Webhook\Enums\InboundWebhookSource;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleAwsSnsRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $data = SnsMessage::fromRequest($request);

        app(StoreInboundWebhook::class)
            ->handle(
                InboundWebhookSource::AwsSns,
                in_array($data->type, ['SubscriptionConfirmation', 'UnsubscribeConfirmation', 'Notification']) ? $data->type : 'UnknownSnsType',
                $request->url(),
                $request->getContent()
            );

        if ($data->type === 'SubscriptionConfirmation') {
            if (app()->environment('testing')) {
                return response(status: 200);
            }

            file_get_contents($data->subscribeURL);

            return response(status: 200);
        }

        if ($data->type === 'UnsubscribeConfirmation') {
            return response(status: 200);
        }

        if ($data->type !== 'Notification') {
            throw new Exception('Unknown AWS SNS webhook type');
        }

        return $next($request);
    }
}
