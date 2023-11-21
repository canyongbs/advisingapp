<?php

namespace App\Filament\Pages;

use App\Filament\Resources\NotificationSettingResource\Pages\ListNotificationSettings;
use Filament\Pages\Page;

class EmailConfigration extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Product Administration';

    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.email-configration';

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ListNotificationSettings::class,
        ]);
    }
}
