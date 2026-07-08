<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Enums;

/**
 * The full subscription lifecycle status synced from Olympus. The instance only
 * acts on ExpiredPeriod2 (warning banner) and Expired (offline); the remaining
 * cases are stored so future behaviour can key off them.
 */
enum SubscriptionStatus: string
{
    case Upcoming = 'upcoming';

    case Active = 'active';

    case Outstanding = 'outstanding';

    case ExpiredPeriod1 = 'expired_period_1';

    case ExpiredPeriod2 = 'expired_period_2';

    case Expired = 'expired';

    case NotApplicable = 'not_applicable';

    /**
     * Whether the expiration warning banner should be shown for this status.
     */
    public function showsExpirationBanner(): bool
    {
        return $this === self::ExpiredPeriod2;
    }

    /**
     * Whether the tenant should be fully inaccessible for this status.
     */
    public function isInaccessible(): bool
    {
        return $this === self::Expired;
    }
}
