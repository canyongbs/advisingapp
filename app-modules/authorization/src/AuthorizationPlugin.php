<?php

namespace Assist\Authorization;

use Filament\Panel;
use Filament\Contracts\Plugin;

class AuthorizationPlugin implements Plugin
{
    public function getId(): string
    {
        return 'authorization-plugin';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__ . '/Filament/Resources',
            for: 'Assist\\Authorization\\Filament\\Resources'
        );
    }

    public function boot(Panel $panel): void
    {
    }
}
