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

namespace AdvisingApp\Notification\Notifications\Channels;

use AdvisingApp\Notification\Actions\MakeOutboundDeliverable;
use AdvisingApp\Notification\DataTransferObjects\DatabaseChannelResultData;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Notification\Enums\NotificationDeliveryStatus;
use AdvisingApp\Notification\Models\DatabaseMessage;
use AdvisingApp\Notification\Notifications\Contracts\HasAfterSendHook;
use AdvisingApp\Notification\Notifications\Contracts\HasBeforeSendHook;
use AdvisingApp\Notification\Notifications\Contracts\OnDemandNotification;
use App\Features\MessagesAndMessageEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Channels\DatabaseChannel as BaseDatabaseChannel;
use Illuminate\Notifications\Notification;
use Throwable;

class DatabaseChannel extends BaseDatabaseChannel
{
    public function send($notifiable, Notification $notification): void
    {
        [$recipientId, $recipientType] = match (true) {
            $notifiable instanceof Model => [$notifiable->getKey(), $notifiable->getMorphClass()],
            $notifiable instanceof AnonymousNotifiable && $notification instanceof OnDemandNotification => $notification->identifyRecipient(),
            default => [null, 'anonymous'],
        };

        $databaseMessage = MessagesAndMessageEvents::active()
            ? new DatabaseMessage([
                'notification_class' => $notification::class,
                'content' => $notification->toDatabase($notifiable),
                'recipient_id' => $recipientId,
                'recipient_type' => $recipientType,
            ])
            : null;

        $deliverable = app(MakeOutboundDeliverable::class)->execute($notification, $notifiable, NotificationChannel::Database);

        if ($notification instanceof HasBeforeSendHook) {
            $notification->beforeSend(
                notifiable: $notifiable,
                deliverable: $deliverable,
                message: $databaseMessage,
                channel: NotificationChannel::Database
            );
        }

        $deliverable->save();

        try {
            $notificationModel = parent::send($notifiable, $notification);

            $result = new DatabaseChannelResultData(
                success: true,
            );

            try {
                $deliverable->markDeliverySuccessful();

                if ($databaseMessage) {
                    $databaseMessage->notification_id = $notificationModel->getKey();

                    $databaseMessage->save();
                }

                // Consider dispatching this as a seperate job so that it can be encapsulated to be retried if it fails, but also avoid changing the status of the deliverable if it fails.
                if ($notification instanceof HasAfterSendHook) {
                    $notification->afterSend($notifiable, $deliverable, $result, $databaseMessage);
                }
            } catch (Throwable $exception) {
                report($exception);
            }
        } catch (Throwable $exception) {
            $deliverable->update([
                'delivery_status' => NotificationDeliveryStatus::DispatchFailed,
            ]);

            throw $exception;
        }
    }
}
