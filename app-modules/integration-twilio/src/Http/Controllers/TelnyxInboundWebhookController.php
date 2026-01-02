<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\IntegrationTwilio\Jobs\ProcessTelnyxMessageReceived;
use AdvisingApp\IntegrationTwilio\Jobs\ProcessTelnyxMessageStatusUpdate;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class TelnyxInboundWebhookController
{
    public function __invoke(Request $request): Response
    {
        $data = $request->toArray()['data'];

        throw_if(! isset($data['record_type']) || $data['record_type'] !== 'event', new Exception('Invalid Telnyx webhook record type'));

        if ($data['event_type'] === 'message.received') {
            dispatch(new ProcessTelnyxMessageReceived($data));

            return response()->noContent();
        }

        if ($data['event_type'] === 'message.sent' || $data['event_type'] === 'message.finalized') {
            dispatch(new ProcessTelnyxMessageStatusUpdate($data));

            return response()->noContent();
        }

        // Did not match any event_type we are currently parsing

        Log::warning('Telnyx event_type not matched', [
            'event_type' => $data['event_type'],
            'payload' => $request->getContent(),
        ]);

        return response()->make(status: 501);
    }
}
