<?php

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
            ? QnaAdvisorThread::query()->whereHas('advisor')
                ->whereMorphedTo('author', Student::class)
                ->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('created_at', [$startDate, $endDate])
                )
                ->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'qna-advisor-students-count',
                now()->addHours(24),
                fn (): int => QnaAdvisorThread::query()->whereHas('advisor')->whereMorphedTo('author', Student::class)->count()
            );

        $prospectsCount = $shouldBypassCache
            ? QnaAdvisorThread::query()->whereHas('advisor')
                ->whereMorphedTo('author', Prospect::class)
                ->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('created_at', [$startDate, $endDate])
                )
                ->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'qna-advisor-prospects-count',
                now()->addHours(24),
                fn (): int => QnaAdvisorThread::query()->whereHas('advisor')->whereMorphedTo('author', Prospect::class)->count()
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
            Stat::make('QnA Advisors', ($qnaAdvisors > 9999999)
                ? Number::abbreviate($qnaAdvisors, maxPrecision: 2)
                : Number::format($qnaAdvisors, maxPrecision: 2)),

            Stat::make('Students', Number::abbreviate($studentsCount, maxPrecision: 2)),
            Stat::make('Prospects', Number::abbreviate($prospectsCount, maxPrecision: 2)),
            Stat::make('Unauthenticated', Number::abbreviate($unauthenticatedCount, maxPrecision: 2)),
        ];
    }
}
