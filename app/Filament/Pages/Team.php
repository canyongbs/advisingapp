<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Team extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'assist.filament.pages.coming-soon';

    protected static ?string $navigationGroup = 'Users and Permissions';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Teams';

    protected ?string $heading = 'Teams';
}
