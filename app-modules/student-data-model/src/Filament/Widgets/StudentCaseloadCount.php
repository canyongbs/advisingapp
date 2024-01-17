<?php

namespace AdvisingApp\StudentDataModel\Filament\Widgets;

use App\Models\User;
use App\Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use AdvisingApp\CaseloadManagement\Enums\CaseloadModel;

class StudentCaseloadCount extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        /** @var User $user */
        $user = auth()->user();

        return [
            Stat::make('Caseloads', $user->caseloads()->model(CaseloadModel::Student)->count()),
        ];
    }
}
