<?php

namespace App\Filament\Widgets;

use Assist\AssistDataModel\Models\Student;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MyStudents extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(
                'Students (Subscribed)',
                $this->formatCount(
                    auth()->user()->subscriptions()->where('subscribable_type', (new Student())->getMorphClass())->count()
                )
            ),
        ];
    }
}
