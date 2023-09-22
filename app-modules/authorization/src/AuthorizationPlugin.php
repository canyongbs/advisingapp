<?php

namespace Assist\Authorization;

use Filament\Panel;
use Livewire\Livewire;
use Filament\Contracts\Plugin;
use Filament\Navigation\MenuItem;
use Livewire\Mechanisms\ComponentRegistry;
use Assist\Authorization\Filament\Pages\Auth\SetPassword;
use Assist\Authorization\Http\Middleware\RedirectIfPasswordNotSet;

class AuthorizationPlugin implements Plugin
{
    public function getId(): string
    {
        return 'authorization-plugin';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->discoverResources(
                in: __DIR__ . '/Filament/Resources',
                for: 'Assist\\Authorization\\Filament\\Resources'
            )
            ->databaseNotifications()
            ->databaseNotificationsPolling('10s')
            ->authMiddleware([
                RedirectIfPasswordNotSet::class,
            ])
            ->authenticatedRoutes(function () use ($panel) {
                SetPassword::routes($panel);
            });

        Livewire::component(app(ComponentRegistry::class)->getName(SetPassword::class), SetPassword::class);
    }

    public function boot(Panel $panel): void
    {
        $panel->userMenuItems(
            [
                'logout' => MenuItem::make()
                    ->label('Log out')
                    ->url(route('logout')),
            ]
        );
    }
}
