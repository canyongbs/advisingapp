<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;
use Filament\Navigation\NavigationItem;
use App\Filament\Resources\NotificationSettingResource\Pages\ListNotificationSettings;

class EmailConfiguration extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Product Administration';

    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.email-configuration';

    protected static bool $shouldRegisterNavigation = false;

    // public function getBreadcrumbs(): array
    // {
    //     return [
    //         $this::getUrl() => 'Email Configuration',
    //     ];
    // }
    //
    // public static function shouldRegisterNavigation(): bool
    // {
    //     /** @var User $user */
    //     $user = auth()->user();
    //
    //     return $user->can(['assistant.access_ai_settings']) || ListConsentAgreements::shouldRegisterNavigation();
    // }
    //
    // public function mount(): void
    // {
    //     /** @var User $user */
    //     $user = auth()->user();
    //
    //     abort_unless($user->can(['assistant.access_ai_settings']) || ListConsentAgreements::shouldRegisterNavigation(), 403);
    //
    //     /** @var NavigationItem $firstNavItem */
    //     $firstNavItem = collect($this->getSubNavigation())->first(function (NavigationItem $item) {
    //         return $item->isVisible();
    //     });
    //
    //     if (is_null($firstNavItem)) {
    //         abort(403);
    //     }
    //
    //     abort_unless($firstNavItem, Response);
    //
    //     redirect($firstNavItem->getUrl());
    // }
    //
    // public function getSubNavigation(): array
    // {
    //     $navigationItems = $this->generateNavigationItems(
    //         [
    //             ListNotificationSettings::class,
    //         ]
    //     );
    //
    //     /** @var User $user */
    //     $user = auth()->user();
    //
    //     if ($user->can(['assistant.access_ai_settings'])) {
    //         $navigationItems = [
    //             ...$navigationItems,
    //         ];
    //     }
    //
    //     return $navigationItems;
    // }
}
