<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class TextMessageTemplates extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

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
