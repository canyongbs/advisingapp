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

namespace AdvisingApp\Report\Support;

use AdvisingApp\Report\Enums\ReportAccessKey;
use AdvisingApp\Report\Models\ReportTeamAccess;
use AdvisingApp\Report\Models\ReportUserAccess;
use App\Models\User;

class ReportAccess
{
    public static function userCanAccessPage(string $pageClass, User $user): bool
    {
        $key = ReportAccessKey::fromPageClass($pageClass);

        if ($key === null) {
            return false;
        }

        return static::userCanAccess($key, $user);
    }

    public static function userCanAccess(ReportAccessKey $key, User $user): bool
    {
        $hasDirectAccess = ReportUserAccess::query()
            ->where('report_key', $key->value)
            ->where('user_id', $user->getKey())
            ->exists();

        if ($hasDirectAccess) {
            return true;
        }

        if (blank($user->team_id)) {
            return false;
        }

        return ReportTeamAccess::query()
            ->where('report_key', $key->value)
            ->where('team_id', $user->team_id)
            ->exists();
    }

    /**
     * The number of distinct users that have access to the report, counting both
     * direct user assignments and members of assigned teams (deduplicated).
     */
    public static function accessCount(ReportAccessKey $key): int
    {
        $directUserIds = ReportUserAccess::query()
            ->where('report_key', $key->value)
            ->pluck('user_id');

        $teamIds = ReportTeamAccess::query()
            ->where('report_key', $key->value)
            ->pluck('team_id');

        $teamUserIds = $teamIds->isEmpty()
            ? collect()
            : User::query()
                ->whereIn('team_id', $teamIds)
                ->pluck('id');

        return $directUserIds
            ->merge($teamUserIds)
            ->unique()
            ->count();
    }
}
