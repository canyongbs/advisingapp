<?php

namespace Assist\Authorization\Models\Concerns;

use Illuminate\Support\Collection;

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
        return config('permissions.web.model');
    }

    protected function apiPermissions(): array
    {
        return config('permissions.api.model');
    }
}
