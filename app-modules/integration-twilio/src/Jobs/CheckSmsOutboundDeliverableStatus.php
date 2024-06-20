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

namespace AdvisingApp\IntegrationTwilio\Jobs;

use Carbon\Carbon;
use Twilio\Rest\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Twilio\Exceptions\TwilioException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use AdvisingApp\Engagement\Models\EngagementDeliverable;
use AdvisingApp\Notification\Models\OutboundDeliverable;

class CheckSmsOutboundDeliverableStatus implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public OutboundDeliverable $deliverable,
    ) {}

    public function handle(Client $twilioClient): void
    {
        try {
            $messageInstance = $twilioClient->messages($this->deliverable->external_reference_id)->fetch();

            $this->deliverable->update([
                'external_status' => $messageInstance->status,
            ]);

            if ($this->deliverable->related && $this->deliverable->related instanceof EngagementDeliverable) {
                $this->deliverable->related->update([
                    'external_status' => $messageInstance->status,
                ]);

                match ($messageInstance->status) {
                    'delivered' => $this->deliverable->related->markDeliverySuccessful(Carbon::parse($messageInstance->dateSent)),
                    'undelivered', 'failed' => $this->deliverable->related->markDeliveryFailed($messageInstance->errorMessage ?? 'Message could not successfully be delivered.'),
                    default => null,
                };
            }

            match ($messageInstance->status) {
                'delivered' => $this->deliverable->markDeliverySuccessful(Carbon::parse($messageInstance->dateSent)),
                'undelivered', 'failed' => $this->deliverable->markDeliveryFailed($messageInstance->errorMessage ?? 'Message could not successfully be delivered.'),
                default => null,
            };
        } catch (TwilioException $e) {
            report($e);
        }
    }
}
