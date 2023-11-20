<?php

namespace Assist\Alert;

use Filament\Panel;
use Filament\Contracts\Plugin;

class AlertPlugin implements Plugin
{
    public function getId(): string
    {
        return 'alert';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__ . '/Filament/Resources',
            for: 'Assist\\Alert\\Filament\\Resources'
        );
    }

    public function boot(Panel $panel): void {}
}
