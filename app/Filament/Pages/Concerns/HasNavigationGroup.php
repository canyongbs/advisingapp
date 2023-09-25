<?php

namespace App\Filament\Pages\Concerns;

use App\Filament\Enums\NavigationGroup;

trait HasNavigationGroup
{
    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::getNavigationGroup(static::class)?->getLabel();
    }

    public static function getNavigationSort(): ?int
    {
        return NavigationGroup::getNavigationSort(static::class);
    }
}
