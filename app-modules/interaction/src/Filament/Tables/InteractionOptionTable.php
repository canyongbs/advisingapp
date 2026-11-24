<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Interaction\Filament\Tables;

use AdvisingApp\Interaction\Enums\InteractableType;
use AdvisingApp\Interaction\Enums\InteractionManagementTab;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\Column;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * The query, filters, and actions shared by the tables of every Interaction Management tab.
 *
 * Outside of a resource page Filament does not authorize actions against the policy, so each action is authorized explicitly.
 */
class InteractionOptionTable
{
    /**
     * @param array<Column> $columns
     */
    public static function configure(Table $table, InteractionManagementTab $tab, array $columns): Table
    {
        $resource = $tab->getResource();

        return $table
            ->query(fn (): Builder => $resource::getEloquentQuery())
            ->recordUrl(fn (Model $record): ?string => $resource::canEdit($record)
                ? $resource::getUrl('edit', ['record' => $record])
                : null)
            ->columns($columns)
            ->filters([
                Filter::make('is_default')
                    ->label('Default')
                    ->query(fn (Builder $query) => $query->where('is_default', true)),
                SelectFilter::make('interactable_type')
                    ->label('Type')
                    ->options(InteractableType::class),
            ])
            ->headerActions([
                CreateAction::make()
                    ->authorize('create', $resource::getModel())
                    ->url(fn (): string => $resource::getUrl('create')),
            ])
            ->recordActions([
                EditAction::make()
                    ->authorize('update')
                    ->url(fn (Model $record): string => $resource::getUrl('edit', ['record' => $record])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorize('deleteAny', $resource::getModel())
                        ->authorizeIndividualRecords('delete'),
                ]),
            ])
            ->defaultGroup('interactable_type');
    }
}
