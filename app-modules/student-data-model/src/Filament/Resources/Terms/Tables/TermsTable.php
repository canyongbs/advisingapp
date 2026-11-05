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

namespace AdvisingApp\StudentDataModel\Filament\Resources\Terms\Tables;

use AdvisingApp\StudentDataModel\Models\Scopes\OrderByMostRecentTerm;
use AdvisingApp\StudentDataModel\Models\Term;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Terms synced from the SIS are listed read-only by start date; otherwise they are maintained and ordered manually.
 */
class TermsTable
{
    public static function configure(Table $table): Table
    {
        $table->paginated([50, 100, 500, 1000]);

        return Term::hasSisTerms()
            ? static::configureForSisTerms($table)
            : static::configureForManualTerms($table);
    }

    protected static function configureForSisTerms(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->tap(new OrderByMostRecentTerm()))
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('code'),
                TextColumn::make('start_date')
                    ->date(),
                TextColumn::make('end_date')
                    ->date(),
            ]);
    }

    protected static function configureForManualTerms(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->select('terms.*')
                ->selectRaw('EXISTS (SELECT 1 FROM enrollments WHERE enrollments.semester_name::citext = terms.name AND enrollments.deleted_at IS NULL) AS is_mapped'))
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('is_mapped')
                    ->label('Mapping')
                    ->badge()
                    ->formatStateUsing(fn (mixed $state): string => $state ? 'Mapped' : 'Unmapped')
                    ->color(fn (mixed $state): string => $state ? 'success' : 'gray'),
            ])
            ->defaultSort('order', 'desc')
            ->reorderable('order', condition: auth()->user()->can('settings.*.update'), direction: 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorizeIndividualRecords('delete'),
                ]),
            ]);
    }
}
