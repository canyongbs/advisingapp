<?php

namespace Assist\CaseloadManagement\Filament\Pages;

use Filament\Pages\Page;

class Campaign extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Mass Engagement';

    protected static string $view = 'assist.filament.pages.coming-soon';
}
