<?php

namespace AdvisingApp\Prospect\Filament\Widgets;

use Illuminate\Support\Number;
use Illuminate\Support\Facades\Cache;
use AdvisingApp\Prospect\Models\Prospect;
use Filament\Widgets\StatsOverviewWidget\Stat;
use AdvisingApp\Engagement\Models\EngagementDeliverable;
use AdvisingApp\Report\Filament\Widgets\StatsOverviewReportWidget;

class ProspectEngagementState extends StatsOverviewReportWidget
{
    protected int | string | array $columnSpan = [
        'sm' => 2,
        'md' => 4,
        'lg' => 4,
    ];

    protected function getStats(): array
    {
        return [
            Stat::make('Total Prospects', Number::abbreviate(
                Cache::tags([$this->cacheTag])->remember('total-prospects-count', now()->addHours(24), function (): int {
                    return Prospect::count();
                }),
                maxPrecision: 2,
            )),
            Stat::make('Total Emails Sent', Number::abbreviate(
                Cache::tags([$this->cacheTag])->remember('total-emails-sent', now()->addHours(24), function (): int {
                    return EngagementDeliverable::whereHas('engagement', function ($q) {
                        return $q->whereHasMorph('recipient', Prospect::class);
                    })
                        ->where('channel', 'email')
                        ->where('delivery_status', 'successful')
                        ->count();
                }),
                maxPrecision: 2,
            )),
            Stat::make('Total Texts Sent', Number::abbreviate(
                Cache::tags([$this->cacheTag])->remember('total-texts-sent', now()->addHours(24), function (): int {
                    return EngagementDeliverable::whereHas('engagement', function ($q) {
                        return $q->whereHasMorph('recipient', Prospect::class);
                    })
                        ->where('channel', 'sms')
                        ->where('delivery_status', 'successful')
                        ->count();
                }),
                maxPrecision: 2,
            )),
            Stat::make('Staff Sending Enagements', Number::abbreviate(
                0,
                maxPrecision: 2,
            )),
        ];
    }
}
