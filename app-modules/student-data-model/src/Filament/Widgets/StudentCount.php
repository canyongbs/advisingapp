<?php

namespace AdvisingApp\StudentDataModel\Filament\Widgets;

use Illuminate\Support\Number;
use App\Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use AdvisingApp\StudentDataModel\Models\Student;

class StudentCount extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Students', Number::abbreviate(Student::count(), maxPrecision: 2)),
        ];
    }
}
