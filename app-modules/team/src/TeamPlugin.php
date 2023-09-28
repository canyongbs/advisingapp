<?php

namespace Assist\Team;

use Filament\Panel;
use Filament\Contracts\Plugin;

class TeamPlugin implements Plugin
{
    public function getId(): string
    {
        return 'team';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__ . '/Filament/Resources',
            for: 'Assist\\Team\\Filament\\Resources'
        );
    }

    public function boot(Panel $panel): void {}
}
