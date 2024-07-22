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

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Engagement\Models\EngagementDeliverable;

class ProspectEngagementLineChart extends LineChartReportWidget
{
    protected static ?string $heading = 'Prospects (Engagement)';

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
        $runningTotalPerMonth = Cache::tags([$this->cacheTag])->remember('prospect_engagements_line_chart', now()->addHours(24), function (): array {
            $totalEmailEnagagementsPerMonth = EngagementDeliverable::query()
                ->whereHas('engagement', function ($q) {
                    return $q->whereHasMorph('recipient', Prospect::class);
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
                    return $q->whereHasMorph('recipient', Prospect::class);
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
                $data['emailEngagement'][$month->format('M Y')] = $totalEmailEnagagementsPerMonth[$month->startOfMonth()->toDateTimeString()] ?? 0;

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
