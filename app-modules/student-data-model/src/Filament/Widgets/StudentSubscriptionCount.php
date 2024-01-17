<?php

namespace AdvisingApp\StudentDataModel\Filament\Widgets;

use App\Models\User;
use App\Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudentSubscriptionCount extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        /** @var User $user */
        $user = auth()->user();

        return [
            Stat::make('Subscriptions', $user->studentSubscriptions()->count()),
        ];
    }
}
