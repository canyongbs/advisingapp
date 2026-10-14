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

namespace AdvisingApp\IntegrationAwsSesEventHandling\Listeners;

use AdvisingApp\IntegrationAwsSesEventHandling\DataTransferObjects\SesEventData;
use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Exceptions\CouldNotFindEmailMessageFromData;
use AdvisingApp\Notification\Enums\EmailMessageEventType;
use AdvisingApp\Notification\Models\EmailMessage;
use App\Features\SesEventDeduplicationFeature;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

abstract class HandleSesEvent implements ShouldQueue
{
    abstract public function handle(SesEvent $event): void;

    protected function getEmailMessageFromData(SesEventData $data): ?EmailMessage
    {
        return EmailMessage::query()
            ->where('id', data_get($data->mail->tags, 'app_message_id'))
            ->first();
    }

    protected function recordEvent(SesEvent $event, EmailMessageEventType $type, mixed $occurredAt): void
    {
        $emailMessage = $this->getEmailMessageFromData($event->data);

        if (is_null($emailMessage)) {
            report(new CouldNotFindEmailMessageFromData($event->data));

            return;
        }

        $attributes = [
            'type' => $type,
            'payload' => $event->data->toArray(),
            'occurred_at' => $occurredAt,
        ];

        if (is_null($event->snsMessageId) || ! SesEventDeduplicationFeature::active()) {
            $emailMessage->events()->create($attributes);

            return;
        }

        try {
            // The savepoint keeps a surrounding transaction usable when a repeated delivery hits the unique index.
            DB::transaction(fn () => $emailMessage->events()->create([
                ...$attributes,
                'sns_message_id' => $event->snsMessageId,
            ]));
        } catch (UniqueConstraintViolationException) {
            // Already recorded by an earlier delivery of the same SNS message.
        }
    }
}
