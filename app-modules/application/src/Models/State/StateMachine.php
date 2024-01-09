<?php

namespace AdvisingApp\Application\Models\State;

use BackedEnum;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Bvtterfly\ModelStateMachine\ConfigLoader;
use Bvtterfly\ModelStateMachine\TransitionManager;
use Bvtterfly\ModelStateMachine\Exceptions\UnknownState;
use Bvtterfly\ModelStateMachine\Exceptions\InvalidTransition;
use AdvisingApp\Application\Models\Concerns\TargetsRelationships;
use Bvtterfly\ModelStateMachine\DataTransferObjects\StateMachineConfig;

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

            // TODO Ensure the field is a backed enum
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
