<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class SLAConfiguration extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'SLA Configuration';

    protected static ?string $navigationGroup = 'Product Administration';

    protected static ?int $navigationSort = 120;

    protected ?string $heading = 'SLA Configuration';

    protected static string $view = 'filament.pages.coming-soon';
}
