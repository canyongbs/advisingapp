<?php

namespace Assist\Case;

use Filament\Panel;
use Filament\Contracts\Plugin;

class ServiceManagementPlugin implements Plugin
{
    public function getId(): string
    {
        return 'service-request';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__ . '/Filament/Resources',
            for: 'Assist\\ServiceManagement\\Filament\\Resources'
        );
    }

    public function boot(Panel $panel): void {}
}
