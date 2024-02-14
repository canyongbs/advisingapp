<?php

namespace App\Registries;

use Illuminate\Support\Collection;

class RoleBasedAccessControlRegistry
{
    public static array $registries = [];

    public static function register($class): void
    {
        static::$registries[] = $class;
    }

    public static function getRegistries(): Collection
    {
        return collect(static::$registries);
    }
}
