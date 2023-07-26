<?php

namespace Assist\CaseModule;

use Filament\Panel;
use Filament\Contracts\Plugin;
use Assist\CaseModule\Filament\Resources\CaseItemResource;
use Assist\CaseModule\Filament\Resources\CaseItemStatusResource;
use Assist\CaseModule\Filament\Resources\CaseItemPriorityResource;

class CasePlugin implements Plugin
{
    public function getId(): string
    {
        return 'case-module';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            CaseItemResource::class,
            CaseItemPriorityResource::class,
            CaseItemStatusResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
    }
}
