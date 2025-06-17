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
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StudentCumulativeCountLineChart extends LineChartReportWidget
{
    protected static ?string $heading = 'Students (Cumulative)';

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

    protected function getData(): array
    {
        $runningTotalPerMonth = Cache::tags(["{{$this->cacheTag}}"])->remember('student-cumulative-count-line-chart', now()->addHours(24), function (): array {
            $totalCreatedPerMonth = DB::select("WITH months AS (
                                        SELECT generate_series(
                                            date_trunc('month', CURRENT_DATE) - INTERVAL '11 months',
                                            date_trunc('month', CURRENT_DATE),
                                            interval '1 month'
                                        ) AS month
                                    ),
                                    monthly_data AS (
                                        SELECT
                                            date_trunc('month', created_at_source) AS month,
                                            COUNT(*) AS monthly_total
                                        FROM students
                                        WHERE created_at_source >= date_trunc('month', CURRENT_DATE) - INTERVAL '11 months'
                                        AND deleted_at IS NULL
                                        GROUP BY date_trunc('month', created_at_source)
                                    )
                                    SELECT
                                        to_char(m.month, 'Mon YYYY') as label,
                                        COALESCE(d.monthly_total, 0) AS monthly_total,
                                        SUM(COALESCE(d.monthly_total, 0)) OVER (ORDER BY m.month) AS running_total
                                    FROM months m
                                    LEFT JOIN monthly_data d ON m.month = d.month
                                    ORDER BY m.month
                                ");

            return collect($totalCreatedPerMonth)->pluck('running_total', 'label')->toArray();
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
}
