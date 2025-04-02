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
use AdvisingApp\Notification\DataTransferObjects\EmailChannelResultData;
use AdvisingApp\Notification\Enums\EmailMessageEventType;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Notification\Exceptions\NotificationQuotaExceeded;
use AdvisingApp\Notification\Models\EmailMessage;
use AdvisingApp\Notification\Models\StoredAnonymousNotifiable;
use AdvisingApp\Notification\Notifications\Attributes\SystemNotification;
use AdvisingApp\Notification\Notifications\Contracts\HasAfterSendHook;
use AdvisingApp\Notification\Notifications\Contracts\HasBeforeSendHook;
use AdvisingApp\Notification\Notifications\Contracts\OnDemandNotification;
use AdvisingApp\Notification\Notifications\Messages\MailMessage;
use App\Features\RoutedEngagements;
use App\Models\Tenant;
use App\Models\User;
use App\Settings\LicenseSettings;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Channels\MailChannel as BaseMailChannel;
use Illuminate\Notifications\Notification;
use ReflectionClass;
use Symfony\Component\Mime\Email;
use Throwable;

class MailChannel extends BaseMailChannel
{
    public function send($notifiable, Notification $notification): void
    {
        [$recipientId, $recipientType] = match (true) {
            $notifiable instanceof Model => [$notifiable->getKey(), $notifiable->getMorphClass()],
            $notifiable instanceof AnonymousNotifiable && $notification instanceof OnDemandNotification => $notification->identifyRecipient($notifiable),
            default => [
                StoredAnonymousNotifiable::query()->createOrFirst([
                    'type' => NotificationChannel::Email,
                    'route' => $notifiable->routeNotificationFor('mail', $notification),
                ])->getKey(),
                (new StoredAnonymousNotifiable())->getMorphClass(),
            ],
        };

        $message = $notification->toMail($notifiable);

        if (! ($message instanceof MailMessage)) {
            throw new Exception('The notification\'s mail message must be an instance of [' . MailMessage::class . '].');
        }

        $recipientAddress = $message->getRecipientEmailAddress() ?? $notifiable->routeNotificationFor('mail', $notification);

        $emailMessage = new EmailMessage([
            'notification_class' => $notification::class,
            'content' => $message->toArray(),
            'recipient_id' => $recipientId,
            'recipient_type' => $recipientType,
            ...(RoutedEngagements::active() ? ['recipient_address' => is_array($recipientAddress) ? null : $recipientAddress] : []),
        ]);

        if ($notification instanceof HasBeforeSendHook) {
            $notification->beforeSend(
                notifiable: $notifiable,
                message: $emailMessage,
                channel: NotificationChannel::Email
            );
        }

        $emailMessage->save();

        $tenant = Tenant::current();
        $tenantMailConfig = $tenant?->config->mail;

        $notificationReflection = new ReflectionClass($notification);
        $isSystemNotification = filled($notificationReflection->getAttributes(SystemNotification::class));

        try {
            if (
                (! ($tenantMailConfig?->isDemoModeEnabled ?? false))
                || ($isSystemNotification && $tenantMailConfig?->isExcludingSystemNotificationsFromDemoMode)
            ) {
                $message->withSymfonyMessage(function (Email $message) use ($tenant, $emailMessage) {
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
                            "app_message_id={$emailMessage->getKey()}",
                            ...($tenant ? ['tenant_id=' . $tenant->getKey()] : []),
                        ]),
                    );
                });

                $quotaUsage = $isSystemNotification ? 0 : $this->determineQuotaUsage($message, $emailMessage);

                throw_if($quotaUsage && (! $this->canSendWithinQuotaLimits($quotaUsage)), new NotificationQuotaExceeded());

                $result = new EmailChannelResultData(
                    success: false,
                );

                try {
                    $sentMessage = $this->mailer->mailer($message->mailer ?? null)->send(
                        $this->buildView($message),
                        array_merge($message->data(), $this->additionalMessageData($notification)),
                        $this->messageBuilder($notifiable, $notification, $message)
                    );
                } catch (Throwable $exception) {
                    $sendingException = $exception;
                }

                if ($sentMessage ?? null) {
                    $result->success = true;
                    $result->recipients = $sentMessage->getEnvelope()->getRecipients();
                }
            } else {
                $result = new EmailChannelResultData(
                    success: true,
                );

                $quotaUsage = 0;
            }

            try {
                if ($result->success) {
                    $emailMessage->quota_usage = $quotaUsage;

                    $emailMessage->events()->create([
                        'type' => (
                            (! $tenantMailConfig?->isDemoModeEnabled ?? false)
                            || ($isSystemNotification && $tenantMailConfig?->isExcludingSystemNotificationsFromDemoMode)
                        )
                            ? EmailMessageEventType::Dispatched
                            : EmailMessageEventType::BlockedByDemoMode,
                        'payload' => $result->toArray(),
                        'occurred_at' => now(),
                    ]);

                    $emailMessage->save();
                } else {
                    $emailMessage->events()->create([
                        'type' => EmailMessageEventType::FailedDispatch,
                        'payload' => $result->toArray(),
                        'occurred_at' => now(),
                    ]);
                }

                if ($notification instanceof HasAfterSendHook) {
                    $notification->afterSend($notifiable, $emailMessage, $result);
                }
            } catch (Throwable $exception) {
                report($exception);
            }

            if ($sendingException ?? null) {
                throw $sendingException;
            }
        } catch (NotificationQuotaExceeded $exception) {
            $emailMessage->events()->create([
                'type' => EmailMessageEventType::RateLimited,
                'payload' => [],
                'occurred_at' => now(),
            ]);
        } catch (Throwable $exception) {
            $emailMessage->events()->create([
                'type' => EmailMessageEventType::FailedDispatch,
                'payload' => [],
                'occurred_at' => now(),
            ]);

            throw $exception;
        }
    }

    protected function determineQuotaUsage(MailMessage $message, EmailMessage $emailMessage): int
    {
        $usage = ($emailMessage->recipient instanceof User) ? 0 : 1;

        $recipientCcEmails = [
            ...$message->cc,
            ...$message->bcc,
        ];

        if ($recipientCcEmails) {
            $usage += (count($recipientCcEmails) - User::query()
                ->whereIn('email', $recipientCcEmails)
                ->count());
        }

        return $usage;
    }

    protected function canSendWithinQuotaLimits(int $usage): bool
    {
        $licenseSettings = app(LicenseSettings::class);

        $resetWindow = $licenseSettings->data->limits->getResetWindow();

        $currentQuotaUsage = EmailMessage::query()
            ->whereBetween('created_at', [$resetWindow['start'], $resetWindow['end']])
            ->sum('quota_usage');

        return ($currentQuotaUsage + $usage) <= $licenseSettings->data->limits->emails;
    }

    protected function getRecipients($notifiable, $notification, $message)
    {
        if (! ($message instanceof MailMessage)) {
            throw new Exception('The notification\'s mail message must be an instance of [' . MailMessage::class . '].');
        }

        if (is_string($address = ($message->getRecipientEmailAddress() ?? $notifiable->routeNotificationFor('mail', $notification)))) {
            return [$address];
        }

        return parent::getRecipients($notifiable, $notification, $message);
    }
}
