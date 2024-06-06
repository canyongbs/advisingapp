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

namespace AdvisingApp\Engagement\Enums;

enum EngagementDeliveryStatus: string
{
    case Awaiting = 'awaiting';
    case Dispatched = 'dispatched';
    case DispatchFailed = 'failed_dispatch';
    case RateLimited = 'rate_limited';

    case Successful = 'successful';
    case Failed = 'failed';

    public function getTextColorClass(): string
    {
        return match ($this) {
            EngagementDeliveryStatus::Awaiting,
            EngagementDeliveryStatus::Dispatched => 'text-yellow-500',

            EngagementDeliveryStatus::Successful => 'text-green-500',

            EngagementDeliveryStatus::Failed,
            EngagementDeliveryStatus::RateLimited,
            EngagementDeliveryStatus::DispatchFailed => 'text-red-500',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            EngagementDeliveryStatus::Awaiting,
            EngagementDeliveryStatus::Dispatched => 'info',

            EngagementDeliveryStatus::Successful => 'success',

            EngagementDeliveryStatus::Failed,
            EngagementDeliveryStatus::RateLimited,
            EngagementDeliveryStatus::DispatchFailed => 'danger',
        };
    }

    public function getIconClass(): string
    {
        return match ($this) {
            EngagementDeliveryStatus::Awaiting,
            EngagementDeliveryStatus::Dispatched => 'heroicon-s-clock',

            EngagementDeliveryStatus::Successful => 'heroicon-s-check-circle',

            EngagementDeliveryStatus::Failed,
            EngagementDeliveryStatus::RateLimited,
            EngagementDeliveryStatus::DispatchFailed => 'heroicon-s-exclamation-circle',
        };
    }

    public function getMessage(): string
    {
        return match ($this) {
            EngagementDeliveryStatus::Successful => 'Successfully delivered',
            EngagementDeliveryStatus::Awaiting, EngagementDeliveryStatus::Dispatched => 'Awaiting delivery',
            EngagementDeliveryStatus::Failed, EngagementDeliveryStatus::DispatchFailed => 'Failed to send',
            EngagementDeliveryStatus::RateLimited => 'Failed to send due to rate limits',
        };
    }
}
