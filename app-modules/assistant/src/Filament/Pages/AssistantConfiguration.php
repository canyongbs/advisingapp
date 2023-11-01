<?php

namespace Assist\Assistant\Filament\Pages;

use Filament\Pages\Page;
use Assist\Consent\Filament\Resources\ConsentAgreementResource\Pages\ListConsentAgreements;

class AssistantConfiguration extends Page
{
    protected static string $view = 'assist.assistant.filament.pages.assistant-configuration';

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static ?string $navigationLabel = 'Artificial Intelligence';

    protected static ?string $navigationGroup = 'Product Administration';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Artificial Intelligence';

    protected static ?string $pluralModelLabel = 'Artificial Intelligence';

    protected static ?string $title = 'Artificial Intelligence';

    public function getSubNavigation(): array
    {
        return $this->generateNavigationItems(
            [
                ListConsentAgreements::class,
            ]
        );
    }
}
