<?php

namespace Assist\Assistant;

use Filament\Panel;
use Filament\Contracts\Plugin;

class AssistantPlugin implements Plugin
{
    public function getId(): string
    {
        return 'assistant';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->discoverResources(
                in: __DIR__ . '/Filament/Resources',
                for: 'Assist\\Assistant\\Filament\\Resources'
            )
            ->discoverPages(
                in: __DIR__ . '/Filament/Pages',
                for: 'Assist\\Assistant\\Filament\\Pages'
            );
    }

    public function boot(Panel $panel): void {}
}
