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

namespace AdvisingApp\Report\Filament\Widgets;

use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\Concern\Models\Concern;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Models\Task;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

class StudentsStats extends StatsOverviewReportWidget
{
    protected int | string | array $columnSpan = [
        'sm' => 2,
        'md' => 4,
        'lg' => 4,
    ];

    public function getStats(): array
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        $groupId = $this->getSelectedGroup();

        $shouldBypassCache = filled($startDate) || filled($endDate) || filled($groupId);

        $studentsCount = $shouldBypassCache
            ? Student::query()
                ->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('created_at_source', [$startDate, $endDate])
                )
                ->when(
                    $groupId,
                    fn (Builder $query) => $this->groupFilter($query, $groupId)
                )
                ->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'total-students-count',
                now()->addHours(24),
                fn () => Student::query()->count()
            );

        $concernsCount = $shouldBypassCache
            ? Concern::query()
                ->whereHasMorph('concern', Student::class, function (Builder $query) use ($groupId) {
                    $query->when(
                        $groupId,
                        fn (Builder $query) => $this->groupFilter($query, $groupId)
                    );
                })
                ->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('created_at', [$startDate, $endDate])
                )
                ->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'total-student-concerns-count',
                now()->addHours(24),
                fn () => Concern::query()->whereHasMorph('concern', Student::class)->count()
            );

        $casesCount = $shouldBypassCache
            ? CaseModel::query()
                ->whereHasMorph('respondent', Student::class, function (Builder $query) use ($groupId) {
                    $query->when(
                        $groupId,
                        fn (Builder $query) => $this->groupFilter($query, $groupId)
                    );
                })
                ->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('created_at', [$startDate, $endDate])
                )
                ->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'total-student-cases-count',
                now()->addHours(24),
                fn () => CaseModel::query()->whereHasMorph('respondent', Student::class)->count()
            );

        $tasksCount = $shouldBypassCache
            ? Task::query()
                ->whereHasMorph('concern', Student::class, function (Builder $query) use ($groupId) {
                    $query->when(
                        $groupId,
                        fn (Builder $query) => $this->groupFilter($query, $groupId)
                    );
                })
                ->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('created_at', [$startDate, $endDate])
                )
                ->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'total-student-tasks-count',
                now()->addHours(24),
                fn () => Task::query()->whereHasMorph('concern', Student::class)->count()
            );

        return [
            Stat::make(
                'Total Students',
                ($studentsCount > 9999999)
            ? Number::abbreviate($studentsCount, maxPrecision: 2)
            : Number::format($studentsCount, maxPrecision: 2)
            ),
            Stat::make('Total Concerns', Number::abbreviate($concernsCount, maxPrecision: 2)),
            Stat::make('Total Cases', Number::abbreviate($casesCount, maxPrecision: 2)),
            Stat::make('Total Tasks', Number::abbreviate($tasksCount, maxPrecision: 2)),
        ];
    }
}
