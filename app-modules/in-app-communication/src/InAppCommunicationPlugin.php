<?php

namespace Assist\InAppCommunication;

use Filament\Panel;
use Filament\Contracts\Plugin;

class InAppCommunicationPlugin implements Plugin
{
    public function getId(): string
    {
        return 'in-app-communication';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__ . '/Filament/Resources',
            for: 'Assist\\InAppCommunication\\Filament\\Resources'
        );

        $panel->discoverPages(
            in: __DIR__ . '/Filament/Pages',
            for: 'Assist\\InAppCommunication\\Filament\\Pages'
        );
    }

    public function boot(Panel $panel): void {}
}
