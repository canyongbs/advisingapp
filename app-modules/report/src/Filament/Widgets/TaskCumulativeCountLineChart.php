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

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class TaskCumulativeCountLineChart extends LineChartReportWidget
{
    protected static ?string $heading = 'Tasks by Affiliation';

    protected int | string | array $columnSpan = 'full';

    public function getData(): array
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        $shouldBypassCache = filled($startDate) || filled($endDate);

        $runningTotalPerMonth = $shouldBypassCache
            ? $this->getTaskCumulativeData($startDate, $endDate)
            : Cache::tags(["{{$this->cacheTag}}"])->remember('task_cumulative_count_line_chart', now()->addHours(24), function (): array {
                return $this->getTaskCumulativeData();
            });

        return [
            'datasets' => [
                [
                    'label' => 'Student Tasks',
                    'data' => array_values($runningTotalPerMonth['studentTasks']),
                    'borderColor' => '#2C8BCA',
                    'pointBackgroundColor' => '#2C8BCA',
                ],
                [
                    'label' => 'Prospect Tasks',
                    'data' => array_values($runningTotalPerMonth['prospectTasks']),
                    'borderColor' => '#FDCC46',
                    'pointBackgroundColor' => '#FDCC46',
                ],
                [
                    'label' => 'Unrelated Tasks',
                    'data' => array_values($runningTotalPerMonth['unrelatedTasks']),
                    'borderColor' => '#FFA500',
                    'pointBackgroundColor' => '#FFA500',
                ],
            ],
            'labels' => array_keys($runningTotalPerMonth['studentTasks']),
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
     * @return array{
     *     studentTasks: array<string, int>,
     *     prospectTasks: array<string, int>,
     *     unrelatedTasks: array<string, int>
     * }
     */
    protected function getTaskCumulativeData(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? Carbon::now()->subMonths(11)->startOfMonth();
        $endDate = $endDate ?? Carbon::now()->endOfMonth();

        $months = $this->getMonthRange($startDate, $endDate);

        $studentType = app(Student::class)->getMorphClass();
        $prospectType = app(Prospect::class)->getMorphClass();

        $studentTasks = $this->getTaskCounts($startDate, $endDate, ['concern_type' => $studentType]);
        $prospectTasks = $this->getTaskCounts($startDate, $endDate, ['concern_type' => $prospectType]);
        $unrelatedTasks = $this->getTaskCounts($startDate, $endDate, ['concern_type' => null, 'concern_id' => null]);

        $result = [
            'studentTasks' => [],
            'prospectTasks' => [],
            'unrelatedTasks' => [],
        ];

        foreach ($months as $month) {
            $key = $month->toDateString();
            $label = $month->format('M Y');

            $result['studentTasks'][$label] = $studentTasks[$key] ?? 0;
            $result['prospectTasks'][$label] = $prospectTasks[$key] ?? 0;
            $result['unrelatedTasks'][$label] = $unrelatedTasks[$key] ?? 0;
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $filters
     *
     * @return array<string, int>
     */
    protected function getTaskCounts(Carbon $startDate, Carbon $endDate, array $filters): array
    {
        $query = Task::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("date_trunc('month', created_at) as month, COUNT(*) as total")
            ->groupByRaw("date_trunc('month', created_at)");

        foreach ($filters as $column => $value) {
            if (is_null($value)) {
                $query->whereNull($column);
            } else {
                $query->where($column, $value);
            }
        }

        return $query->get()
            ->mapWithKeys(fn ($item) => [
                Carbon::parse($item['month'])->startOfMonth()->toDateString() => (int) $item['total'],
            ])
            ->all();
    }
}
