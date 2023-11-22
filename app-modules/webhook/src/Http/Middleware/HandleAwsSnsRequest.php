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

namespace Assist\Webhook\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Assist\Webhook\Enums\InboundWebhookSource;
use Symfony\Component\HttpFoundation\Response;
use Assist\Webhook\Actions\StoreInboundWebhook;
use Assist\Webhook\DataTransferObjects\SnsMessage;

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
