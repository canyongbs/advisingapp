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

use AdvisingApp\IntegrationTwilio\Settings\TwilioSettings;
use AdvisingApp\Notification\Actions\MakeOutboundDeliverable;
use AdvisingApp\Notification\DataTransferObjects\SmsChannelResultData;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Notification\Enums\NotificationDeliveryStatus;
use AdvisingApp\Notification\Exceptions\NotificationQuotaExceeded;
use AdvisingApp\Notification\Models\Contracts\CanBeNotified;
use AdvisingApp\Notification\Models\OutboundDeliverable;
use AdvisingApp\Notification\Notifications\Contracts\HasAfterSendHook;
use AdvisingApp\Notification\Notifications\Contracts\HasBeforeSendHook;
use AdvisingApp\Notification\Notifications\Messages\TwilioMessage;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use Talkroute\MessageSegmentCalculator\SegmentCalculator;
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
        $deliverable = resolve(MakeOutboundDeliverable::class)->execute($notification, $notifiable, NotificationChannel::Sms);

        if ($notification instanceof HasBeforeSendHook) {
            $notification->beforeSend($notifiable, $deliverable, NotificationChannel::Sms);
        }

        $deliverable->save();

        try {
            if ((! ($notifiable instanceof CanBeNotified)) || (! $notifiable->canRecieveSms())) {
                $deliverable->update([
                    'delivery_status' => NotificationDeliveryStatus::DispatchFailed,
                    'last_delivery_attempt' => now(),
                    'delivery_response' => 'System determined recipient cannot receive SMS messages.',
                ]);

                return;
            }

            $message = $notification->toSms($notifiable);

            $twilioSettings = app(TwilioSettings::class);

            $quotaUsage = $this->determineQuotaUsage($message, $deliverable);

            throw_if($quotaUsage && (! $this->canSendWithinQuotaLimits($quotaUsage)), new NotificationQuotaExceeded());

            if (! $twilioSettings->is_demo_mode_enabled) {
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
                        config('local_development.twilio.to_number') ?: $message->getRecipientPhoneNumber(),
                        $messageContent
                    );

                    $result->success = true;
                    $result->message = $message;
                } catch (TwilioException $exception) {
                    $result->error = $exception->getMessage();
                }
            } else {
                $result = SmsChannelResultData::from([
                    'success' => true,
                    'message' => new MessageInstance(
                        new V2010(new MessagingBase(new Client(username: 'abc123', password: 'abc123'))),
                        [
                            'sid' => Str::random(),
                            'status' => 'delivered',
                            'from' => $message->getFrom(),
                            'to' => $message->getRecipientPhoneNumber(),
                            'body' => $message->getContent(),
                            'num_segments' => 1,
                        ],
                        'abc123'
                    ),
                ]);
            }

            try {
                if ($result->success) {
                    $deliverable->update([
                        'external_reference_id' => $result->message->sid,
                        'external_status' => $result->message->status,
                        'delivery_status' => $twilioSettings->is_demo_mode_enabled
                            ? NotificationDeliveryStatus::BlockedByDemoMode
                            : NotificationDeliveryStatus::Dispatched,
                        'quota_usage' => $this->determineQuotaUsage($result, $deliverable),
                    ]);
                } else {
                    $deliverable->update([
                        'delivery_status' => NotificationDeliveryStatus::DispatchFailed,
                        'delivery_response' => $result->error,
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

    protected function determineQuotaUsage(TwilioMessage | SmsChannelResultData $message, OutboundDeliverable $deliverable): int
    {
        if (app(TwilioSettings::class)->is_demo_mode_enabled) {
            return 0;
        }

        if ($deliverable->recipient instanceof User) {
            return 0;
        }

        if ($message instanceof TwilioMessage) {
            return SegmentCalculator::segmentsCount($message->getContent());
        }

        return $message->message->numSegments;
    }

    protected function canSendWithinQuotaLimits(int $usage): bool
    {
        $licenseSettings = app(LicenseSettings::class);

        $resetWindow = $licenseSettings->data->limits->getResetWindow();

        $currentQuotaUsage = OutboundDeliverable::query()
            ->where('channel', NotificationChannel::Sms)
            ->whereBetween('created_at', [$resetWindow['start'], $resetWindow['end']])
            ->sum('quota_usage');

        return ($currentQuotaUsage + $usage) <= $licenseSettings->data->limits->sms;
    }
}
