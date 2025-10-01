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

use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Ai\Models\QnaAdvisorThread;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

class QnaAdvisorReportStats extends StatsOverviewReportWidget
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

        $shouldBypassCache = filled($startDate) || filled($endDate);

        $qnaAdvisors = $shouldBypassCache
            ? QnaAdvisor::query()
                ->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('created_at', [$startDate, $endDate])
                )
                ->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'qna-advisor-report',
                now()->addHours(24),
                fn (): int => QnaAdvisor::query()->count()
            );

        $studentsCount = $shouldBypassCache
            ? QnaAdvisorThread::query()
                ->whereMorphedTo('author', Student::class)
                ->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('created_at', [$startDate, $endDate])
                )
                ->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'qna-advisor-students-count',
                now()->addHours(24),
                fn (): int => QnaAdvisorThread::query()->whereMorphedTo('author', Student::class)->count()
            );

        $prospectsCount = $shouldBypassCache
            ? QnaAdvisorThread::query()
                ->whereMorphedTo('author', Prospect::class)
                ->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('created_at', [$startDate, $endDate])
                )
                ->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'qna-advisor-prospects-count',
                now()->addHours(24),
                fn (): int => QnaAdvisorThread::query()->whereMorphedTo('author', Prospect::class)->count()
            );

        $unauthenticatedCount = $shouldBypassCache
            ? QnaAdvisorThread::query()
                ->whereNull('author_id')
                ->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('created_at', [$startDate, $endDate])
                )
                ->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'qna-advisor-unauthenticated-count',
                now()->addHours(24),
                fn (): int => QnaAdvisorThread::query()->whereNull('author_id')
                    ->count()
            );

        return [
            Stat::make('QnA Advisors', Number::abbreviate($qnaAdvisors, maxPrecision: 2)),
            Stat::make('Students', Number::abbreviate($studentsCount, maxPrecision: 2)),
            Stat::make('Prospects', Number::abbreviate($prospectsCount, maxPrecision: 2)),
            Stat::make('Unauthenticated', Number::abbreviate($unauthenticatedCount, maxPrecision: 2)),
        ];
    }
}
