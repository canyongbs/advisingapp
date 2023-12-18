<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Events extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Meeting Center';

    protected static ?int $navigationSort = 20;

    protected static string $view = 'filament.pages.coming-soon';
}
