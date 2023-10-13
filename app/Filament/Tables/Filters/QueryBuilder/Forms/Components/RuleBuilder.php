<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Forms\Components;

use App\Filament\Tables\Filters\QueryBuilder\Concerns\HasConstraints;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
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
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class RuleBuilder extends Builder
{
    use HasConstraints;

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->blocks(function (Builder $component): array {
                return [
                    ...array_map(
                        fn (Constraint $constraint): Builder\Block => $constraint->getBuilderBlock(),
                        $this->getConstraints(),
                    ),
                    Builder\Block::make('or')
                        ->label(function (?array $state, ?string $uuid) use ($component) {
                            if (blank($state) || blank($uuid)) {
                                return 'Disjunction (OR)';
                            }

                            if (! count($state['groups'] ?? [])) {
                                return '(No rules)';
                            }

                            $repeater = $component->getChildComponentContainer($uuid)
                                ->getComponent(fn (Component $component): bool => $component instanceof Repeater);

                            $itemLabels = collect($repeater->getChildComponentContainers())
                                ->map(fn (ComponentContainer $blockContainer, string $blockContainerUuid): string => $repeater->getItemLabel($blockContainerUuid));

                            return (($state['not'] ?? false) ? 'NOT ' : '') . '(' . $itemLabels->implode(') OR (') . ')';
                        })
                        ->icon('heroicon-m-bars-4')
                        ->schema(fn (): array => [
                            Repeater::make('groups')
                                ->schema(fn (): array => [
                                    static::make('rules')
                                        ->constraints($this->getConstraints()),
                                ])
                                ->addAction(fn (Action $action) => $action
                                    ->label('Add rule group')
                                    ->icon('heroicon-s-plus'))
                                ->labelBetweenItems('OR')
                                ->collapsible()
                                ->expandAllAction(fn (Action $action) => $action->hidden())
                                ->collapseAllAction(fn (Action $action) => $action->hidden())
                                ->itemLabel(function (ComponentContainer $container, array $state): string {
                                    $builder = $container->getComponent(fn (Component $component): bool => $component instanceof RuleBuilder);

                                    $blockLabels = collect($builder->getChildComponentContainers())
                                        ->map(fn (ComponentContainer $blockContainer, string $blockUuid): string => $blockContainer->getParentComponent()->getLabel($blockContainer->getRawState(), $blockUuid));

                                    if ($blockLabels->isEmpty()) {
                                        return '(No rules)';
                                    }

                                    return (($state['not'] ?? false) ? 'NOT ' : '') . '(' .  $blockLabels->implode(') AND (') . ')';
                                })
                                ->truncateItemLabel(false)
                                ->cloneable()
                                ->reorderable(false)
                                ->hiddenLabel()
                                ->generateUuidUsing(fn (): string => Str::random(4)),
                            Checkbox::make('not')
                                ->label('NOT'),
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
            ->truncateBlockLabel(false)
            ->generateUuidUsing(fn (): string => Str::random(4));
    }
}
