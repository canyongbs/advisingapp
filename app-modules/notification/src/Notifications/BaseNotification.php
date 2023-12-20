<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Notification\Notifications;

use ReflectionClass;
use ReflectionProperty;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use AdvisingApp\Notification\Models\OutboundDeliverable;
use AdvisingApp\Notification\Enums\NotificationDeliveryStatus;
use AdvisingApp\Notification\Actions\CreateOutboundDeliverable;
use AdvisingApp\Notification\Notifications\Concerns\ChannelTrait;
use AdvisingApp\Notification\DataTransferObjects\SmsChannelResultData;
use AdvisingApp\Notification\DataTransferObjects\NotificationResultData;

abstract class BaseNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use Dispatchable;

    public array $channels = [];

    public function via(object $notifiable): array
    {
        $reflectionClass = new ReflectionClass($this);

        return collect($reflectionClass->getTraits())
            ->filter(function (ReflectionClass $trait) {
                return collect($trait->getTraits())
                    ->contains(fn (ReflectionClass $nestedTrait) => $nestedTrait->getName() === ChannelTrait::class);
            })

            ->map(function (ReflectionClass $trait) {
                return collect($trait->getProperties())
                    ->filter(function (ReflectionProperty $property) {
                        return $property->getName() === 'channel';
                    })
                    ->map(function (ReflectionProperty $property) {
                        return $property->getValue();
                    });
            })
            ->flatten()
            ->toArray();
    }

    public function beforeSend(object $notifiable, string $channel): OutboundDeliverable|false
    {
        $deliverable = resolve(CreateOutboundDeliverable::class)->handle($this, $notifiable, $channel);

        $this->beforeSendHook($notifiable, $deliverable, $channel);

        // TODO Implement some kind of checker against the rate limits for a particular channel
        // Reminder that we'll need to consider "must send" notifications, which may be those sent
        // To a sending party letting them know they've hit a quota or been throttled, etc...
        // if (! $this->checkRateLimitsFor($channel)) {
        //     $deliverable->update([
        //         'delivery_status' => NotificationDeliveryStatus::RateLimited,
        //     ]);

        //     return false;
        // }

        return $deliverable;
    }

    public function afterSend(object $notifiable, OutboundDeliverable $deliverable, NotificationResultData $result): void
    {
        match (true) {
            $result->type instanceof SmsChannelResultData => $this->afterSendSms($notifiable, $deliverable, $result->type),
            default => throw new \Exception('Invalid notification result data.'),
        };

        $this->afterSendHook($notifiable, $deliverable);
    }

    protected function afterSendSms(object $notifiable, OutboundDeliverable $deliverable, SmsChannelResultData $result): void
    {
        if ($result->success) {
            $deliverable->update([
                'external_reference_id' => $result->message->sid,
                'external_status' => $result->message->status,
                'delivery_status' => NotificationDeliveryStatus::Successful,
            ]);
        } else {
            $deliverable->update([
                'delivery_status' => NotificationDeliveryStatus::Failed,
                'delivery_response' => $result->error,
            ]);
        }
    }

    protected function beforeSendHook(object $notifiable, OutboundDeliverable $deliverable, string $channel): void {}

    protected function afterSendHook(object $notifiable, OutboundDeliverable $deliverable): void {}
}
