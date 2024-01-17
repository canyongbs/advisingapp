<?php

namespace AdvisingApp\StudentDataModel\Filament\Widgets;

use App\Models\User;
use AdvisingApp\Alert\Enums\AlertStatus;
use App\Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudentAlertCount extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        /** @var User $user */
        $user = auth()->user();

        return [
            Stat::make('Alerts', $user->studentAlerts()->status(AlertStatus::Active)->count()),
        ];
    }
}
