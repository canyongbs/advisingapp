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

namespace AdvisingApp\Notification\Notifications\Channels;

use Exception;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Channels\MailChannel;
use AdvisingApp\Engagement\Models\EngagementDeliverable;
use AdvisingApp\Notification\Models\OutboundDeliverable;
use AdvisingApp\Notification\Notifications\BaseNotification;
use AdvisingApp\Notification\Notifications\EmailNotification;
use AdvisingApp\Notification\Enums\NotificationDeliveryStatus;
use AdvisingApp\Notification\Exceptions\NotificationQuotaExceeded;
use AdvisingApp\Notification\Models\Contracts\NotifiableInterface;
use AdvisingApp\Notification\DataTransferObjects\EmailChannelResultData;
use AdvisingApp\Notification\DataTransferObjects\NotificationResultData;

class EmailChannel extends MailChannel
{
    public function send($notifiable, Notification $notification): void
    {
        /** @var AnonymousNotifiable|NotifiableInterface $notifiable */
        try {
            DB::beginTransaction();

            if (! $notification instanceof EmailNotification) {
                return;
            }

            /** @var BaseNotification $notification */
            $deliverable = $notification->beforeSend($notifiable, EmailChannel::class);

            if (! $this->canSendWithinQuotaLimits($notification, $notifiable)) {
                $deliverable->update(['delivery_status' => NotificationDeliveryStatus::RateLimited]);

                // Do anything else we need to notify sending party that notification was not sent

                if ($deliverable->related instanceof EngagementDeliverable) {
                    $deliverable->related->update(['delivery_status' => NotificationDeliveryStatus::RateLimited]);
                }

                DB::commit();

                throw new NotificationQuotaExceeded();
            }

            $result = $this->handle($notifiable, $notification);

            $notification->afterSend($notifiable, $deliverable, $result);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    public function handle(object $notifiable, BaseNotification $notification): NotificationResultData
    {
        $result = new EmailChannelResultData(
            success: false,
        );

        $message = parent::send($notifiable, $notification);

        if (! is_null($message)) {
            $result->success = true;
            $result->recipients = $message->getEnvelope()->getRecipients();
        }

        return $result;
    }

    public static function afterSending(object $notifiable, OutboundDeliverable $deliverable, EmailChannelResultData $result): void
    {
        if ($result->success) {
            $deliverable->update([
                'delivery_status' => NotificationDeliveryStatus::Dispatched,
                'quota_usage' => self::determineQuotaUsage($result->recipients),
            ]);
        } else {
            $deliverable->update([
                'delivery_status' => NotificationDeliveryStatus::DispatchFailed,
            ]);
        }
    }

    public static function determineQuotaUsage(array $recipients): int
    {
        return collect($recipients)->filter(function ($recipient) {
            $user = User::where('email', $recipient->getAddress())->first();

            return ! $user || ! $user->hasRole('authorization.super_admin');
        })->count();
    }

    public function canSendWithinQuotaLimits(BaseNotification $notification, object $notifiable): bool
    {
        if (! $notification instanceof EmailNotification) {
            throw new Exception('Invalid notification type.');
        }

        $primaryRecipientUsage = 1;

        if ($notification->getMetadata()['outbound_deliverable_id']) {
            $deliverable = OutboundDeliverable::with('recipient')->find($notification->getMetadata()['outbound_deliverable_id']);

            $recipient = $deliverable->recipient;

            if ($recipient instanceof User && $recipient->hasRole('authorization.super_admin')) {
                $primaryRecipientUsage = 0;
            }
        }

        // 1 for the primary recipient, plus the number of cc and bcc recipients
        $estimatedQuotaUsage = $primaryRecipientUsage + count($notification->toMail($notifiable)->cc) + count($notification->toMail($notifiable)->bcc);

        $licenseSettings = app(LicenseSettings::class);

        $resetWindow = $licenseSettings->data->limits->getResetWindow();

        $currentQuotaUsage = OutboundDeliverable::where('channel', 'email')
            ->whereBetween('created_at', [$resetWindow['start'], $resetWindow['end']])
            ->sum('quota_usage');

        return $currentQuotaUsage + $estimatedQuotaUsage <= $licenseSettings->data->limits->emails;
    }
}
