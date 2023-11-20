<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class DataLakehouse extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static string $view = 'assist.filament.pages.coming-soon';

    protected static ?string $navigationGroup = 'Data and Analytics';

    protected static ?int $navigationSort = 2;
}
