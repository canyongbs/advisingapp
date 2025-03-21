<?php

namespace AdvisingApp\Ai\Filament\Pages;

use AdvisingApp\Authorization\Enums\LicenseType;
use Filament\Pages\Page;

class ResearchRequests extends Page
{
    protected static ?string $navigationGroup = 'Artificial Intelligence';

    protected static ?int $navigationSort = 30;

    protected static string $view = 'filament.pages.coming-soon';

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        if (! $user->hasLicense(LicenseType::ConversationalAi)) {
            return false;
        }

        return $user->isSuperAdmin();
    }
}
