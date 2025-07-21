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

namespace AdvisingApp\Report\Filament\Widgets;

use AdvisingApp\CaseManagement\Enums\SystemCaseClassification;
use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\StudentDataModel\Models\Student;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

class StudentCaseStats extends StatsOverviewReportWidget
{
    public function getStats(): array
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        $shouldBypassCache = filled($startDate) || filled($endDate);

        $casesCount = $shouldBypassCache
            ? CaseModel::query()
                ->whereHasMorph('respondent', Student::class)
                ->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('created_at', [$startDate, $endDate])
                )
                ->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'total-student-cases-count',
                now()->addHours(24),
                fn (): int => CaseModel::query()
                    ->whereHasMorph('respondent', Student::class)
                    ->count()
            );

        if ($shouldBypassCache) {
            if (filled($startDate) && filled($endDate)) {
                $differenceInDays = Carbon::parse($endDate)->diffInDays(Carbon::parse($startDate));

                if ($differenceInDays > 30) {
                    $startDate = Carbon::parse($endDate)->subDays(30);
                }
            }
            $recentCasesCount = CaseModel::query()
                ->whereHasMorph('respondent', Student::class)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
        } else {
            $recentCasesCount = Cache::tags(["{{$this->cacheTag}}"])->remember(
                'total-student-recent-cases-count',
                now()->addHours(24),
                fn (): int => CaseModel::query()
                    ->whereHasMorph('respondent', Student::class)
                    ->where('created_at', '>=', now()->subDays(30))
                    ->count()
            );
        }

        $openCases = $shouldBypassCache
        ? CaseModel::query()
            ->whereHasMorph('respondent', Student::class)
            ->when(
                $startDate && $endDate,
                fn (Builder $query): Builder => $query->whereBetween('created_at', [$startDate, $endDate])
            )
            ->open()
            ->count()
        : Cache::tags(["{{$this->cacheTag}}"])->remember(
            'total-student-open-cases-count',
            now()->addHours(24),
            fn (): int => CaseModel::query()
                ->whereHasMorph('respondent', Student::class)
                ->open()
                ->count()
        );

        $closedCases = $shouldBypassCache
            ? CaseModel::query()
                ->whereHasMorph('respondent', Student::class)
                ->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('created_at', [$startDate, $endDate])
                )
                ->whereRelation('status', 'classification', '==', SystemCaseClassification::Closed)
                ->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'total-student-closed-cases-count',
                now()->addHours(24),
                fn (): int => CaseModel::query()
                    ->whereHasMorph('respondent', Student::class)
                    ->whereRelation('status', 'classification', '==', SystemCaseClassification::Closed)
                    ->count()
            );

        return [
            Stat::make('Total Cases', Number::abbreviate($casesCount, maxPrecision: 2)),
            Stat::make('Recent Cases', Number::abbreviate($recentCasesCount, maxPrecision: 2)),
            Stat::make('Open Cases', Number::abbreviate($openCases, maxPrecision: 2)),
            Stat::make('Closed Cases', Number::abbreviate($closedCases, maxPrecision: 2)),
        ];
    }

    protected function getColumns(): int
    {
        return 2;
    }
}
