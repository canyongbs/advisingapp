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

    /**
     * @return Collection
     */
    public function getAllStates(): Collection
    {
        return $this->config->states->keys();
    }

    /**
     * @param BackedEnum|string|null $state
     *
     * @throws \Exception
     *
     * @return Collection
     */
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

    public function transitionTo(Model $contextModel, Model $relatedModel, BackedEnum|string $newState, array $additionalData = [])
    {
        ray('transitionTo()', $contextModel, $relatedModel, $newState, $additionalData);

        $newStateVal = $newState;

        if (! is_string($newState)) {
            $this->checkValidEnum($newState);
            $newStateVal = $newState->value;
        }

        $currentState = $this->currentState();

        ray('currentState value', $currentState);
        ray('newState value', $newStateVal);

        $this->validateTransitionExistence($currentState, $newStateVal);

        $stateTransitionConfig = $this->config->getStateTransitionConfig($currentState, $newStateVal);

        $transitionActions = $this->config->getTransitionActions($currentState, $newStateVal);
        $destinationStateActions = $this->config->getStateActions($newStateVal);
        $actions = $transitionActions->concat($destinationStateActions);

        $stateMachineTransition = new TransitionManager($contextModel, $actions, $additionalData);
        $stateMachineTransition->transit();

        if ($this->targetingRelationship($this->state)) {
            $stateInPieces = explode('.', $this->state);
            // Remove the "field" from the state
            array_pop($stateInPieces);

            // We should probably offload this to a transition
            $chain = $this->accessNestedRelations($contextModel, $stateInPieces);
            $chain->associate($relatedModel);
            $contextModel->save();
        } else {
            $stateTransitionConfig->getStateTransition()->commitTransition($newState, $this->model, $this->state, $additionalData);
        }
    }

    public function accessNestedRelations($model, $relations)
    {
        $current = $model;

        foreach ($relations as $relation) {
            if (! method_exists($current, $relation)) {
                throw new \Exception("Relation '{$relation}' does not exist on " . get_class($current));
            }

            $current = $current->{$relation};

            if ($current === null) {
                return null;
            }
        }

        return $current;
    }

    /**
     * @param  string  $sourceState
     * @param  string  $destinationState
     *
     * @throws Exception
     *
     * @return bool
     */
    protected function isValidTransition(string $sourceState, string $destinationState): bool
    {
        return $this->getStateTransitions($sourceState)->contains($destinationState);
    }

    /**
     * @param string $sourceState
     * @param string $destinationState
     *
     * @throws \Exception
     */
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

            $model = $this->accessNestedRelations($this->model, $stateInPieces);

            return $model->{$field}->value;
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
