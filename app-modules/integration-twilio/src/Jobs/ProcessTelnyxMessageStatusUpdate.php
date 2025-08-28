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

namespace AdvisingApp\IntegrationTwilio\Jobs;

use AdvisingApp\IntegrationAwsSesEventHandling\Exceptions\CouldNotFindSmsMessageFromData;
use AdvisingApp\Notification\Enums\SmsMessageEventType;
use AdvisingApp\Notification\Models\SmsMessage;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessTelnyxMessageStatusUpdate implements ShouldQueue
{
    use Queueable;

    /**
     * @param array{
     *     payload: array{
     *         id: string,
     *         to: array<array{status: string}>
     *     },
     *     occurred_at: string
     * } $data
     */
    public function __construct(
        protected array $data,
    ) {}

    public function handle(): void
    {
        $smsMessage = SmsMessage::query()
            ->where('external_reference_id', $this->data['payload']['id'])
            ->first();

        if (is_null($smsMessage)) {
            report(new CouldNotFindSmsMessageFromData($this->data));

            return;
        }

        $smsMessage->events()->create([
            'type' => match ($this->data['payload']['to'][0]['status']) {
                'queued', 'sending' => SmsMessageEventType::Queued,
                'sent' => SmsMessageEventType::Sent,
                'delivered' => SmsMessageEventType::Delivered,
                'sending_failed', 'delivery_failed', 'delivery_unconfirmed' => SmsMessageEventType::Failed,
                default => throw new Exception('Unknown SMS message status'),
            },
            'payload' => $this->data,
            'occurred_at' => $this->data['occurred_at'],
        ]);
    }
}
