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

namespace AdvisingApp\Notification\Enums;

use Filament\Support\Contracts\HasLabel;

enum NotificationDeliveryStatus: string implements HasLabel
{
    // Internal
    case Processing = 'processing';
    case Dispatched = 'dispatched';
    case DispatchFailed = 'failed_dispatch';
    case RateLimited = 'rate_limited';
    case BlockedByDemoMode = 'blocked_by_demo_mode';

    // External
    case Failed = 'failed';
    case Successful = 'successful';

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public function getTextColorClass(): string
    {
        return match ($this) {
            self::Processing,
            self::BlockedByDemoMode,
            self::Dispatched => 'text-yellow-500',

            self::Successful => 'text-green-500',

            self::Failed,
            self::RateLimited,
            self::DispatchFailed => 'text-red-500',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Processing,
            self::BlockedByDemoMode,
            self::Dispatched => 'info',

            self::Successful => 'success',

            self::Failed,
            self::RateLimited,
            self::DispatchFailed => 'danger',
        };
    }

    public function getIconClass(): string
    {
        return match ($this) {
            self::Processing,
            self::Dispatched => 'heroicon-s-clock',

            self::BlockedByDemoMode,
            self::Successful => 'heroicon-s-check-circle',

            self::Failed,
            self::RateLimited,
            self::DispatchFailed => 'heroicon-s-exclamation-circle',
        };
    }

    public function getMessage(): string
    {
        return match ($this) {
            self::Successful => 'Successfully delivered',
            self::Processing, self::Dispatched => 'Awaiting delivery',
            self::Failed, self::DispatchFailed => 'Failed to send',
            self::RateLimited => 'Failed to send due to rate limits',
            self::BlockedByDemoMode => 'Blocked by demo mode',
        };
    }
}
