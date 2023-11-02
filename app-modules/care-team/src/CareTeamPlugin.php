<?php

namespace Assist\CareTeam;

use Filament\Panel;
use Filament\Contracts\Plugin;

class CareTeamPlugin implements Plugin
{
    public function getId(): string
    {
        return 'care-team';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__ . '/Filament/Resources',
            for: 'Assist\\CareTeam\\Filament\\Resources'
        );
    }

    public function boot(Panel $panel): void {}
}
