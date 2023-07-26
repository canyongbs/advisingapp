<?php

namespace App\Providers;

use ReflectionClass;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;
use App\Actions\Finders\ApplicationModels;
use Illuminate\Database\Eloquent\Relations\Relation;

class MorphServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        ray(resolve(ApplicationModels::class)->all());

        Relation::enforceMorphMap(
            resolve(ApplicationModels::class)->all()->mapWithKeys(function ($modelClass) {
                $reflection = new ReflectionClass($modelClass);

                return [
                    Str::snake($reflection->getShortName()) => $reflection->getName(),
                ];
            })->toArray()
        );
    }
}
