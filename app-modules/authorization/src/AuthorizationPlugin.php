<?php

namespace Assist\Authorization;

use Filament\Panel;
use Filament\Contracts\Plugin;
use Filament\Navigation\MenuItem;

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
            ->databaseNotificationsPolling('10s');
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
