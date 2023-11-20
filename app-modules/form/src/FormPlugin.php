<?php

namespace Assist\Form;

use Filament\Panel;
use Filament\Contracts\Plugin;

class FormPlugin implements Plugin
{
    public function getId(): string
    {
        return 'form';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->discoverResources(
                in: __DIR__ . '/Filament/Resources',
                for: 'Assist\\Form\\Filament\\Resources'
            )
            ->discoverPages(
                in: __DIR__ . '/Filament/Pages',
                for: 'Assist\\Form\\Filament\\Pages'
            );
    }

    public function boot(Panel $panel): void {}
}
