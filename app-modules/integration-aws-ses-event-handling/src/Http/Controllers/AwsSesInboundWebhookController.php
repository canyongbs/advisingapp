<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\IntegrationAwsSesEventHandling\Http\Controllers;

use AdvisingApp\IntegrationAwsSesEventHandling\Actions\DispatchSesEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\DataTransferObjects\SesEventData;
use AdvisingApp\IntegrationAwsSesEventHandling\Exceptions\CouldNotFindTenantFromData;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// TODO: Cleanup Task (ses-sqs): delete this controller and its route once the HTTP SNS subscription is removed everywhere.
class AwsSesInboundWebhookController extends Controller
{
    public function __invoke(Request $request, DispatchSesEvent $dispatchSesEvent)
    {
        $snsMessageId = $request->json('MessageId');

        try {
            $dispatchSesEvent(SesEventData::fromRequest($request), is_string($snsMessageId) ? $snsMessageId : null);
        } catch (CouldNotFindTenantFromData $exception) {
            report($exception);

            // A 4xx tells SNS not to retry an event that can never be routed.
            return response(status: 404);
        }

        return response(status: 200);
    }
}
