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

use AdvisingApp\IntegrationAwsSesEventHandling\Settings\SesSettings;
use AdvisingApp\Notification\Actions\MakeOutboundDeliverable;
use AdvisingApp\Notification\DataTransferObjects\EmailChannelResultData;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Notification\Enums\NotificationDeliveryStatus;
use AdvisingApp\Notification\Exceptions\NotificationQuotaExceeded;
use AdvisingApp\Notification\Models\OutboundDeliverable;
use AdvisingApp\Notification\Notifications\HasAfterSendHook;
use AdvisingApp\Notification\Notifications\HasBeforeSendHook;
use App\Models\Tenant;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Notifications\Channels\MailChannel as BaseMailChannel;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Throwable;

class MailChannel extends BaseMailChannel
{
    public function send($notifiable, Notification $notification): void
    {
        $deliverable = app(MakeOutboundDeliverable::class)->execute($notification, $notifiable, NotificationChannel::Email);

        if ($notification instanceof HasBeforeSendHook) {
            $notification->beforeSend($notifiable, $deliverable, NotificationChannel::Email);
        }

        $deliverable->save();

        try {
            $message = $notification->toMail($notifiable)
                ->withSymfonyMessage(function (Email $message) use ($deliverable) {
                    $settings = app(SesSettings::class);

                    if (filled($settings->configuration_set)) {
                        $message->getHeaders()->addTextHeader(
                            'X-SES-CONFIGURATION-SET',
                            $settings->configuration_set
                        );
                    }

                    $message->getHeaders()->addTextHeader(
                        'X-SES-MESSAGE-TAGS',
                        implode(', ', [
                            "outbound_deliverable_id={$deliverable->getKey()}",
                            ...(Tenant::checkCurrent() ? ['tenant_id=' . Tenant::current()->getKey()] : []),
                        ]),
                    );
                });

            throw_if(! $this->canSendWithinQuotaLimits($message, $deliverable), new NotificationQuotaExceeded());

            $result = new EmailChannelResultData(
                success: false,
            );

            $sentMessage = $this->mailer->mailer($message->mailer ?? null)->send(
                $this->buildView($message),
                array_merge($message->data(), $this->additionalMessageData($notification)),
                $this->messageBuilder($notifiable, $notification, $message)
            );

            $result->success = true;
            $result->recipients = $sentMessage->getEnvelope()->getRecipients();

            try {
                if ($result->success) {
                    $isDemoModeEnabled = Tenant::current()?->config->mail->isDemoModeEnabled ?? false;

                    $deliverable->update([
                        'delivery_status' => (! $isDemoModeEnabled)
                            ? NotificationDeliveryStatus::Dispatched
                            : NotificationDeliveryStatus::Successful,
                        'quota_usage' => (! $isDemoModeEnabled)
                            ? $this->determineQuotaUsage($result->recipients)
                            : 0,
                    ]);
                } else {
                    $deliverable->update([
                        'delivery_status' => NotificationDeliveryStatus::DispatchFailed,
                    ]);
                }

                // Consider dispatching this as a seperate job so that it can be encapsulated to be retried if it fails, but also avoid changing the status of the deliverable if it fails.
                if ($notification instanceof HasAfterSendHook) {
                    $notification->afterSend($notifiable, $deliverable, $result);
                }
            } catch (Throwable $exception) {
                report($exception);
            }
        } catch (NotificationQuotaExceeded $exception) {
            $deliverable->update(['delivery_status' => NotificationDeliveryStatus::RateLimited]);
        } catch (Throwable $exception) {
            $deliverable->update([
                'delivery_status' => NotificationDeliveryStatus::DispatchFailed,
            ]);

            throw $exception;
        }
    }

    protected function determineQuotaUsage(array $recipients): int
    {
        $users = User::query()
            ->with('roles')
            ->whereIn('email', array_map(
                fn (Address $recipient): string => $recipient->getAddress(),
                $recipients,
            ))
            ->get()
            ->keyBy('email');

        return collect($recipients)
            ->filter(fn (Address $recipient): bool => $users[$recipient->getAddress()]?->isSuperAdmin() ?? false)
            ->count();
    }

    protected function canSendWithinQuotaLimits(MailMessage $message, OutboundDeliverable $deliverable): bool
    {
        $primaryRecipientUsage = ($deliverable->recipient instanceof User && $deliverable->recipient->isSuperAdmin()) ? 0 : 1;

        // 1 for the primary recipient, plus the number of cc and bcc recipients
        $estimatedQuotaUsage = $primaryRecipientUsage + count($message->cc) + count($message->bcc);

        $licenseSettings = app(LicenseSettings::class);

        $resetWindow = $licenseSettings->data->limits->getResetWindow();

        $currentQuotaUsage = OutboundDeliverable::query()
            ->where('channel', NotificationChannel::Email)
            ->whereBetween('created_at', [$resetWindow['start'], $resetWindow['end']])
            ->sum('quota_usage');

        return ($currentQuotaUsage + $estimatedQuotaUsage) <= $licenseSettings->data->limits->emails;
    }
}
