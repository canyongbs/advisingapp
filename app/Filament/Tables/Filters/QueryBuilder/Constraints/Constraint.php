<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints;

use Closure;
use Filament\Forms\Get;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Components\Component;
use Filament\Forms\Components\Builder\Block;
use Illuminate\Validation\ValidationException;

class Constraint extends Component
{
    use Concerns\HasLabel;
    use Concerns\HasName;
    use Concerns\HasOperators;
    use HasIcon;

    public const OPERATOR_SELECT_NAME = 'operator';

    protected string | Closure | null $attribute = null;

    protected string | Closure | null $relationship = null;

    protected ?Closure $modifyRelationshipQueryUsing = null;

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

                $operator
                    ->constraint($this)
                    ->settings($state['settings'])
                    ->inverse($isInverseOperator);

                try {
                    return $operator->getSummary();
                } finally {
                    $operator
                        ->constraint(null)
                        ->settings(null)
                        ->inverse(null);
                }
            })
            ->icon($this->getIcon())
            ->schema(function (): array {
                $operatorSelectOptions = $this->getOperatorSelectOptions();

                return [
                    Select::make(static::OPERATOR_SELECT_NAME)
                        ->options($operatorSelectOptions)
                        ->default(array_key_first($operatorSelectOptions))
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
                        ->key('settings')
                        ->columnSpan(2)
                        ->columns(2),
                ];
            })
            ->columns(3);
    }

    public function getOperatorSelectOptions(): array
    {
        foreach ($this->getOperators() as $operatorName => $operator) {
            $options[$operatorName] = $operator->inverse(false)->getLabel();
            $options["{$operatorName}.inverse"] = $operator->inverse()->getLabel();

            $operator->inverse(null);
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

    public function attribute(string | Closure | null $name): static
    {
        $this->attribute = $name;

        return $this;
    }

    public function relationship(string $name, string $titleAttribute, ?Closure $modifyQueryUsing = null): static
    {
        $this->attribute("{$name}.{$titleAttribute}");

        $this->modifyRelationshipQueryUsing = $modifyQueryUsing;

        return $this;
    }

    public function getAttribute(): string
    {
        return $this->evaluate($this->attribute) ?? $this->getName();
    }

    public function queriesRelationships(): bool
    {
        return str($this->getAttribute())->contains('.');
    }

    public function getRelationshipName(): string
    {
        return (string) str($this->getAttribute())->beforeLast('.');
    }

    public function getAttributeForQuery(): string
    {
        return (string) str($this->getAttribute())->afterLast('.');
    }

    public function getModifyRelationshipQueryUsing(): ?Closure
    {
        return $this->modifyRelationshipQueryUsing;
    }
}
