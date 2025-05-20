<?php

namespace AdvisingApp\Report\Filament\Widgets;

use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\StudentDataModel\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class StudentInteractionLineChart extends LineChartReportWidget
{
    protected static ?string $heading = 'Students (Interaction)';

    protected int | string | array $columnSpan = 'full';

    public function getData(): array
    {
        $runningTotalPerMonth = Cache::tags([$this->cacheTag])->remember('student_interactions_line_chart', now()->addHours(24), function (): array {
            $totalInteractionPerMonth = Interaction::query()
                ->whereHasMorph('interactable', Student::class)
                ->toBase()
                ->selectRaw('date_trunc(\'month\', created_at) as month')
                ->selectRaw('count(*) as total')
                ->where('created_at', '>', now()->subYear())
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $runningTotalPerMonth = [];

            foreach (range(11, 0) as $month) {
                $month = Carbon::now()->subMonths($month);
                $runningTotalPerMonth[$month->format('M Y')] = $totalInteractionPerMonth[$month->startOfMonth()->toDateTimeString()] ?? 0;
            }

            return $runningTotalPerMonth;
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
}
