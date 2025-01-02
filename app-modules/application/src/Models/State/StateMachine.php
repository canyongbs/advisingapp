<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Application\Models\State;

use AdvisingApp\Application\Models\Concerns\TargetsRelationships;
use BackedEnum;
use Bvtterfly\ModelStateMachine\ConfigLoader;
use Bvtterfly\ModelStateMachine\DataTransferObjects\StateMachineConfig;
use Bvtterfly\ModelStateMachine\Exceptions\InvalidTransition;
use Bvtterfly\ModelStateMachine\Exceptions\UnknownState;
use Bvtterfly\ModelStateMachine\TransitionManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class StateMachine
{
    use TargetsRelationships;

    private readonly StateMachineConfig $config;

    public function __construct(private readonly Model $model, private readonly string $enumClass, private readonly string $state)
    {
        $this->config = ConfigLoader::load($enumClass);
    }

    public function getAllStates(): Collection
    {
        return $this->config->states->keys();
    }

    public function getStateTransitions(BackedEnum|string|null $state = null): Collection
    {
        $state ??= $this->currentState();

        if ($state instanceof BackedEnum) {
            $this->checkValidEnum($state);
            $state = $state->value;
        }

        $states = $this->config->states;

        if (! $states->has($state)) {
            throw UnknownState::make();
        }

        return $states
            ->get($state)
            ->transitions
            ->pluck('to');
    }

    public function transitionTo(Model $relatedModel, BackedEnum|string $newState, array $additionalData = [])
    {
        $newStateVal = $newState;

        if (! is_string($newState)) {
            $this->checkValidEnum($newState);
            $newStateVal = $newState->value;
        }

        $currentState = $this->currentState();

        $this->validateTransitionExistence($currentState, $newStateVal);

        $stateTransitionConfig = $this->config->getStateTransitionConfig($currentState, $newStateVal);

        $transitionActions = $this->config->getTransitionActions($currentState, $newStateVal);
        $destinationStateActions = $this->config->getStateActions($newStateVal);
        $actions = $transitionActions->concat($destinationStateActions);

        $stateMachineTransition = new TransitionManager($this->model, $actions, $additionalData);
        $stateMachineTransition->transit();

        if ($this->targetingRelationship($this->state)) {
            $stateInPieces = explode('.', $this->state);
            // Remove the "field" from the state
            array_pop($stateInPieces);

            // We should probably offload this to a transition
            $chain = $this->dynamicMethodChain($this->model, $stateInPieces);
            $chain->associate($relatedModel);
            $this->model->save();
        } else {
            $stateTransitionConfig->getStateTransition()->commitTransition($newState, $this->model, $this->state, $additionalData);
        }
    }

    protected function isValidTransition(string $sourceState, string $destinationState): bool
    {
        return $this->getStateTransitions($sourceState)->contains($destinationState);
    }

    private function validateTransitionExistence(string $sourceState, string $destinationState): void
    {
        $states = $this->config->states;

        if (! $states->has($sourceState) || ! $states->has($destinationState)) {
            throw UnknownState::make();
        }

        if (! $this->isValidTransition($sourceState, $destinationState)) {
            throw InvalidTransition::make($sourceState, $destinationState);
        }
    }

    private function currentState(): ?string
    {
        if ($this->targetingRelationship($this->state)) {
            $stateInPieces = explode('.', $this->state);

            $field = array_pop($stateInPieces);

            $modelWithState = $this->accessNestedRelations($this->model, $stateInPieces);

            return $modelWithState->{$field}->value;
        }

        $state = $this->model->{$this->state};

        if (! $state) {
            $state = $this->config->initial;

            if (! $state) {
                throw UnknownState::make();
            }

            return $state;
        }

        return $this->model->{$this->state}->value;
    }

    private function checkValidEnum(BackedEnum $state)
    {
        throw_unless($state instanceof $this->enumClass, UnknownState::make());
    }
}
