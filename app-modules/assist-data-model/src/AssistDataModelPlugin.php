<?php

namespace Assist\AssistDataModel;

use Filament\Panel;
use Filament\Contracts\Plugin;

class AssistDataModelPlugin implements Plugin
{
    public function getId(): string
    {
        return 'assist-data-model';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__ . '/Filament/Resources',
            for: 'Assist\\AssistDataModel\\Filament\\Resources'
        );
    }

    public function boot(Panel $panel): void
    {
    }
}
