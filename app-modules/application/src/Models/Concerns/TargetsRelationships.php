<?php

namespace AdvisingApp\Application\Models\Concerns;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

trait TargetsRelationships
{
    public function targetingRelationship(string $state): bool
    {
        return Str::contains($state, '.');
    }

    public function accessNestedRelations(Model $model, array $relations)
    {
        $current = $model;

        foreach ($relations as $relation) {
            if (! method_exists($current, $relation)) {
                throw new Exception("Relation '{$relation}' does not exist on " . get_class($current));
            }

            $current = $current->{$relation};

            if ($current === null) {
                return null;
            }
        }

        return $current;
    }

    public function dynamicMethodChain(Model $model, array $methods)
    {
        $current = $model;

        foreach ($methods as $method) {
            if (! method_exists($current, $method)) {
                throw new Exception("Method '{$method}' does not exist on " . get_class($current));
            }

            $current = $current->$method();
        }

        return $current;
    }
}
