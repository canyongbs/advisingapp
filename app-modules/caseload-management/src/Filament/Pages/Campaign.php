<?php

namespace Assist\CaseloadManagement\Filament\Pages;

use Filament\Pages\Page;

class Campaign extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static string $view = 'assist.filament.pages.coming-soon';

    protected static ?string $navigationGroup = 'Mass Engagement';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Campaigns';
}
