<?php

namespace App\Filament\Pages\Concerns;

use Filament\Pages\Page;
use Filament\Resources\Resource;
use Illuminate\Routing\Redirector;

trait HasChildNavigationItemsOnly
{
    public static function shouldRegisterNavigation(): bool
    {
        foreach (static::$children as $child) {
            if (static::canAccessChildPage($child)) {
                return true;
            }
        }

        return false;
    }

    public function mount(): Redirector
    {
        foreach (static::$children as $child) {
            if (static::canAccessChildPage($child)) {
                return redirect($child::getUrl());
            }
        }

        abort(403);
    }

    /**
     * @param class-string<Page | Resource> $child
     */
    protected static function canAccessChildPage(string $child): bool
    {
        if (! $child::shouldRegisterNavigation()) {
            return false;
        }

        if (! is_subclass_of($child, Resource::class)) {
            return true;
        }

        return $child::canViewAny();
    }
}
