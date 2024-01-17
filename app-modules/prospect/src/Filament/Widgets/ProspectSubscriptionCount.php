<?php

namespace AdvisingApp\Prospect\Filament\Widgets;

use App\Models\User;
use App\Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProspectSubscriptionCount extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        /** @var User $user */
        $user = auth()->user();

        return [
            Stat::make('Subscriptions', $user->prospectSubscriptions()->count()),
        ];
    }
}
