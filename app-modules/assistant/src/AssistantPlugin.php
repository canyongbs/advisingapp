<?php

namespace Assist\Assistant;

use Filament\Panel;
use Filament\Contracts\Plugin;
use Assist\Assistant\Filament\Pages\AIAssistant;

class AssistantPlugin implements Plugin
{
    public function getId(): string
    {
        return 'assistant';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__ . '/Filament/Resources',
            for: 'Assist\\Assistant\\Filament\\Resources'
        )
            ->pages(
                [
                    AIAssistant::class,
                ]
            );
    }

    public function boot(Panel $panel): void {}
}
