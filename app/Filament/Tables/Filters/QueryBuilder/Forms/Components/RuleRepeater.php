<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Forms\Components;

use App\Filament\Tables\Filters\QueryBuilder\Concerns\HasRules;
use App\Filament\Tables\Filters\QueryBuilder\Rules\Rule;
use Assist\Prospect\Filament\Resources\ProspectResource;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class RuleRepeater extends Repeater
{
    use HasRules;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schema(fn (): array => [
            Checkbox::make('not')
                ->label('NOT'),
            Builder::make('rules')
                ->blocks(function (Builder $component): array {
                    $builder = $component;

                    return [
                        ...array_map(
                            fn (Rule $rule): Builder\Block => $rule->getBuilderBlock(),
                            $this->getRules(),
                        ),
                        Builder\Block::make('orGroup')
                            ->label(function (?array $state, ?string $uuid) use ($builder) {
                                if (blank($state) || blank($uuid)) {
                                    return 'Nested OR';
                                }

                                if (! count($state['groups'] ?? [])) {
                                    return '(No rules)';
                                }

                                $repeater = $builder->getChildComponentContainers()[$uuid]
                                    ->getComponent(fn (Component $component): bool => $component instanceof RuleRepeater);

                                $itemLabels = collect($repeater->getChildComponentContainers())
                                    ->map(fn (ComponentContainer $blockContainer, string $blockContainerUuid): string => $repeater->getItemLabel($blockContainerUuid));

                                return ($state['not'] ? 'NOT ' : '') . '(' . $itemLabels->implode(') OR (') . ')';
                            })
                            ->icon('heroicon-m-bars-4')
                            ->schema(fn (): array => [
                                Checkbox::make('not')
                                    ->label('NOT'),
                                static::make('groups')
                                    ->hiddenLabel(),
                            ]),
                    ];
                })
                ->addAction(fn (Action $action) => $action
                    ->label('Add rule')
                    ->icon('heroicon-s-plus'))
                ->addBetweenAction(fn (Action $action) => $action->hidden())
                ->labelBetweenItems('AND')
                ->hiddenLabel()
                ->blockNumbers(false)
                ->collapsible()
                ->cloneable()
                ->reorderable(false)
                ->expandAllAction(fn (Action $action) => $action->hidden())
                ->collapseAllAction(fn (Action $action) => $action->hidden())
                ->truncateBlockLabel(false),
        ]);

        $this->addAction(fn (Action $action) => $action
            ->label('Add rule group')
            ->icon('heroicon-s-plus'));

        $this->labelBetweenItems('OR');

        $this->collapsible();

        $this->expandAllAction(fn (Action $action) => $action->hidden());

        $this->collapseAllAction(fn (Action $action) => $action->hidden());

        $this->itemLabel(function (ComponentContainer $containter, array $state): string {
            $builder = $containter->getComponent(fn (Component $component): bool => $component instanceof Builder);

            $blockLabels = collect($builder->getChildComponentContainers())
                ->map(fn (ComponentContainer $blockContainer, string $blockUuid): string => $blockContainer->getParentComponent()->getLabel($blockContainer->getRawState(), $blockUuid));

            if ($blockLabels->isEmpty()) {
                return '(No rules)';
            }

            return ($state['not'] ? 'NOT ' : '') . '(' .  $blockLabels->implode(') AND (') . ')';
        });

        $this->truncateItemLabel(false);

        $this->cloneable();

        $this->reorderable(false);
    }
}
