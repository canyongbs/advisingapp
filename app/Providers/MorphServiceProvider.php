<?php

namespace App\Providers;

use ReflectionClass;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class MorphServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Relation::enforceMorphMap(
            get_application_models()->mapWithKeys(function ($modelClass) {
                $reflection = new ReflectionClass($modelClass);

                return [
                    Str::snake($reflection->getShortName()) => $reflection->getName(),
                ];
            })->toArray()
        );
    }
}
