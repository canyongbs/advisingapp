<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

namespace App\Filament\Tables\Filters\QueryBuilder\Forms\Components;

use Exception;
use Illuminate\Support\Str;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use App\Filament\Tables\Filters\QueryBuilder\Concerns\HasConstraints;

class RuleBuilder extends Builder
{
    use HasConstraints;

    public const OR_BLOCK_NAME = 'or';

    public const OR_BLOCK_GROUPS_REPEATER_NAME = 'groups';

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
                    Builder\Block::make(static::OR_BLOCK_NAME)
                        ->label(function (?array $state, ?string $uuid) use ($component) {
                            if (blank($state) || blank($uuid)) {
                                return __('filament-tables::filters/query-builder.form.or_groups.block.label');
                            }

                            if (! count($state[static::OR_BLOCK_GROUPS_REPEATER_NAME] ?? [])) {
                                return __('filament-tables::filters/query-builder.no_rules');
                            }

                            $repeater = $component->getChildComponentContainer($uuid)
                                ->getComponent(fn (Component $component): bool => $component instanceof Repeater);

                            if (! ($repeater instanceof Repeater)) {
                                throw new Exception('No repeater component found.');
                            }

                            $itemLabels = collect($repeater->getChildComponentContainers())
                                ->map(fn (ComponentContainer $blockContainer, string $blockContainerUuid): string => $repeater->getItemLabel($blockContainerUuid));

                            if ($itemLabels->count() === 1) {
                                return $itemLabels->first();
                            }

                            return '(' . $itemLabels->implode(') ' . __('filament-tables::filters/query-builder.form.or_groups.block.or') . ' (') . ')';
                        })
                        ->icon('heroicon-m-bars-4')
                        ->schema(fn (): array => [
                            Repeater::make(static::OR_BLOCK_GROUPS_REPEATER_NAME)
                                ->label(__('filament-tables::filters/query-builder.form.or_groups.label'))
                                ->schema(fn (): array => [
                                    static::make('rules')
                                        ->constraints($this->getConstraints())
                                        ->blockPickerColumns($this->getBlockPickerColumns())
                                        ->blockPickerWidth($this->getBlockPickerWidth()),
                                ])
                                ->addAction(fn (Action $action) => $action
                                    ->label(__('filament-tables::filters/query-builder.actions.add_rule_group.label'))
                                    ->icon('heroicon-s-plus'))
                                ->labelBetweenItems(__('filament-tables::filters/query-builder.item_separators.or'))
                                ->collapsible()
                                ->expandAllAction(fn (Action $action) => $action->hidden())
                                ->collapseAllAction(fn (Action $action) => $action->hidden())
                                ->itemLabel(function (ComponentContainer $container, array $state): string {
                                    $builder = $container->getComponent(fn (Component $component): bool => $component instanceof RuleBuilder);

                                    if (! ($builder instanceof RuleBuilder)) {
                                        throw new Exception('No rule builder component found.');
                                    }

                                    $blockLabels = collect($builder->getChildComponentContainers())
                                        ->map(function (ComponentContainer $blockContainer, string $blockUuid): string {
                                            $block = $blockContainer->getParentComponent();

                                            if (! ($block instanceof Builder\Block)) {
                                                throw new Exception('No block component found.');
                                            }

                                            return $block->getLabel($blockContainer->getRawState(), $blockUuid);
                                        });

                                    if ($blockLabels->isEmpty()) {
                                        return __('filament-tables::filters/query-builder.no_rules');
                                    }

                                    if ($blockLabels->count() === 1) {
                                        return $blockLabels->first();
                                    }

                                    return '(' . $blockLabels->implode(') ' . __('filament-tables::filters/query-builder.form.rules.item.and') . ' (') . ')';
                                })
                                ->truncateItemLabel(false)
                                ->cloneable()
                                ->reorderable(false)
                                ->hiddenLabel()
                                ->generateUuidUsing(fn (): string => Str::random(4)),
                        ]),
                ];
            })
            ->addAction(fn (Action $action) => $action
                ->label(__('filament-tables::filters/query-builder.actions.add_rule.label'))
                ->icon('heroicon-s-plus'))
            ->addBetweenAction(fn (Action $action) => $action->hidden())
            ->label(__('filament-tables::filters/query-builder.form.rules.label'))
            ->hiddenLabel()
            ->labelBetweenItems(__('filament-tables::filters/query-builder.item_separators.and'))
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
