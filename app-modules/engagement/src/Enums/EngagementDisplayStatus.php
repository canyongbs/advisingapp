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

namespace AdvisingApp\Engagement\Enums;

use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Notification\Enums\EmailMessageEventType;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Notification\Enums\SmsMessageEventType;
use Exception;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EngagementDisplayStatus implements HasLabel, HasColor
{
    // Internal
    case Scheduled;
    case Pending;
    case SystemDelayed;
    case SystemFailed;

    // Email specific
    case Bounced;
    case Delayed;
    case Read;
    case Clicked;
    case Complaint;
    case Unsubscribed;

    // SMS specific
    case Accepted;
    case Queued;
    case Sending;

    // Shared
    case Sent;
    case Failed;
    case Delivered;

    public function getLabel(): string
    {
        return match ($this) {
            self::SystemDelayed => 'System Delayed',
            self::SystemFailed => 'System Failed',
            default => $this->name,
        };
    }

    public static function getStatus(Engagement $engagement): self
    {
        return match ($engagement->channel) {
            NotificationChannel::Email => self::parseEmailStatus($engagement),
            NotificationChannel::Sms => self::parseSmsStatus($engagement),
            default => throw new Exception('Unsupported channel'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Delivered, self::Read, self::Clicked => 'success',
            self::Scheduled, self::Delayed, self::Pending, self::Accepted, self::Queued, self::Sending => 'info',
            self::Failed, self::Bounced, self::Complaint, self::SystemFailed => 'danger',
            self::Sent, self::Unsubscribed => 'gray',
            self::SystemDelayed => 'warning',
        };
    }

    protected static function parseEmailStatus(Engagement $engagement): self
    {
        if (! is_null($engagement->dispatch_failed_at)) {
            return self::SystemFailed;
        }

        $status = self::Pending;

        if (! is_null($engagement->scheduled_at)) {
            $status = self::Scheduled;
        }

        $events = $engagement->latestEmailMessage?->events()->orderBy('occurred_at', 'asc')->get();

        $events?->each(function ($event) use (&$status) {
            match ($event->type) {
                // This is needed due to a bug where sometimes the Dispatched event isn't saved
                // until some of the other external events have already come in
                EmailMessageEventType::Dispatched => $status = ($status === self::Pending || $status === self::Scheduled) ? self::Pending : $status,

                EmailMessageEventType::FailedDispatch => $status = self::SystemDelayed,
                EmailMessageEventType::RateLimited => $status = self::Failed,

                // We will consider the message "delivered" if blocked by demo mode
                // for visual demo purposes
                EmailMessageEventType::BlockedByDemoMode => $status = self::Delivered,

                EmailMessageEventType::Bounce => $status = self::Bounced,
                EmailMessageEventType::Complaint => $status = self::Complaint,
                EmailMessageEventType::Delivery => $status = self::Delivered,
                EmailMessageEventType::Send => $status = self::Sent,
                EmailMessageEventType::Reject => $status = self::Failed,
                // @phpstan-ignore identical.alwaysFalse (This is not actually an error because PHPStan doesn't realized the loop can affect the status here.)
                EmailMessageEventType::Open => $status = ($status === self::Clicked) ? self::Clicked : self::Read,
                EmailMessageEventType::Click => $status = self::Clicked,
                EmailMessageEventType::RenderingFailure => $status = self::Failed,
                EmailMessageEventType::Subscription => $status = self::Unsubscribed,
                EmailMessageEventType::DeliveryDelay => $status = self::Delayed,
            };
        });

        return $status;
    }

    protected static function parseSmsStatus(Engagement $engagement): self
    {
        if (! is_null($engagement->dispatch_failed_at)) {
            return self::SystemFailed;
        }

        $status = self::Pending;

        if (! is_null($engagement->scheduled_at)) {
            $status = self::Scheduled;
        }

        $events = $engagement->latestSmsMessage?->events()->orderBy('occurred_at', 'asc')->get();

        $events?->each(function ($event) use (&$status) {
            match ($event->type) {
                // This is needed due to a bug where sometimes the Dispatched event isn't saved
                // until some of the other external events have already come in
                SmsMessageEventType::Dispatched => $status = ($status === self::Pending || $status === self::Scheduled) ? self::Pending : $status,

                SmsMessageEventType::FailedDispatch => $status = self::SystemDelayed,
                SmsMessageEventType::RateLimited => $status = self::Failed,

                // We will consider the message "delivered" if blocked by demo mode
                // for visual demo purposes
                SmsMessageEventType::BlockedByDemoMode => $status = self::Delivered,

                SmsMessageEventType::Queued => $status = self::Queued,
                SmsMessageEventType::Canceled => $status = self::Failed,
                SmsMessageEventType::Sent => $status = self::Sent,
                SmsMessageEventType::Failed => $status = self::Failed,
                SmsMessageEventType::Delivered => $status = self::Delivered,
                SmsMessageEventType::Undelivered => $status = self::Failed,
                SmsMessageEventType::Read => $status = self::Read,
            };
        });

        return $status;
    }
}
