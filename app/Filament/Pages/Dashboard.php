<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\MyStudents;
use App\Filament\Widgets\MyProspects;
use App\Filament\Widgets\TotalStudents;
use App\Filament\Widgets\TotalProspects;
use Filament\Pages\Dashboard as BasePage;

class Dashboard extends BasePage
{
    public function getWidgets(): array
    {
        return [
            TotalStudents::class,
            TotalProspects::class,
            MyStudents::class,
            MyProspects::class,
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
