<?php

namespace Assist\MeetingCenter;

use Filament\Panel;
use Filament\Contracts\Plugin;

class MeetingCenterPlugin implements Plugin
{
    public function getId(): string
    {
        return 'meeting-center';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__ . '/Filament/Resources',
            for: 'Assist\\MeetingCenter\\Filament\\Resources'
        );

        $panel->discoverWidgets(
            in: __DIR__ . '/Filament/Widgets',
            for: 'Assist\\MeetingCenter\\Filament\\Widgets'
        );
    }

    public function boot(Panel $panel): void {}
}
