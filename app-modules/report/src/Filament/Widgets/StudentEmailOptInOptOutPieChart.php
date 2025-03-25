<?php

namespace AdvisingApp\Report\Filament\Widgets;

use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Support\Colors\Color;
use Filament\Support\RawJs;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class StudentEmailOptInOptOutPieChart extends PieChartReportWidget
{
    protected static ?string $heading = 'Students Email Address';

    protected static ?string $maxHeight = '240px';

    public function render(): View
    {
        [$emailOptInPercentage, $emailOptOutPercentage] = $this->getData()['datasets'][0]['data'];

        if ($emailOptInPercentage == 0 && $emailOptOutPercentage == 0) {
            return view('livewire.no-widget-data');
        }

        return view(static::$view, $this->getViewData());
    }

    public function getData(): array
    {
        $totalStudents = Student::count();

        $emailOptInPercentage = Cache::tags([$this->cacheTag])->remember('email_opt_in_percentage', now()->addHours(24), function () use ($totalStudents): float {
            return $totalStudents > 0 ? number_format(Student::where('email_bounce', false)->count() / $totalStudents * 100, 2) : 0;
        });

        $emailOptOutPercentage = Cache::tags([$this->cacheTag])->remember('email_opt_out_percentage', now()->addHours(24), function () use ($totalStudents): float {
            return $totalStudents > 0 ? number_format(Student::where('email_bounce', true)->count() / $totalStudents * 100,2) : 0;
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
