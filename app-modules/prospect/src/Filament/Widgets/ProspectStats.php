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

namespace AdvisingApp\Prospect\Filament\Widgets;

use App\Models\User;
use Laravel\Pennant\Feature;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\Cache;
use AdvisingApp\Alert\Enums\AlertStatus;
use AdvisingApp\Prospect\Models\Prospect;
use Filament\Widgets\StatsOverviewWidget;
use AdvisingApp\Segment\Enums\SegmentModel;
use Filament\Widgets\StatsOverviewWidget\Stat;
use AdvisingApp\CaseloadManagement\Enums\CaseloadModel;

class ProspectStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        /** @var User $user */
        $user = auth()->user();

        return [
            Stat::make('Total Prospects', Number::abbreviate(
                Cache::tags(['prospects'])
                    ->remember('prospects-count', now()->addHour(), function (): int {
                        return Prospect::count();
                    }),
                maxPrecision: 2,
            )),
            Stat::make('My Subscriptions', Cache::tags(['prospects', "user-{$user->getKey()}-prospect-subscriptions"])
                ->remember("user-{$user->getKey()}-prospect-subscriptions-count", now()->addHour(), function () use ($user): int {
                    return $user->prospectSubscriptions()->count();
                })),
            Stat::make('My Alerts', Cache::tags(['prospects', "user-{$user->getKey()}-prospect-alerts"])
                ->remember("user-{$user->getKey()}-prospect-alerts-count", now()->addHour(), function () use ($user): int {
                    return $user->prospectAlerts()->status(AlertStatus::Active)->count();
                })),
            Stat::make('My Population Segments', Cache::tags(["user-{$user->getKey()}-prospect-segments"])
                ->remember("user-{$user->getKey()}-prospect-segments-count", now()->addHour(), function () use ($user): int {
                    return
                    Feature::active('enable-segments')
                        ? $user->segments()->model(SegmentModel::Prospect)->count()
                        : $user->caseloads()->model(CaseloadModel::Prospect)->count();
                })),
        ];
    }
}
