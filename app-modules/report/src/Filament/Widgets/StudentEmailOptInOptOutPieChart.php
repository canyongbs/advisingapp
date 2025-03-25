<?php

namespace AdvisingApp\Report\Filament\Widgets;

use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Support\Colors\Color;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\Cache;

class StudentEmailOptInOptOutPieChart extends PieChartReportWidget
{
    protected static ?string $heading = 'Students Email Address';

    public function getData(): array
    {
        $totalStudents = Student::count();

        $emailOptInPercentage = Cache::tags([$this->cacheTag])->remember('email_opt_in_percentage', now()->addHours(24), function () use ($totalStudents): float {
            return $totalStudents > 0 ? (Student::where('email_bounce', false)->count() / $totalStudents) * 100 : 0;
        });

        $emailOptOutPercentage = Cache::tags([$this->cacheTag])->remember('email_opt_out_percentage', now()->addHours(24), function () use ($totalStudents): float {
            return $totalStudents > 0 ? (Student::where('email_bounce', true)->count() / $totalStudents) * 100 : 0;
        });

        return [
            'labels' => ['Can receive emails', 'Cannot receive emails'],
            'datasets' => [
                [
                    'data' => [$emailOptInPercentage, $emailOptOutPercentage],
                    'backgroundColor' => [
                        $this->getRgbString(Color::Emerald[500]),
                        $this->getRgbString(Color::Red[500]),
                    ],
                    'hoverOffset' => 4,
                ],
            ],
        ];
    }

    protected function getRgbString($color): string
    {
        return "rgb({$color})";
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
        {
            plugins: {
                legend: {
                    display: true,
                },
                tooltip: {
                    callbacks: {
                        label: (value) => value.label + ': ' + value.raw + '%',
                    },
                },
            },
            scales: {
                x: {
                    display: false,
                },
                y: {
                    display: false,
                },
            },
        }
    JS);
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
