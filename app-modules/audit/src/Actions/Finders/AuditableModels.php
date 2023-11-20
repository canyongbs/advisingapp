<?php

namespace Assist\Audit\Actions\Finders;

use ReflectionClass;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Relations\Relation;

class AuditableModels
{
    public static function all(): Collection
    {
        return collect(Relation::morphMap())
            ->filter(fn (string $class) => (new ReflectionClass($class))->implementsInterface(Auditable::class))
            ->transform(fn (string $class) => (new ReflectionClass($class))->getShortName());
    }
}
