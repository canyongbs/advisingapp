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

namespace AdvisingApp\Notification\Notifications;

use Exception;
use App\Models\Tenant;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use AdvisingApp\Notification\Models\OutboundDeliverable;
use AdvisingApp\Notification\Actions\MakeOutboundDeliverable;
use AdvisingApp\Notification\Notifications\Channels\SmsChannel;
use AdvisingApp\Notification\Notifications\Channels\EmailChannel;
use AdvisingApp\Notification\Notifications\Concerns\ChannelTrait;
use AdvisingApp\Notification\Notifications\Channels\DatabaseChannel;
use AdvisingApp\Notification\DataTransferObjects\SmsChannelResultData;
use AdvisingApp\Notification\DataTransferObjects\EmailChannelResultData;
use AdvisingApp\Notification\DataTransferObjects\NotificationResultData;
use AdvisingApp\Notification\DataTransferObjects\DatabaseChannelResultData;

abstract class BaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected array $metadata = [];

    public $tries = 3;

    public function via(object $notifiable): array
    {
        $traits = collect(class_uses_recursive(static::class));

        return $traits
            ->filter(function ($traitName) {
                return in_array(ChannelTrait::class, class_uses($traitName));
            })
            ->map(function ($traitName) {
                $channelName = Str::before(class_basename($traitName), 'ChannelTrait');
                $methodName = 'get' . Str::studly($channelName) . 'Channel';

                if (method_exists($traitName, $methodName)) {
                    return forward_static_call([$traitName, $methodName]);
                }

                return null;
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    public function viaQueues(): array
    {
        return [
            DatabaseChannel::class => config('queue.outbound_communication_queue'),
            EmailChannel::class => config('queue.outbound_communication_queue'),
            SmsChannel::class => config('queue.outbound_communication_queue'),
        ];
    }

    public function beforeSend(object $notifiable, string $channel): OutboundDeliverable|false
    {
        $deliverable = resolve(MakeOutboundDeliverable::class)->handle($this, $notifiable, $channel);

        $this->beforeSendHook($notifiable, $deliverable, $channel);

        $deliverable->save();

        $this->metadata = [
            'outbound_deliverable_id' => $deliverable->id,
        ];

        if (Tenant::checkCurrent()) {
            $this->metadata['tenant_id'] = Tenant::current()->getKey();
        }

        return $deliverable;
    }

    public function afterSend(object $notifiable, OutboundDeliverable $deliverable, NotificationResultData $result): void
    {
        match (true) {
            $result instanceof SmsChannelResultData => SmsChannel::afterSending($notifiable, $deliverable, $result),
            $result instanceof EmailChannelResultData => EmailChannel::afterSending($notifiable, $deliverable, $result),
            $result instanceof DatabaseChannelResultData => DatabaseChannel::afterSending($notifiable, $deliverable, $result),
            default => throw new Exception('Invalid notification result data.'),
        };

        $this->afterSendHook($notifiable, $deliverable);
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    protected function beforeSendHook(object $notifiable, OutboundDeliverable $deliverable, string $channel): void {}

    protected function afterSendHook(object $notifiable, OutboundDeliverable $deliverable): void {}
}
