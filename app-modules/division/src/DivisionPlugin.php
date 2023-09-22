<?php

namespace Assist\Division;

use Filament\Panel;
use Filament\Contracts\Plugin;

class DivisionPlugin implements Plugin
{
    public function getId(): string
    {
        return 'division';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__ . '/Filament/Resources',
            for: 'Assist\\Division\\Filament\\Resources'
        );
    }

    public function boot(Panel $panel): void {}
}
