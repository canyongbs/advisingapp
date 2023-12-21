<?php

namespace AdvisingApp\Notification\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Channels\MailChannel;
use AdvisingApp\Notification\Models\OutboundDeliverable;
use AdvisingApp\Notification\Notifications\BaseNotification;
use AdvisingApp\Notification\DataTransferObjects\EmailChannelResultData;
use AdvisingApp\Notification\DataTransferObjects\NotificationResultData;

class EmailChannel extends MailChannel
{
    public function send($notifiable, Notification $notification): void
    {
        /** @var BaseNotification $notification */
        $deliverable = $notification->beforeSend($notifiable, EmailChannel::class);

        if ($deliverable === false) {
            // Do anything else we need to to notify sending party that notification was not sent
            return;
        }

        $result = $this->handle($notifiable, $notification);

        $notification->afterSend($notifiable, $deliverable, $result);
    }

    public function handle(object $notifiable, BaseNotification $notification): NotificationResultData
    {
        $result = new EmailChannelResultData(
            success: false,
        );

        $message = parent::send($notifiable, $notification);

        if (! is_null($message)) {
            $result->success = true;
        }

        return $result;
    }

    public static function afterSending(object $notifiable, OutboundDeliverable $deliverable, EmailChannelResultData $result): void
    {
        // TODO Do we want to add any updating of the deliverable here?
        // Or do we want to leave it all for SES events handled via webhook?
    }
}
