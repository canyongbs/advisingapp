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
use Filament\Support\Colors\Color;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class StudentEmailOptInOptOutPieChart extends PieChartReportWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Student Email Addresses';

    protected int | string | array $columnSpan = [
        'sm' => 12,
        'md' => 6,
        'lg' => 6,
    ];

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
        $startDate = filled($this->filters['startDate'] ?? null)
              ? Carbon::parse($this->filters['startDate'])->startOfDay()
              : null;

        $endDate = filled($this->filters['endDate'] ?? null)
            ? Carbon::parse($this->filters['endDate'])->endOfDay()
            : null;

        $shouldBypassCache = filled($startDate) || filled($endDate);

        // $emailOptInPercentage = Cache::tags(["{{$this->cacheTag}}"])->remember('email_opt_in_count', now()->addHours(24), function (): int {
        //     return Student::where('email_bounce', false)->count();
        // });

        $emailOptInPercentage = $shouldBypassCache
            ? Student::where('email_bounce', false)->whereBetween('created_at_source', [$startDate, $endDate])->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember('email_opt_in_count', now()->addHours(24), function (): int {
                return Student::where('email_bounce', false)->count();
            });

        // $emailOptOutPercentage = Cache::tags(["{{$this->cacheTag}}"])->remember('email_opt_out_count', now()->addHours(24), function (): int {
        //     return Student::where('email_bounce', true)->count();
        // });

        $emailOptOutPercentage = $shouldBypassCache
            ? Student::where('email_bounce', true)->whereBetween('created_at_source', [$startDate, $endDate])->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember('email_opt_out_count', now()->addHours(24), function (): int {
                return Student::where('email_bounce', true)->count();
            });

        // $emailNullPercentage = Cache::tags(["{{$this->cacheTag}}"])->remember('email_null_count', now()->addHours(24), function (): int {
        //     return Student::whereNull('email_bounce')->count();
        // });

        $emailNullPercentage = $shouldBypassCache
            ? Student::whereNull('email_bounce')->whereBetween('created_at_source', [$startDate, $endDate])->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember('email_null_count', now()->addHours(24), function (): int {
                return Student::whereNull('email_bounce')->count();
            });

        return [
            'labels' => ['Can receive emails', 'Cannot receive emails', 'Data unavailable'],
            'datasets' => [
                [
                    'data' => [$emailOptInPercentage, $emailOptOutPercentage, $emailNullPercentage],
                    'backgroundColor' => [
                        $this->getRgbString(Color::Orange[500]),
                        $this->getRgbString(Color::Blue[500]),
                        $this->getRgbString(Color::Gray[500]),
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

    /**
     * @return array<string, mixed>
     */
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
