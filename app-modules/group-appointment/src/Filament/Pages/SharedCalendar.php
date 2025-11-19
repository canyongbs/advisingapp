<?php

namespace AdvisingApp\GroupAppointment\Filament\Pages;

use App\Filament\Clusters\GroupAppointment;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;

class SharedCalender extends Page
{

    protected string $view = 'filament.pages.coming-soon';

    protected static ?string $cluster = GroupAppointment::class;
}
