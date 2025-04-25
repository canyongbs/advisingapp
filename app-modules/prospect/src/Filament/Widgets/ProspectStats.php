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

namespace AdvisingApp\Prospect\Filament\Widgets;

use AdvisingApp\Alert\Enums\SystemAlertStatusClassification;
use AdvisingApp\Alert\Models\Alert;
use AdvisingApp\CaseManagement\Enums\SystemCaseClassification;
use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\Engagement\Enums\EngagementResponseStatus;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Task\Enums\TaskStatus;
use AdvisingApp\Task\Models\Task;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class ProspectStats extends StatsOverviewWidget
{
    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        /** @var User $user */
        $user = auth()->user();

        return [
            Stat::make('New Messages', Number::format(EngagementResponse::query()
                ->whereHasMorph('sender', Prospect::class, fn (Builder $query) => $query->whereRelation('subscriptions', 'user_id', $user->getKey()))
                ->where('status', EngagementResponseStatus::New)
                ->count())),
            Stat::make('Open Cases', Number::format(CaseModel::query()
                ->whereHasMorph('respondent', Prospect::class, fn (Builder $query) => $query->whereRelation('subscriptions', 'user_id', $user->getKey()))
                ->whereRelation('status', 'classification', '!=', SystemCaseClassification::Closed)
                ->count())),
            Stat::make('Open Alerts', Number::format(Alert::query()
                ->whereHasMorph('concern', Prospect::class, fn (Builder $query) => $query->whereRelation('subscriptions', 'user_id', $user->getKey()))
                ->whereHas('status', fn (Builder $query) => $query->whereNotIn('classification', [SystemAlertStatusClassification::Resolved, SystemAlertStatusClassification::Canceled]))
                ->count())),
            Stat::make('Open Tasks', Number::format(Task::query()
                ->whereHasMorph('concern', Prospect::class, fn (Builder $query) => $query->whereRelation('subscriptions', 'user_id', $user->getKey()))
                ->whereNotIn('status', [TaskStatus::Completed, TaskStatus::Canceled])
                ->count())),
            Stat::make('Actioned Messages', Number::format(EngagementResponse::query()
                ->whereHasMorph('sender', Prospect::class, fn (Builder $query) => $query->whereRelation('subscriptions', 'user_id', $user->getKey()))
                ->where('status', EngagementResponseStatus::Actioned)
                ->count()))
                ->extraAttributes(['class' => 'fi-wi-stats-overview-stat-primary']),
            Stat::make('Closed Cases', Number::format(CaseModel::query()
                ->whereHasMorph('respondent', Prospect::class, fn (Builder $query) => $query->whereRelation('subscriptions', 'user_id', $user->getKey()))
                ->whereRelation('status', 'classification', SystemCaseClassification::Closed)
                ->count()))
                ->extraAttributes(['class' => 'fi-wi-stats-overview-stat-primary']),
            Stat::make('Closed Alerts', Number::format(Alert::query()
                ->whereHasMorph('concern', Prospect::class, fn (Builder $query) => $query->whereRelation('subscriptions', 'user_id', $user->getKey()))
                ->whereHas('status', fn (Builder $query) => $query->whereIn('classification', [SystemAlertStatusClassification::Resolved, SystemAlertStatusClassification::Canceled]))
                ->count()))
                ->extraAttributes(['class' => 'fi-wi-stats-overview-stat-primary']),
            Stat::make('Closed Tasks', Number::format(Task::query()
                ->whereHasMorph('concern', Prospect::class, fn (Builder $query) => $query->whereRelation('subscriptions', 'user_id', $user->getKey()))
                ->whereIn('status', [TaskStatus::Completed, TaskStatus::Canceled])
                ->count()))
                ->extraAttributes(['class' => 'fi-wi-stats-overview-stat-primary']),
        ];
    }
}
