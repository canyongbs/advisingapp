<?php

namespace Assist\Audit;

use Filament\Panel;
use Filament\Contracts\Plugin;
use Assist\Audit\Filament\Pages\ManageAuditSettings;

class AuditPlugin implements Plugin
{
    public function getId(): string
    {
        return 'audit';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->discoverResources(
                in: __DIR__ . '/Filament/Resources',
                for: 'Assist\\Audit\\Filament\\Resources'
            )
            ->pages(
                [
                    ManageAuditSettings::class,
                ]
            );
    }

    public function boot(Panel $panel): void {}
}
