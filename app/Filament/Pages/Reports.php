<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Reports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Reporting';

    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.coming-soon';
}
