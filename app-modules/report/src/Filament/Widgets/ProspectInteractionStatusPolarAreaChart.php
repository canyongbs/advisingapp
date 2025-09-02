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

use AdvisingApp\Interaction\Models\InteractionStatus;
use AdvisingApp\Prospect\Models\Prospect;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ProspectInteractionStatusPolarAreaChart extends ChartReportWidget
{
    protected static ?string $heading = 'Status';

    protected int | string | array $columnSpan = [
        'sm' => 12,
        'md' => 6,
        'lg' => 6,
    ];

    protected static ?string $maxHeight = '240px';

    public function render(): View
    {
        $data = $this->getData()['datasets'][0]['data'];

        $data = $data->filter();

        if (! count($data)) {
            return view('livewire.no-widget-data');
        }

        return view(static::$view, $this->getViewData());
    }

    public function getData(): array
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        $segmentId = $this->getSelectedSegment();

        $shouldBypassCache = filled($startDate) || filled($endDate) || filled($segmentId);

        $interactionsByStatus = $shouldBypassCache
            ? $this->getInteractionStatusData($startDate, $endDate, $segmentId)
            : Cache::tags(["{{$this->cacheTag}}"])->remember('prospect_interactions_by_status', now()->addHours(24), function () {
                return $this->getInteractionStatusData();
            });

        return [
            'labels' => $interactionsByStatus->pluck('name'),
            'datasets' => [
                [
                    'data' => $interactionsByStatus->pluck('interactions_count'),
                    'backgroundColor' => $interactionsByStatus->pluck('bg_color'),
                    'hoverOffset' => 4,
                ],
            ],
        ];
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
        return 'polarArea';
    }

    /**
     * @return Collection<int, InteractionStatus>
     */
    protected function getInteractionStatusData(?Carbon $startDate = null, ?Carbon $endDate = null, ?string $segmentId = null): Collection
    {
        return InteractionStatus::withCount([
            'interactions' => function ($query) use ($startDate, $endDate, $segmentId) {
                $query->whereHasMorph('interactable', Prospect::class, function (Builder $query) use ($segmentId) {
                        $query->when(
                            $segmentId,
                            fn (Builder $query) => $this->segmentFilter($query, $segmentId)
                        );
                    })
                    ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('created_at', [$startDate, $endDate]);
                    });
            },
        ])->get(['id', 'name'])->map(function (InteractionStatus $interactionStatus) {
            $interactionStatus['bg_color'] = $interactionStatus->color->getRgbString();

            return $interactionStatus;
        });
    }
}
