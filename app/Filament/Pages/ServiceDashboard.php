<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ServiceDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Service Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Dashboard';

    protected ?string $heading = 'Dashboard';

    protected static string $view = 'filament.pages.coming-soon';
}
