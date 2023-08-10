<?php

namespace Assist\KnowledgeBase;

use Filament\Panel;
use Filament\Contracts\Plugin;

class KnowledgeBasePlugin implements Plugin
{
    public function getId(): string
    {
        return 'knowledge-base';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__ . '/Filament/Resources',
            for: 'Assist\\KnowledgeBase\\Filament\\Resources'
        );
    }

    public function boot(Panel $panel): void
    {
    }
}
