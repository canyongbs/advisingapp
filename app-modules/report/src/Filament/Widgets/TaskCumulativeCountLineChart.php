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
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TaskCumulativeCountLineChart extends LineChartReportWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Tasks by Affiliation';

    protected int | string | array $columnSpan = 'full';

    public function getData(): array
    {
        $startDate = filled($this->filters['startDate'] ?? null)
            ? Carbon::parse($this->filters['startDate'])->startOfDay()
            : null;

        $endDate = filled($this->filters['endDate'] ?? null)
            ? Carbon::parse($this->filters['endDate'])->endOfDay()
            : null;

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
        if ($startDate && $endDate) {
            $studentData = DB::select("
                WITH months AS (
                    SELECT generate_series(
                        date_trunc('month', ?::date),
                        date_trunc('month', ?::date),
                        interval '1 month'
                    ) AS month
                ),
                monthly_data AS (
                    SELECT
                        date_trunc('month', created_at) AS month,
                        COUNT(*) AS total
                    FROM tasks
                    WHERE created_at BETWEEN ? AND ?
                    AND concern_type = ?
                    AND deleted_at IS NULL
                    GROUP BY date_trunc('month', created_at)
                )
                SELECT
                    to_char(m.month, 'Mon YYYY') AS label,
                    COALESCE(md.total, 0) AS total
                FROM months m
                LEFT JOIN monthly_data md ON m.month = md.month
                ORDER BY m.month
            ", [
                $startDate,
                $endDate,
                $startDate,
                $endDate,
                app(Student::class)->getMorphClass(),
            ]);

            $prospectData = DB::select("
                WITH months AS (
                    SELECT generate_series(
                        date_trunc('month', ?::date),
                        date_trunc('month', ?::date),
                        interval '1 month'
                    ) AS month
                ),
                monthly_data AS (
                    SELECT
                        date_trunc('month', created_at) AS month,
                        COUNT(*) AS total
                    FROM tasks
                    WHERE created_at BETWEEN ? AND ?
                    AND concern_type = ?
                    AND deleted_at IS NULL
                    GROUP BY date_trunc('month', created_at)
                )
                SELECT
                    to_char(m.month, 'Mon YYYY') AS label,
                    COALESCE(md.total, 0) AS total
                FROM months m
                LEFT JOIN monthly_data md ON m.month = md.month
                ORDER BY m.month
            ", [
                $startDate,
                $endDate,
                $startDate,
                $endDate,
                app(Prospect::class)->getMorphClass(),
            ]);

            $unrelatedData = DB::select("
                WITH months AS (
                    SELECT generate_series(
                        date_trunc('month', ?::date),
                        date_trunc('month', ?::date),
                        interval '1 month'
                    ) AS month
                ),
                monthly_data AS (
                    SELECT
                        date_trunc('month', created_at) AS month,
                        COUNT(*) AS total
                    FROM tasks
                    WHERE created_at BETWEEN ? AND ?
                    AND concern_id IS NULL
                    AND concern_type IS NULL
                    AND deleted_at IS NULL
                    GROUP BY date_trunc('month', created_at)
                )
                SELECT
                    to_char(m.month, 'Mon YYYY') AS label,
                    COALESCE(md.total, 0) AS total
                FROM months m
                LEFT JOIN monthly_data md ON m.month = md.month
                ORDER BY m.month
            ", [
                $startDate,
                $endDate,
                $startDate,
                $endDate,
            ]);
        } else {
            $studentData = DB::select("
                WITH months AS (
                    SELECT generate_series(
                        date_trunc('month', CURRENT_DATE) - INTERVAL '11 months',
                        date_trunc('month', CURRENT_DATE),
                        interval '1 month'
                    ) AS month
                ),
                monthly_data AS (
                    SELECT
                        date_trunc('month', created_at) AS month,
                        COUNT(*) AS total
                    FROM tasks
                    WHERE created_at >= date_trunc('month', CURRENT_DATE) - INTERVAL '11 months'
                    AND concern_type = ?
                    AND deleted_at IS NULL
                    GROUP BY date_trunc('month', created_at)
                )
                SELECT
                    to_char(m.month, 'Mon YYYY') AS label,
                    COALESCE(md.total, 0) AS total
                FROM months m
                LEFT JOIN monthly_data md ON m.month = md.month
                ORDER BY m.month
            ", [
                app(Student::class)->getMorphClass(),
            ]);

            $prospectData = DB::select("
                WITH months AS (
                    SELECT generate_series(
                        date_trunc('month', CURRENT_DATE) - INTERVAL '11 months',
                        date_trunc('month', CURRENT_DATE),
                        interval '1 month'
                    ) AS month
                ),
                monthly_data AS (
                    SELECT
                        date_trunc('month', created_at) AS month,
                        COUNT(*) AS total
                    FROM tasks
                    WHERE created_at >= date_trunc('month', CURRENT_DATE) - INTERVAL '11 months'
                    AND concern_type = ?
                    AND deleted_at IS NULL
                    GROUP BY date_trunc('month', created_at)
                )
                SELECT
                    to_char(m.month, 'Mon YYYY') AS label,
                    COALESCE(md.total, 0) AS total
                FROM months m
                LEFT JOIN monthly_data md ON m.month = md.month
                ORDER BY m.month
            ", [
                app(Prospect::class)->getMorphClass(),
            ]);

            $unrelatedData = DB::select("
                WITH months AS (
                    SELECT generate_series(
                        date_trunc('month', CURRENT_DATE) - INTERVAL '11 months',
                        date_trunc('month', CURRENT_DATE),
                        interval '1 month'
                    ) AS month
                ),
                monthly_data AS (
                    SELECT
                        date_trunc('month', created_at) AS month,
                        COUNT(*) AS total
                    FROM tasks
                    WHERE created_at >= date_trunc('month', CURRENT_DATE) - INTERVAL '11 months'
                    AND concern_id IS NULL
                    AND concern_type IS NULL
                    AND deleted_at IS NULL
                    GROUP BY date_trunc('month', created_at)
                )
                SELECT
                    to_char(m.month, 'Mon YYYY') AS label,
                    COALESCE(md.total, 0) AS total
                FROM months m
                LEFT JOIN monthly_data md ON m.month = md.month
                ORDER BY m.month
            ");
        }

        return [
            'studentTasks' => collect($studentData)->pluck('total', 'label')->toArray(),
            'prospectTasks' => collect($prospectData)->pluck('total', 'label')->toArray(),
            'unrelatedTasks' => collect($unrelatedData)->pluck('total', 'label')->toArray(),
        ];
    }
}
