<?php

namespace App\Filament\Clusters\GroupAppointment\Pages;

use App\Filament\Clusters\GroupAppointment;
use Filament\Pages\Page;

class SharedCalender extends Page
{
    protected string $view = 'filament.pages.coming-soon';

    protected static ?string $cluster = GroupAppointment::class;
}
