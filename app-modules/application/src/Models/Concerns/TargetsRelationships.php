<?php

namespace AdvisingApp\Application\Models\Concerns;

use Illuminate\Support\Str;

trait TargetsRelationships
{
    public function targetingRelationship(string $state): bool
    {
        return Str::contains($state, '.');
    }
}
