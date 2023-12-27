<?php

namespace Database\Factories\Concerns;

use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

trait RandomizeState
{
    public function randomizeState(array $states = []): self
    {
        $states = empty($states) ? $this->getStates() : collect($states);

        $randomState = $states->random();

        return call_user_func([$this, $randomState]);
    }

    protected function getStates(): Collection
    {
        $class = new ReflectionClass($this);

        return collect($class->getMethods(ReflectionMethod::IS_PUBLIC))
            ->reject(function ($method) use ($class) {
                return $method->getName() === 'randomizeState' ||
                       $method->getDeclaringClass()->getName() !== $class->getName();
            })
            ->filter(function ($method) {
                if (! $method->hasReturnType()) {
                    return false;
                }

                $returnType = $method->getReturnType();

                return $returnType instanceof ReflectionNamedType &&
                       ($returnType->getName() === 'self' ||
                        is_subclass_of($returnType->getName(), Factory::class));
            })
            ->map
            ->getName();
    }
}
