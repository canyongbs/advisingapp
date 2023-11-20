<?php

namespace Assist\Authorization\Models\Concerns;

use ReflectionClass;
use Illuminate\Support\Collection;
use App\Models\Attributes\NoPermissions;
use App\Actions\Finders\ApplicationModules;

// This trait is used to define specific permissions for a model
// A model that needs to break typical conventions can either extend
// Or override the functionality provided here.
trait DefinesPermissions
{
    public function getWebPermissions(): Collection
    {
        return collect($this->webPermissions());
    }

    public function getApiPermissions(): Collection
    {
        return collect($this->apiPermissions());
    }

    protected function webPermissions(): array
    {
        if ((new ReflectionClass($this))->getAttributes(NoPermissions::class)) {
            return [];
        }

        return resolve(ApplicationModules::class)
            ->moduleConfig(
                module: 'authorization',
                path: 'permissions/web/model'
            );
    }

    protected function apiPermissions(): array
    {
        if ((new ReflectionClass($this))->getAttributes(NoPermissions::class)) {
            return [];
        }

        return resolve(ApplicationModules::class)
            ->moduleConfig(
                module: 'authorization',
                path: 'permissions/api/model'
            );
    }
}
