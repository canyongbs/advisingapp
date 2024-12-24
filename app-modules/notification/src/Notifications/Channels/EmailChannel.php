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

use AdvisingApp\Engagement\Exceptions\InvalidNotificationTypeInChannel;
use AdvisingApp\Notification\Actions\MakeOutboundDeliverable;
use AdvisingApp\Notification\DataTransferObjects\EmailChannelResultData;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Notification\Enums\NotificationDeliveryStatus;
use AdvisingApp\Notification\Exceptions\NotificationQuotaExceeded;
use AdvisingApp\Notification\Models\OutboundDeliverable;
use AdvisingApp\Notification\Notifications\BaseNotification;
use AdvisingApp\Notification\Notifications\Channels\Contracts\NotificationChannelInterface;
use AdvisingApp\Notification\Notifications\EmailNotification;
use App\Models\Tenant;
use App\Models\User;
use App\Settings\LicenseSettings;
use Exception;
use Illuminate\Notifications\Channels\MailChannel;
use Illuminate\Notifications\Notification;
use Throwable;

class EmailChannel extends MailChannel implements NotificationChannelInterface
{
    public function send($notifiable, Notification $notification): void
    {
        $deliverable = resolve(MakeOutboundDeliverable::class)->handle($notification, $notifiable, NotificationChannel::Email);

        /** @var BaseNotification $notification */
        $notification->beforeSend($notifiable, $deliverable, NotificationChannel::Email);

        $deliverable->save();

        try {
            throw_if(! $notification instanceof EmailNotification || ! $notification instanceof BaseNotification, new InvalidNotificationTypeInChannel());

            $notification->metadata['outbound_deliverable_id'] = $deliverable->id;

            if (Tenant::checkCurrent()) {
                $notification->metadata['tenant_id'] = Tenant::current()->getKey();
            }

            throw_if(! $this->canSendWithinQuotaLimits($notification, $notifiable), new NotificationQuotaExceeded());

            $result = $this->handle($notifiable, $notification);

            try {
                if ($result->success) {
                    $demoMode = Tenant::current()?->config->mail->isDemoModeEnabled ?? false;

                    $deliverable->update([
                        'delivery_status' => ! $demoMode
                            ? NotificationDeliveryStatus::Dispatched
                            : NotificationDeliveryStatus::Successful,
                        'quota_usage' => ! $demoMode
                            ? self::determineQuotaUsage($result->recipients)
                            : 0,
                    ]);
                } else {
                    $deliverable->update([
                        'delivery_status' => NotificationDeliveryStatus::DispatchFailed,
                    ]);
                }

                // Consider dispatching this as a seperate job so that it can be encapsulated to be retried if it fails, but also avoid changing the status of the deliverable if it fails
                $notification->afterSend($notifiable, $deliverable, $result);
            } catch (Throwable $e) {
                report($e);
            }
        } catch (NotificationQuotaExceeded $e) {
            $deliverable->update(['delivery_status' => NotificationDeliveryStatus::RateLimited]);
        } catch (Throwable $e) {
            $deliverable->update([
                'delivery_status' => NotificationDeliveryStatus::DispatchFailed,
            ]);

            throw $e;
        }
    }

    public function handle(object $notifiable, BaseNotification $notification): EmailChannelResultData
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

    public static function determineQuotaUsage(array $recipients): int
    {
        return collect($recipients)->filter(function ($recipient) {
            $user = User::with('roles')->where('email', $recipient->getAddress())->first();

            return ! $user || ! $user->isSuperAdmin();
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

            if ($recipient instanceof User && $recipient->isSuperAdmin()) {
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
