<?php

namespace StubModuleNamespace\StubClassNamePrefix;

use Filament\Panel;
use Filament\Contracts\Plugin;

class StubClassNamePrefixPlugin implements Plugin
{
    public function getId(): string
    {
        return 'StubModuleName';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__ . '/Filament/Resources',
            for: 'StubModuleNamespace\\StubClassNamePrefix\\Filament\\Resources'
        );
    }

    public function boot(Panel $panel): void {}
}
