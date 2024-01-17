<?php

namespace AdvisingApp\Prospect\Filament\Widgets;

use App\Models\User;
use App\Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use AdvisingApp\CaseloadManagement\Enums\CaseloadModel;

class ProspectCaseloadCount extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        /** @var User $user */
        $user = auth()->user();

        return [
            Stat::make('Caseloads', $user->caseloads()->model(CaseloadModel::Prospect)->count()),
        ];
    }
}
