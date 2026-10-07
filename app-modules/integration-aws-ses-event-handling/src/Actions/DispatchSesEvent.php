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

namespace AdvisingApp\IntegrationAwsSesEventHandling\Actions;

use AdvisingApp\IntegrationAwsSesEventHandling\DataTransferObjects\SesEventData;
use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesBounceEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesClickEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesComplaintEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesDeliveryDelayEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesDeliveryEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesOpenEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesRejectEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesRenderingFailureEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesSendEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesSubscriptionEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Exceptions\CouldNotFindTenantFromData;
use AdvisingApp\Notification\Models\EmailMessageEvent;
use App\Features\SesEventDeduplicationFeature;
use App\Models\Tenant;
use Exception;
use Illuminate\Support\Str;

class DispatchSesEvent
{
    /**
     * Every delivery of one SNS message carries the same id, so the listeners record it to skip repeated deliveries.
     *
     * @throws CouldNotFindTenantFromData
     */
    public function __invoke(SesEventData $data, ?string $snsMessageId = null): void
    {
        $tenantId = data_get($data->mail->tags, 'tenant_id.0');

        $tenant = Str::isUuid($tenantId) ? Tenant::query()->find($tenantId) : null;

        if (! $tenant instanceof Tenant) {
            throw new CouldNotFindTenantFromData($data);
        }

        $tenant->execute(function () use ($data, $snsMessageId) {
            if (
                filled($snsMessageId)
                && SesEventDeduplicationFeature::active()
                && EmailMessageEvent::query()->where('sns_message_id', $snsMessageId)->exists()
            ) {
                return;
            }

            match ($data->eventType) {
                'Bounce' => SesBounceEvent::dispatch($data, $snsMessageId),
                'Click' => SesClickEvent::dispatch($data, $snsMessageId),
                'Complaint' => SesComplaintEvent::dispatch($data, $snsMessageId),
                'Delivery' => SesDeliveryEvent::dispatch($data, $snsMessageId),
                'DeliveryDelay' => SesDeliveryDelayEvent::dispatch($data, $snsMessageId),
                'Open' => SesOpenEvent::dispatch($data, $snsMessageId),
                'Reject' => SesRejectEvent::dispatch($data, $snsMessageId),
                'RenderingFailure' => SesRenderingFailureEvent::dispatch($data, $snsMessageId),
                'Send' => SesSendEvent::dispatch($data, $snsMessageId),
                'Subscription' => SesSubscriptionEvent::dispatch($data, $snsMessageId),
                default => throw new Exception('Unknown AWS SES event type'),
            };
        });
    }
}
