<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class EmailTemplates extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static string $view = 'assist.filament.pages.coming-soon';

    public function getBreadcrumbs(): array
    {
        return [
            ...(new EmailConfiguration())->getBreadcrumbs(),
            $this::getUrl() => $this::getNavigationLabel(),
        ];
    }

    public function getSubNavigation(): array
    {
        return (new EmailConfiguration())->getSubNavigation();
    }
}
