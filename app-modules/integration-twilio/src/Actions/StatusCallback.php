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

namespace AdvisingApp\IntegrationTwilio\Actions;

use AdvisingApp\IntegrationTwilio\DataTransferObjects\TwilioStatusCallbackData;
use AdvisingApp\Notification\Actions\UpdateOutboundDeliverableSmsStatus;
use AdvisingApp\Notification\Events\CouldNotFindOutboundDeliverableFromExternalReference;
use AdvisingApp\Notification\Models\OutboundDeliverable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StatusCallback implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public TwilioStatusCallbackData $data
    ) {}

    public function handle(): void
    {
        $outboundDeliverable = OutboundDeliverable::query()
            ->where('external_reference_id', $this->data->messageSid)
            ->first();

        if (is_null($outboundDeliverable)) {
            CouldNotFindOutboundDeliverableFromExternalReference::dispatch($this->data);

            return;
        }

        // TODO In order to potentially reduce the amount of noise from jobs, we might want to introduce a "screener" that eliminates certain jobs based on their status
        // And only run the update if it's a status that we want to run some type of update against. For instance, we will receive callbacks for
        // queued, sending, sent, etc... but we don't actually want/need to do anything during these lifecycle hooks. We only really care about
        // delivered, undelivered, failed, etc... statuses.
        // https://canyongbs.atlassian.net/browse/ADVAPP-113

        UpdateOutboundDeliverableSmsStatus::dispatch($outboundDeliverable, $this->data);
    }
}
