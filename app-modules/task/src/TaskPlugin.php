<?php

namespace Assist\Task;

use Filament\Panel;
use Filament\Contracts\Plugin;

class TaskPlugin implements Plugin
{
    public function getId(): string
    {
        return 'task';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__ . '/Filament/Resources',
            for: 'Assist\\Task\\Filament\\Resources'
        );
    }

    public function boot(Panel $panel): void {}
}
