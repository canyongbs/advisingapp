<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Ai\Models\PromptType;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;

class PromptsByCategoryDoughnutChart extends ChartReportWidget
{
    protected static ?string $heading = 'Prompts';

    protected int | string | array $columnSpan = [
        'sm' => 12,
        'md' => 4,
        'lg' => 4,
    ];

    protected static ?string $maxHeight = '240px';

    public function render(): View
    {
        if(!$this->getChartData()->count()){
            return view('livewire.nowidgetdata');
        }

        $data = $this->getChartData()->pluck('prompts_count')->filter();

        if(!count($data)){
            return view('livewire.nowidgetdata');
        }
        
        return view(static::$view, $this->getViewData());
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

    protected function getData(): array
    {
        $promptsByCategory = $this->getChartData();

        return [
            'labels' => $promptsByCategory->pluck('title'),
            'datasets' => [
                [
                    'label' => 'My First Dataset',
                    'data' => $promptsByCategory->pluck('prompts_count'),
                    'backgroundColor' => $promptsByCategory->pluck('bg_color'),
                    'hoverOffset' => 4,
                ],
            ],
        ];
    }

    protected function getChartData(){
        return Cache::tags([$this->cacheTag])->remember('prompt_by_category_chart', now()->addHours(24), function (): Collection {
            $promptsByCategoryData = PromptType::withCount(['prompts'])->get(['id', 'title']);

            $promptsByCategoryData = $promptsByCategoryData->map(function (PromptType $promptType) {
                $promptType['bg_color'] = $this->getRgbString();

                return $promptType;
            });

            return $promptsByCategoryData;
        });
    }

    protected function getRgbString(): string
    {
        return 'rgb(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ')';
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
