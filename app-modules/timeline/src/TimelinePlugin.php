<?php

namespace Assist\Timeline;

use Filament\Panel;
use Filament\Contracts\Plugin;

class TimelinePlugin implements Plugin
{
    public function getId(): string
    {
        return 'timeline';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__ . '/Filament/Resources',
            for: 'Assist\\Timeline\\Filament\\Resources'
        );
    }

    public function boot(Panel $panel): void {}
}
