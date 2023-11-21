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
