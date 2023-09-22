<?php

namespace Assist\CaseloadManagement;

use Filament\Panel;
use Filament\Contracts\Plugin;

class CaseloadManagementPlugin implements Plugin
{
    public function getId(): string
    {
        return 'caseload-management';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__ . '/Filament/Resources',
            for: 'Assist\\CaseloadManagement\\Filament\\Resources'
        );

        $panel->discoverPages(
            in: __DIR__ . '/Filament/Pages',
            for: 'Assist\\CaseloadManagement\\Filament\\Pages'
        );
    }

    public function boot(Panel $panel): void {}
}
