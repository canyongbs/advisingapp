<?php

namespace AdvisingApp\Application\Models\Concerns;

use Bvtterfly\ModelStateMachine\StateMachine;
use Bvtterfly\ModelStateMachine\HasStateMachine;
use Bvtterfly\ModelStateMachine\Exceptions\FieldWithoutCast;
use Bvtterfly\ModelStateMachine\Exceptions\CouldNotFindStateMachineField;
use AdvisingApp\Application\Models\State\StateMachine as RelationBasedStateMachine;

trait HasRelationBasedStateMachine
{
    use TargetsRelationships;
    use HasStateMachine {
        getStateMachine as parentGetStateMachine;
    }

    public function getStateMachine(string $enumClass, string $state): RelationBasedStateMachine|StateMachine
    {
        // We assume that any state utilizing dot notation is targeting a relationship
        if ($this->targetingRelationship($state)) {
            $this->isStateMachineField($state);

            return new RelationBasedStateMachine($this, $enumClass, $state);
        }

        return $this->parentGetStateMachine($state);
    }

    private function setInitialState(): void
    {
        foreach ($this->getStateMachineFields() as $field) {
            // We don't necessarily need to set the initial state if we are targeting a relationship
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

    private function isStateMachineField(string $field): bool
    {
        if (! in_array($field, $this->getStateMachineFields())) {
            throw CouldNotFindStateMachineField::make($field);
        }

        if ($this->targetingRelationship($field)) {
            $relationPath = explode('.', $field);

            $stateField = array_pop($relationPath);

            $relatedModel = $this->accessNestedRelations($this, $relationPath);

            if (! $relatedModel->hasCast($stateField)) {
                throw FieldWithoutCast::make($stateField);
            }
        } else {
            if (! $this->hasCast($field)) {
                throw FieldWithoutCast::make($field);
            }
        }

        return true;
    }
}
