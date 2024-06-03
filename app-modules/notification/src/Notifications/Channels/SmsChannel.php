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
use Twilio\Rest\Client;
use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\DB;
use Twilio\Exceptions\TwilioException;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Engagement\Models\EngagementDeliverable;
use AdvisingApp\Notification\Models\OutboundDeliverable;
use Talkroute\MessageSegmentCalculator\SegmentCalculator;
use AdvisingApp\Notification\Notifications\SmsNotification;
use AdvisingApp\Notification\Enums\NotificationDeliveryStatus;
use AdvisingApp\Notification\Exceptions\NotificationQuotaExceeded;
use AdvisingApp\Notification\DataTransferObjects\SmsChannelResultData;
use AdvisingApp\Notification\DataTransferObjects\NotificationResultData;

class SmsChannel
{
    public function send(object $notifiable, SmsNotification $notification): void
    {
        try {
            DB::beginTransaction();

            $deliverable = $notification->beforeSend($notifiable, SmsChannel::class);

            if (! $this->canSendWithinQuotaLimits($notification, $notifiable)) {
                $deliverable->update(['delivery_status' => NotificationDeliveryStatus::RateLimited]);

                // Do anything else we need to notify sending party that notification was not sent

                if ($deliverable->related instanceof EngagementDeliverable) {
                    $deliverable->related->update(['delivery_status' => NotificationDeliveryStatus::RateLimited]);
                }

                DB::commit();

                throw new NotificationQuotaExceeded();
            }

            $smsData = $this->handle($notifiable, $notification);

            $notification->afterSend($notifiable, $deliverable, $smsData);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    public function handle(object $notifiable, SmsNotification $notification): NotificationResultData
    {
        $twilioMessage = $notification->toSms($notifiable);

        $client = app(Client::class);

        $messageContent = [
            'from' => $twilioMessage->getFrom(),
            'body' => $twilioMessage->getContent(),
        ];

        if (! app()->environment('local')) {
            $messageContent['statusCallback'] = config('app.url') . route('inbound.webhook.twilio', 'status_callback', false);
        }

        $result = SmsChannelResultData::from([
            'success' => false,
        ]);

        try {
            $message = $client->messages->create(
                config('local_development.twilio.to_number') ?: $twilioMessage->getRecipientPhoneNumber(),
                $messageContent
            );

            $result->success = true;
            $result->message = $message;
        } catch (TwilioException $e) {
            $result->error = $e->getMessage();
        }

        return $result;
    }

    public static function afterSending(object $notifiable, OutboundDeliverable $deliverable, SmsChannelResultData $result): void
    {
        if ($result->success) {
            $deliverable->update([
                'external_reference_id' => $result->message->sid,
                'external_status' => $result->message->status,
                'delivery_status' => NotificationDeliveryStatus::Dispatched,
                'quota_usage' => self::determineQuotaUsage($result),
            ]);
        } else {
            $deliverable->update([
                'delivery_status' => NotificationDeliveryStatus::DispatchFailed,
                'delivery_response' => $result->error,
            ]);
        }
    }

    public static function determineQuotaUsage(SmsChannelResultData $result): int
    {
        if ($user = User::where('phone_number', $result->message->to)->first()) {
            if ($user->hasRole('authorization.super_admin')) {
                return 0;
            }
        }

        return $result->message->numSegments;
    }

    public function canSendWithinQuotaLimits(SmsNotification $notification, object $notifiable): bool
    {
        $estimatedQuotaUsage = SegmentCalculator::segmentsCount($notification->toSms($notifiable)->getContent());

        $licenseSettings = app(LicenseSettings::class);

        $resetWindow = $licenseSettings->data->limits->getResetWindow();

        $currentQuotaUsage = OutboundDeliverable::where('channel', NotificationChannel::Sms)
            ->whereBetween('created_at', [$resetWindow['start'], $resetWindow['end']])
            ->sum('quota_usage');

        return $currentQuotaUsage + $estimatedQuotaUsage <= $licenseSettings->data->limits->sms;
    }
}
