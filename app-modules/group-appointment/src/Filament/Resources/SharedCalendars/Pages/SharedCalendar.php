<?php

namespace AdvisingApp\GroupAppointment\Filament\Resources\SharedCalendars\Pages;

use App\Filament\Clusters\GroupAppointment;
use Filament\Pages\Page;

class SharedCalendar extends Page
{
    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.coming-soon';

    protected static ?string $cluster = GroupAppointment::class;

    protected static ?string $navigationLabel = 'Shared Calendar';
}
