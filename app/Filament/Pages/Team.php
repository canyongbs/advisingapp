<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Pages\Concerns\HasNavigationGroup;

class Team extends Page
{
    use HasNavigationGroup;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'assist.filament.pages.coming-soon';

    protected static ?string $navigationLabel = 'Teams';

    protected ?string $heading = 'Teams';
}
