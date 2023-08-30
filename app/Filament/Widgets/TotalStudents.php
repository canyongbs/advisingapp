<?php

namespace App\Filament\Widgets;

use Assist\AssistDataModel\Models\Student;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalStudents extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Students', $this->formatCount(Student::count())),
        ];
    }
}
