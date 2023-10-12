<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints;

use App\Filament\Tables\Filters\QueryBuilder\Constraints\Concerns\HasLabel;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Component as FormComponent;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Support\Components\Component;
use Filament\Support\Concerns\HasIcon;
use Illuminate\Validation\ValidationException;

class Constraint extends Component
{
    use Concerns\HasLabel;
    use Concerns\HasName;
    use Concerns\HasOperators;
    use HasIcon;

    const OPERATOR_SELECT_NAME = 'operator';

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public function getBuilderBlock(): Block
    {
        return Block::make($this->getName())
            ->label(function (Block $component, ?array $state, ?string $uuid) {
                if (blank($state[static::OPERATOR_SELECT_NAME] ?? null)) {
                    return $this->getLabel();
                }

                [$operatorName, $isInverseOperator] = $this->parseOperatorString($state[static::OPERATOR_SELECT_NAME]);

                $operator = $this->getOperator($operatorName);

                if (! $operator) {
                    return $this->getLabel();
                }

                try {
                    $component->getContainer()->getParentComponent()
                        ->getChildComponentContainer($uuid)
                        ->getComponent('settings')
                        ->getChildComponentContainer()
                        ->validate();
                } catch (ValidationException $exception) {
                    return $this->getLabel();
                }

                return $operator->getSummary($this->getLabel(), $state['settings'], $isInverseOperator);
            })
            ->icon($this->getIcon())
            ->schema(fn (): array => [
                Select::make(static::OPERATOR_SELECT_NAME)
                    ->options($this->getOperatorSelectOptions())
                    ->live()
                    ->afterStateUpdated(fn (Select $component, Get $get) => $component
                        ->getContainer()
                        ->getComponent('settings')
                        ->getChildComponentContainer()
                        ->fill($get('settings'))),
                Group::make(function ($component, Get $get): array {
                    $operator = $get(static::OPERATOR_SELECT_NAME);

                    if (blank($operator)) {
                        return [];
                    }

                    [$operatorName] = $this->parseOperatorString($operator);

                    $operator = $this->getOperator($operatorName);

                    if (! $operator) {
                        return [];
                    }

                    return $operator->getFormSchema();
                })
                    ->statePath('settings')
                    ->key('settings'),
            ])
            ->columns(3);
    }

    public function getOperatorSelectOptions(): array
    {
        foreach ($this->getOperators() as $operatorName => $operator) {
            $options[$operatorName] = $operator->getLabel(isInverse: false);
            $options["{$operatorName}.inverse"] = $operator->getLabel(isInverse: true);
        }

        return $options;
    }

    public function parseOperatorString(string $operator): array
    {
        if (str($operator)->endsWith('.inverse')) {
            return [(string) str($operator)->beforeLast('.'), true];
        }

        return [$operator, false];
    }
}
