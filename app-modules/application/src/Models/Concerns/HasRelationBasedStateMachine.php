<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Application\Models\Concerns;

use AdvisingApp\Application\Models\State\StateMachine as RelationBasedStateMachine;
use Bvtterfly\ModelStateMachine\Exceptions\CouldNotFindStateMachineField;
use Bvtterfly\ModelStateMachine\Exceptions\FieldWithoutCast;
use Bvtterfly\ModelStateMachine\HasStateMachine;
use Bvtterfly\ModelStateMachine\StateMachine;

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
