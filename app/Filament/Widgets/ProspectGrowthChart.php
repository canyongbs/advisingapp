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

namespace App\Filament\Widgets;

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Enums\ActionCenterTab;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Reactive;

class ProspectGrowthChart extends ChartWidget
{
    #[Reactive]
    public string $activeTab;

    protected static ?string $heading = 'Prospects (Cumulative)';

    protected static ?string $maxHeight = '200px';

    protected int | string | array $columnSpan = 'full';

    public function getData(): array
    {
        /** @var User $user */
        $user = auth()->user();

        $tab = ActionCenterTab::tryFrom($this->activeTab) ?? ActionCenterTab::All;

        $baseQuery = Prospect::query();

        $baseQuery = match ($tab) {
            ActionCenterTab::Subscribed => $baseQuery->whereHas('subscriptions', fn (Builder $query) => $query->where('user_id', $user->getKey())),
            ActionCenterTab::CareTeam => $baseQuery->whereHas('careTeam', fn (Builder $query) => $query->where('user_id', $user->getKey())),
            default => $baseQuery,
        };

        $totalCreatedPerMonth = (clone $baseQuery)
            ->toBase()
            ->selectRaw('date_trunc(\'month\', created_at) as month')
            ->selectRaw('count(*) as total')
            ->where('created_at', '>', now()->subYear())
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $runningTotal = (clone $baseQuery)
            ->where('created_at', '<', now()->subYear())
            ->count();

        $runningTotalPerMonth = [];

        foreach (range(11, 0) as $month) {
            $month = Carbon::now()->subMonths($month);

            $runningTotal += $totalCreatedPerMonth[$month->startOfMonth()->toDateTimeString()] ?? 0;

            $runningTotalPerMonth[$month->format('M Y')] = $runningTotal;
        }

        return [
            'datasets' => [
                [
                    'data' => array_values($runningTotalPerMonth),
                ],
            ],
            'labels' => array_keys($runningTotalPerMonth),
        ];
    }

    /**
      * @return array<string, array<string, array<string, bool|int>>>
      */
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

    protected function getType(): string
    {
        return 'line';
    }
}
