<?php

namespace AdvisingApp\Report\Filament\Widgets;

use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Support\Colors\Color;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class StudentSmsOptInOptOutPieChart extends PieChartReportWidget
{
    protected static ?string $heading = 'Students Text';

    protected static ?string $maxHeight = '240px';

    public function render(): View
    {
        [$smsOptOutCount, $smsOptInCount] = $this->getData()['datasets'][0]['data'];

        if ($smsOptInCount == 0 && $smsOptOutCount == 0) {
            return view('livewire.no-widget-data');
        }

        return view(static::$view, $this->getViewData());
    }

    public function getData(): array
    {
        $smsOptInCount = Cache::tags([$this->cacheTag])->remember('sms_opt_in_count', now()->addHours(24), function (): int {
            return Student::where('sms_opt_out', false)->count();
        });

        $smsOptOutCount = Cache::tags([$this->cacheTag])->remember('sms_opt_out_count', now()->addHours(24), function (): int {
            return Student::where('sms_opt_out', true)->count();
        });

        return [
            'labels' => ['Can receive texts', 'Cannot receive texts'],
            'datasets' => [
                [
                    'label' => 'My First Dataset',
                    'data' => [$smsOptInCount, $smsOptOutCount],
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

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => false,
                ],
                'y' => [
                    'display' => false,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
