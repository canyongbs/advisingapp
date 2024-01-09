<?php

namespace AdvisingApp\Application\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Bvtterfly\ModelStateMachine\StateMachine;
use Bvtterfly\ModelStateMachine\HasStateMachine;
use Bvtterfly\ModelStateMachine\Exceptions\FieldWithoutCast;
use AdvisingApp\Application\Models\State\StateMachine as OurStateMachine;
use Bvtterfly\ModelStateMachine\Exceptions\CouldNotFindStateMachineField;

trait HasRelationBasedStateMachine
{
    use TargetsRelationships;
    use HasStateMachine {
        getStateMachine as parentGetStateMachine;
    }

    public function getStateMachine(Model $forModel, string $enumClass, string $state): OurStateMachine|StateMachine
    {
        ray('getStateMachine', $forModel);

        // We assume that any state utilizing dot notation is targeting a relationship
        if ($this->targetingRelationship($state)) {
            return new OurStateMachine($this, $enumClass, $state);
        }

        return $this->parentGetStateMachine($state);
    }

    public function getValueFromDotNotation(Model $model, string $dotNotation)
    {
        $parts = explode('.', $dotNotation);
        $field = array_pop($parts); // The last part is the field

        $currentModel = $model;

        foreach ($parts as $relation) {
            if (! method_exists($currentModel, $relation)) {
                throw new \Exception("Relation '{$relation}' does not exist on " . get_class($currentModel));
            }

            $currentModel = $currentModel->{$relation};

            if (! $currentModel) {
                throw new \Exception("Relation '{$relation}' returned null on " . get_class($currentModel));
            }
        }

        if (! isset($currentModel->{$field})) {
            throw new \Exception("Field '{$field}' does not exist on " . get_class($currentModel));
        }

        return $currentModel->{$field};
    }

    private function setInitialState(): void
    {
        foreach ($this->getStateMachineFields() as $field) {
            // We don't need to set the initial state if we are targeting a relationship
            // Maybe this is something we move towards in the future
            if ($this->targetingRelationship($field) || $this->{$field} !== null) {
                continue;
            }

            $stateMachineConfig = $this->getStateMachineConfig($field);

            $initialValue = $stateMachineConfig->initial;

            if ($initialValue === null) {
                continue;
            }

            $this->{$field} = $initialValue;
        }
    }

    private function setInitialStates(): void
    {
        foreach ($this->getStateMachineFields() as $field) {
            [$relationName, $nestedField] = $this->parseField($field);

            if ($relationName) {
                $relatedModel = $this->{$relationName};

                if ($relatedModel->{$nestedField} !== null) {
                    continue;
                }

                $stateMachineConfig = $relatedModel->getStateMachineConfig($nestedField);
            } else {
                if ($this->{$field} !== null) {
                    continue;
                }

                $stateMachineConfig = $this->getStateMachineConfig($field);
            }

            $initialValue = $stateMachineConfig->initial;

            if ($initialValue === null) {
                continue;
            }

            if ($relationName) {
                $relatedModel->{$nestedField} = $initialValue;
            } else {
                $this->{$field} = $initialValue;
            }
        }
    }

    private function isStateMachineField(string $field): bool
    {
        // Our check here needs to change a bit...
        [$relationName, $nestedField] = $this->parseField($field);

        if ($relationName) {
            $relatedModel = $this->{$relationName};

            if (! $relatedModel->hasCast($nestedField)) {
                throw FieldWithoutCast::make($nestedField);
            }
        } else {
            if (! in_array($field, $this->getStateMachineFields())) {
                throw CouldNotFindStateMachineField::make($field);
            }

            if (! $this->hasCast($field)) {
                throw FieldWithoutCast::make($field);
            }
        }

        return true;
    }

    private function parseField(string $field): array
    {
        if (str_contains($field, '.')) {
            return explode('.', $field, 2);
        }

        return [null, $field];
    }
}
