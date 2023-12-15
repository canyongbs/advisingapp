<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ChangeManagement extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Service Management';

    protected static ?int $navigationSort = 20;

    protected static string $view = 'filament.pages.coming-soon';
}
