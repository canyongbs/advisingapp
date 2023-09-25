<?php

namespace Assist\CaseloadManagement\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Pages\Concerns\HasNavigationGroup;

class Campaign extends Page
{
    use HasNavigationGroup;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static string $view = 'assist.filament.pages.coming-soon';

    protected static ?string $navigationLabel = 'Campaigns';
}
