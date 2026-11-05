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

namespace AdvisingApp\Report\Filament\Widgets;

use AdvisingApp\StudentDataModel\Models\Scopes\WithoutArchivedStudents;
use AdvisingApp\StudentDataModel\Models\Student;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

/**
 * Each bucket reports how many students, who are not currently archived, had been created in
 * the SIS by the END of that month. A student's current archive status applies to every bucket,
 * so archiving a student removes them from the whole chart, including the months before they
 * were archived, and the running total only ever rises.
 *
 * It is seeded with a baseline headcount of students who already existed before the reported
 * window, so the first bucket reflects the whole population rather than only students added
 * since the window started.
 */
class StudentCumulativeCountLineChart extends LineChartReportWidget
{
    protected ?string $heading = 'Students (Cumulative)';

    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 4,
        'lg' => 4,
    ];

    public function render(): View
    {
        if (Student::query()->tap(new WithoutArchivedStudents())->whereNull('created_at_source')->exists()) {
            return view('report::filament.widgets.empty', [
                'message' => 'We apologize, some records are missing information about when your students were created in the student information system (SIS), so we are unable to accurately present the cumulative growth of students at your institution in this chart.',
            ]);
        }

        return parent::render();
    }

    public static function canView(): bool
    {
        return (! Student::query()->tap(new WithoutArchivedStudents())->exists())
            || Student::query()->tap(new WithoutArchivedStudents())->whereNotNull('created_at_source')->exists();
    }

    public function getData(): array
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        $groupId = $this->getSelectedGroup();

        $shouldBypassCache = filled($startDate) || filled($endDate) || $groupId;

        $runningTotalPerMonth = $shouldBypassCache
            ? $this->getStudentRunningTotalData($startDate, $endDate, $groupId)
            : Cache::tags(["{{$this->cacheTag}}"])
                ->remember('student-cumulative-count-line-chart', now()->addHours(24), function (): array {
                    return $this->getStudentRunningTotalData();
                });

        return [
            'datasets' => [
                [
                    'data' => array_values($runningTotalPerMonth),
                ],
            ],
            'labels' => array_keys($runningTotalPerMonth),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'min' => 0,
                ],
            ],
        ];
    }

    /**
     * @return array<string, int>
     */
    protected function getStudentRunningTotalData(?Carbon $startDate = null, ?Carbon $endDate = null, ?string $groupId = null): array
    {
        $startDate = $startDate ?? Carbon::now()->startOfMonth()->subMonths(11);
        $endDate = $endDate ?? Carbon::now()->endOfMonth();

        $months = $this->getMonthRange($startDate, $endDate);

        // Students who existed before the window are already part of the population the first
        // bucket should report, not just whoever is added later.
        $baseline = Student::query()
            ->tap(new WithoutArchivedStudents())
            ->when($groupId, fn (Builder $query) => $this->groupFilter($query, $groupId))
            ->where('created_at_source', '<', $startDate)
            ->count();

        $addedPerMonth = Student::query()
            ->tap(new WithoutArchivedStudents())
            ->when($groupId, fn (Builder $query) => $this->groupFilter($query, $groupId))
            ->whereBetween('created_at_source', [$startDate, $endDate])
            ->selectRaw("date_trunc('month', created_at_source) as month, COUNT(*) as monthly_total")
            ->groupByRaw("date_trunc('month', created_at_source)")
            ->get()
            ->mapWithKeys(function (object $item): array {
                return [
                    Carbon::parse($item['month'])->startOfMonth()->toDateString() => (int) $item['monthly_total'],
                ];
            });

        $runningTotal = [];
        $total = $baseline;

        foreach ($months as $month) {
            $key = $month->toDateString();
            $label = $month->format('M Y');
            $total += $addedPerMonth[$key] ?? 0;
            $runningTotal[$label] = $total;
        }

        return $runningTotal;
    }
}
