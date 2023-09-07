<?php

namespace Assist\Interaction;

use Filament\Panel;
use Filament\Contracts\Plugin;

class InteractionPlugin implements Plugin
{
    public function getId(): string
    {
        return 'interaction';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__ . '/Filament/Resources',
            for: 'Assist\\Interaction\\Filament\\Resources'
        );
    }

    public function boot(Panel $panel): void {}
}
