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

use AdvisingApp\StudentDataModel\Models\Student;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

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
        if (Student::query()->whereNull('created_at_source')->exists()) {
            return view('report::filament.widgets.empty', [
                'message' => 'We apologize, some records are missing information about when your students were created in the student information system (SIS), so we are unable to accurately present the cumulative growth of students at your institution in this chart.',
            ]);
        }

        return parent::render();
    }

    public static function canView(): bool
    {
        return (! Student::query()->exists()) || Student::query()->whereNotNull('created_at_source')->exists();
    }

    public function getData(): array
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        $segmentId = $this->getSelectedSegment();

        $shouldBypassCache = filled($startDate) || filled($endDate) || $segmentId;

        $runningTotalPerMonth = $shouldBypassCache
            ? $this->getStudentRunningTotalData($startDate, $endDate, $segmentId)
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
    protected function getStudentRunningTotalData(?Carbon $startDate = null, ?Carbon $endDate = null, ?string $segmentId = null): array
    {
        $startDate = $startDate ?? Carbon::now()->subMonths(11)->startOfMonth();
        $endDate = $endDate ?? Carbon::now()->endOfMonth();

        $months = $this->getMonthRange($startDate, $endDate);

        $monthlyData = Student::query()
            ->whereBetween('created_at_source', [$startDate, $endDate])
            ->when(
                $segmentId,
                fn (Builder $query) => $this->segmentFilter($query, $segmentId)
            )
            ->selectRaw("date_trunc('month', created_at_source) as month, COUNT(*) as monthly_total")
            ->groupByRaw("date_trunc('month', created_at_source)")
            ->get()
            ->mapWithKeys(function (object $item): array {
                return [
                    Carbon::parse($item['month'])->startOfMonth()->toDateString() => (int) $item['monthly_total'],
                ];
            });

        $runningTotal = [];
        $total = 0;

        foreach ($months as $month) {
            $key = $month->toDateString();
            $label = $month->format('M Y');
            $count = $monthlyData[$key] ?? 0;
            $total += $count;
            $runningTotal[$label] = $total;
        }

        return $runningTotal;
    }
}
