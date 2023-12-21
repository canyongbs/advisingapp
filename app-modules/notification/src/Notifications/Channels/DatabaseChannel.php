<?php

namespace AdvisingApp\Notification\Notifications\Channels;

use Illuminate\Notifications\Notification;
use AdvisingApp\Notification\Models\OutboundDeliverable;
use AdvisingApp\Notification\Notifications\BaseNotification;
use AdvisingApp\Notification\DataTransferObjects\EmailChannelResultData;
use AdvisingApp\Notification\DataTransferObjects\NotificationResultData;
use AdvisingApp\Notification\DataTransferObjects\DatabaseChannelResultData;
use Illuminate\Notifications\Channels\DatabaseChannel as BaseDatabaseChannel;

class DatabaseChannel extends BaseDatabaseChannel
{
    public function send($notifiable, Notification $notification): void
    {
        /** @var BaseNotification $notification */
        $deliverable = $notification->beforeSend($notifiable, DatabaseChannel::class);

        if ($deliverable === false) {
            // Do anything else we need to to notify sending party that notification was not sent
            return;
        }

        $result = $this->handle($notifiable, $notification);

        $notification->afterSend($notifiable, $deliverable, $result);
    }

    public function handle(object $notifiable, BaseNotification $notification): NotificationResultData
    {
        parent::send($notifiable, $notification);

        return new DatabaseChannelResultData(
            success: true,
        );
    }

    public static function afterSending(object $notifiable, OutboundDeliverable $deliverable, EmailChannelResultData $result): void {}
}
