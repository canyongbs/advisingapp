<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\IntegrationTwilio\Settings\TwilioSettings;
use AdvisingApp\Notification\DataTransferObjects\SmsChannelResultData;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Notification\Enums\SmsMessageEventType;
use AdvisingApp\Notification\Enums\SmsMessagingProvider;
use AdvisingApp\Notification\Exceptions\NotificationQuotaExceeded;
use AdvisingApp\Notification\Exceptions\SmsOptOutException;
use AdvisingApp\Notification\Models\Contracts\CanBeNotified;
use AdvisingApp\Notification\Models\SmsMessage;
use AdvisingApp\Notification\Models\StoredAnonymousNotifiable;
use AdvisingApp\Notification\Notifications\Contracts\HasAfterSendHook;
use AdvisingApp\Notification\Notifications\Contracts\HasBeforeSendHook;
use AdvisingApp\Notification\Notifications\Contracts\OnDemandNotification;
use AdvisingApp\Notification\Notifications\Messages\TwilioMessage;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use App\Settings\LicenseSettings;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use Talkroute\MessageSegmentCalculator\SegmentCalculator;
use Telnyx\Message;
use Telnyx\Telnyx;
use Throwable;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Api\V2010;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Client;
use Twilio\Rest\MessagingBase;

class SmsChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        [$recipientId, $recipientType] = match (true) {
            $notifiable instanceof Model => [$notifiable->getKey(), $notifiable->getMorphClass()],
            $notifiable instanceof AnonymousNotifiable && $notification instanceof OnDemandNotification => $notification->identifyRecipient(),
            default => [
                StoredAnonymousNotifiable::query()->createOrFirst([
                    'type' => NotificationChannel::Sms,
                    'route' => $notifiable->routeNotificationFor('sms', $notification),
                ])->getKey(),
                (new StoredAnonymousNotifiable())->getMorphClass(),
            ],
        };

        $message = $notification->toSms($notifiable);

        if (! ($message instanceof TwilioMessage)) {
            throw new Exception('The notification\'s mail message must be an instance of [' . TwilioMessage::class . '].');
        }

        $recipientNumber = $message->getRecipientPhoneNumber() ?? $notifiable->routeNotificationFor('sms', $notification);

        $smsMessage = new SmsMessage([
            'notification_class' => $notification::class,
            'content' => $message->toArray(),
            'recipient_id' => $recipientId,
            'recipient_type' => $recipientType,
            'recipient_number' => is_array($recipientNumber) ? null : $recipientNumber,
        ]);

        if ($notification instanceof HasBeforeSendHook) {
            $notification->beforeSend(
                notifiable: $notifiable,
                message: $smsMessage,
                channel: NotificationChannel::Sms
            );
        }

        $smsMessage->save();

        if ($notifiable instanceof CanBeNotified && ! $notifiable->canReceiveSms()) {
            $smsMessage->events()->create([
                'type' => SmsMessageEventType::FailedDispatch,
                'payload' => [
                    'error' => 'System determined recipient cannot receive SMS messages.',
                ],
                'occurred_at' => now(),
            ]);

            return;
        }

        if ($recipientNumber && $this->isRecipientOptedOut($notifiable, $recipientNumber)) {
            $smsMessage->events()->create([
                'type' => SmsMessageEventType::FailedDispatch,
                'payload' => [
                    'error' => 'Recipient phone number has opted out of SMS messages.',
                ],
                'occurred_at' => now(),
            ]);

            throw new SmsOptOutException($recipientNumber);
        }

        try {
            if (blank($recipientNumber)) {
                $smsMessage->events()->create([
                    'type' => SmsMessageEventType::FailedDispatch,
                    'payload' => [
                        'error' => 'System determined recipient cannot receive SMS messages.',
                    ],
                    'occurred_at' => now(),
                ]);

                return;
            }

            $twilioSettings = app(TwilioSettings::class);

            $quotaUsage = $this->determineQuotaUsage($message, $smsMessage);

            throw_if($quotaUsage && (! $this->canSendWithinQuotaLimits($quotaUsage)), new NotificationQuotaExceeded());

            if (! $twilioSettings->is_demo_mode_enabled) {
                $result = match ($twilioSettings->provider) {
                    SmsMessagingProvider::Twilio => $this->sendViaTwilio($message, $recipientNumber),
                    SmsMessagingProvider::Telnyx => $this->sendViaTelnyx($message, $recipientNumber, $twilioSettings->telnyx_api_key),
                };
            } else {
                $result = SmsChannelResultData::from([
                    'success' => true,
                    'message' => new MessageInstance(
                        new V2010(new MessagingBase(new Client(username: 'abc123', password: 'abc123'))),
                        [
                            'sid' => Str::random(),
                            'status' => 'delivered',
                            'from' => $message->getFrom(),
                            'to' => $recipientNumber,
                            'body' => $message->getContent(),
                            'num_segments' => 1,
                        ],
                        'abc123'
                    ),
                ]);
            }

            try {
                if ($result->success) {
                    $smsMessage->quota_usage = $this->determineQuotaUsage($result, $smsMessage);
                    $smsMessage->external_reference_id = match ($twilioSettings->provider) {
                        SmsMessagingProvider::Twilio => $result->message->sid,
                        SmsMessagingProvider::Telnyx => $result->message->id, // @phpstan-ignore property.notFound
                    };

                    $smsMessage->events()->create([
                        'type' => $twilioSettings->is_demo_mode_enabled
                            ? SmsMessageEventType::BlockedByDemoMode
                            : SmsMessageEventType::Dispatched,
                        'payload' => $result->message->toArray(),
                        'occurred_at' => now(),
                    ]);

                    $smsMessage->save();
                } else {
                    $smsMessage->events()->create([
                        'type' => SmsMessageEventType::FailedDispatch,
                        'payload' => [
                            'error' => $result->error,
                        ],
                        'occurred_at' => now(),
                    ]);
                }

                if ($notification instanceof HasAfterSendHook) {
                    $notification->afterSend($notifiable, $smsMessage, $result);
                }
            } catch (Throwable $exception) {
                report($exception);
            }
        } catch (NotificationQuotaExceeded $exception) {
            $smsMessage->events()->create([
                'type' => SmsMessageEventType::RateLimited,
                'payload' => [],
                'occurred_at' => now(),
            ]);
        } catch (Throwable $exception) {
            $smsMessage->events()->create([
                'type' => SmsMessageEventType::FailedDispatch,
                'payload' => [],
                'occurred_at' => now(),
            ]);

            throw $exception;
        }
    }

    protected function sendViaTwilio(TwilioMessage $message, mixed $recipientNumber): SmsChannelResultData
    {
        $client = app(Client::class);

        $messageContent = [
            'from' => $message->getFrom(),
            'body' => $message->getContent(),
        ];

        if (! app()->environment('local')) {
            $messageContent['statusCallback'] = config('app.url') . route('inbound.webhook.twilio', 'status_callback', false);
        }

        $result = SmsChannelResultData::from([
            'success' => false,
        ]);

        try {
            $message = $client->messages->create(
                config('local_development.twilio.to_number') ?: $recipientNumber,
                $messageContent,
            );

            $result->success = true;
            $result->message = $message;
        } catch (TwilioException $exception) {
            $result->error = $exception->getMessage();
        }

        return $result;
    }

    protected function sendViaTelnyx(TwilioMessage $message, mixed $recipientNumber, ?string $apiKey): SmsChannelResultData
    {
        $result = SmsChannelResultData::from([
            'success' => false,
        ]);

        try {
            Telnyx::setApiKey($apiKey);

            $message = Message::create([
                'from' => $message->getFrom(),
                'to' => config('local_development.twilio.to_number') ?: $recipientNumber,
                'text' => $message->getContent(),
                'webhook_url' => config('app.url') . route('inbound.webhook.telnyx', absolute: false),
                'webhook_failover_url' => null,
            ]);

            $result->success = true;
            $result->message = $message;
        } catch (Throwable $exception) {
            $result->error = $exception->getMessage();
        }

        return $result;
    }

    protected function determineQuotaUsage(TwilioMessage | SmsChannelResultData $message, SmsMessage $smsMessage): int
    {
        if (app(TwilioSettings::class)->is_demo_mode_enabled) {
            return 0;
        }

        if ($smsMessage->recipient instanceof User) {
            return 0;
        }

        if ($message instanceof TwilioMessage) {
            return SegmentCalculator::segmentsCount($message->getContent());
        }

        // It's numSegments for Twilio or parts for Telnyx
        return $message->message->numSegments ?? $message->message->parts; // @phpstan-ignore property.notFound
    }

    protected function canSendWithinQuotaLimits(int $usage): bool
    {
        $licenseSettings = app(LicenseSettings::class);

        $resetWindow = $licenseSettings->data->limits->getResetWindow();

        $currentQuotaUsage = SmsMessage::query()
            ->whereBetween('created_at', [$resetWindow['start'], $resetWindow['end']])
            ->sum('quota_usage');

        return ($currentQuotaUsage + $usage) <= $licenseSettings->data->limits->sms;
    }

    protected function isRecipientOptedOut(object $notifiable, string $recipientNumber): bool
    {
        if ((! $notifiable instanceof Student) && (! $notifiable instanceof Prospect)) {
            return false;
        }

        return $notifiable->phoneNumbers()
            ->where('number', $recipientNumber)
            ->whereHas('smsOptOut')
            ->exists();
    }
}
