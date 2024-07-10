<?php

namespace AdvisingApp\StudentDataModel\Filament\Widgets;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Engagement\Models\EngagementDeliverable;
use AdvisingApp\Report\Filament\Widgets\ChartReportWidget;

class StudentEngagementLineChart extends ChartReportWidget
{
    protected static ?string $heading = 'Students (Engagement)';

    protected int | string | array $columnSpan = 'full';

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
        $runningTotalPerMonth = Cache::tags([$this->cacheTag])->remember('student_engagements_line_chart', now()->addHours(24), function (): array {
            $totalEmailEngagementsPerMonth = EngagementDeliverable::query()
                ->whereHas('engagement', function ($q) {
                    return $q->whereHasMorph('recipient', Student::class);
                })
                ->toBase()
                ->where('channel', 'email')
                ->selectRaw('date_trunc(\'month\', created_at) as month')
                ->selectRaw('count(*) as total')
                ->where('created_at', '>', now()->subYear())
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $totalTextEnagagementsPerMonth = EngagementDeliverable::query()
                ->whereHas('engagement', function ($q) {
                    return $q->whereHasMorph('recipient', Student::class);
                })
                ->toBase()
                ->where('channel', 'sms')
                ->selectRaw('date_trunc(\'month\', created_at) as month')
                ->selectRaw('count(*) as total')
                ->where('created_at', '>', now()->subYear())
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $data = [];

            foreach (range(11, 0) as $month) {
                $month = Carbon::now()->subMonths($month);
                $data['emailEngagement'][$month->format('M Y')] = $totalEmailEngagementsPerMonth[$month->startOfMonth()->toDateTimeString()] ?? 0;
                $data['textEnagagment'][$month->format('M Y')] = $totalTextEnagagementsPerMonth[$month->startOfMonth()->toDateTimeString()] ?? 0;
            }

            return $data;
        });

        return [
            'datasets' => [
                [
                    'label' => 'Email',
                    'data' => array_values($runningTotalPerMonth['emailEngagement']),
                    'borderColor' => '#2C8BCA',
                    'pointBackgroundColor' => '#2C8BCA',
                ],
                [
                    'label' => 'SMS',
                    'data' => array_values($runningTotalPerMonth['textEnagagment']),
                    'borderColor' => '#FDCC46',
                    'pointBackgroundColor' => '#FDCC46',
                ],
            ],
            'labels' => array_keys($runningTotalPerMonth['emailEngagement']),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
