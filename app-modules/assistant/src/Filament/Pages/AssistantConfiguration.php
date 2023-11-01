<?php

namespace Assist\Assistant\Filament\Pages;

use App\Models\User;
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

    public function getBreadcrumbs(): array
    {
        return [
            $this::getUrl() => 'Artificial Intelligence',
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can(['assistant.access_ai_settings']) || ListConsentAgreements::shouldRegisterNavigation();
    }

    public function mount(): void
    {
        /** @var User $user */
        $user = auth()->user();

        abort_unless($user->can(['assistant.access_ai_settings']) || ListConsentAgreements::shouldRegisterNavigation(), 403);
    }

    public function getSubNavigation(): array
    {
        $navigationItems = $this->generateNavigationItems(
            [
                ListConsentAgreements::class,
            ]
        );

        /** @var User $user */
        $user = auth()->user();

        if ($user->can(['assistant.access_ai_settings'])) {
            $navigationItems = [
                ...$navigationItems,
                ...ManageAiSettings::getNavigationItems(),
            ];
        }

        return $navigationItems;
    }
}
