<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\MyTasks;
use App\Filament\Widgets\MyStudents;
use App\Filament\Widgets\MyProspects;
use App\Filament\Widgets\TotalStudents;
use App\Filament\Widgets\WelcomeWidget;
use App\Filament\Widgets\TotalProspects;
use App\Filament\Widgets\RecentLeadsList;
use Filament\Pages\Dashboard as BasePage;
use App\Filament\Widgets\MyServiceRequests;

class Dashboard extends BasePage
{
    public function getWidgets(): array
    {
        return [
            WelcomeWidget::class,
            TotalStudents::class,
            TotalProspects::class,
            MyStudents::class,
            MyProspects::class,
            MyServiceRequests::class,
            MyTasks::class,
            RecentLeadsList::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'lg' => 4,
        ];
    }
}
