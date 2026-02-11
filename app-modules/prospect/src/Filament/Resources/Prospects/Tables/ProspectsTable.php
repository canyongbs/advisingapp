<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Prospect\Filament\Resources\Prospects\Tables;

use AdvisingApp\Prospect\Filament\Resources\Prospects\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProspectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(fn () => Prospect::query())
            ->columns([
                TextColumn::make(Prospect::displayNameKey())
                    ->label('Name')
                    ->sortable(),
                TextColumn::make('primaryEmailAddress.address')
                    ->label('Email')
                    ->sortable(),
                TextColumn::make('primaryPhoneNumber.number')
                    ->label('Phone')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->state(function (Prospect $record) {
                        return $record->status->name;
                    })
                    ->color(function (Prospect $record) {
                        return $record->status->color->value;
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->join('prospect_statuses', 'prospects.status_id', '=', 'prospect_statuses.id')
                            ->orderBy('prospect_statuses.name', $direction);
                    }),
                TextColumn::make('source.name')
                    ->label('Source')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('g:ia - M j, Y ')
                    ->sortable(),
            ])
            ->filters([
                QueryBuilder::make()
                    // WARNING: Removing constraints from this list requires a data migration to clean up
                    // existing Groups that reference the removed filter types. See docs/explanations/maintaining-group-filters.md
                    ->constraints([
                        TextConstraint::make('first_name')
                            ->icon('heroicon-m-user'),
                        TextConstraint::make('last_name')
                            ->icon('heroicon-m-user'),
                        TextConstraint::make('full_name')
                            ->icon('heroicon-m-user'),
                        TextConstraint::make('preferred')
                            ->label('Preferred Name')
                            ->icon('heroicon-m-user'),
                        DateConstraint::make('created_at')
                            ->icon('heroicon-m-calendar'),
                        TextConstraint::make('email')
                            ->label('Primary Email')
                            ->relationship('primaryEmailAddress', 'address')
                            ->icon('heroicon-m-envelope'),
                        TextConstraint::make('phone')
                            ->label('Primary Phone')
                            ->relationship('primaryPhoneNumber', 'number')
                            ->icon('heroicon-m-phone'),
                        TextConstraint::make('address')
                            ->label('Primary Address line 1')
                            ->relationship('primaryAddress', 'line_1')
                            ->icon('heroicon-m-map-pin'),
                        TextConstraint::make('address_2')
                            ->label('Primary Address line 2')
                            ->relationship('primaryAddress', 'line_2')
                            ->icon('heroicon-m-map-pin'),
                        RelationshipConstraint::make('tags')
                            ->label('Tags')
                            ->icon('heroicon-m-rectangle-group')
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->multiple()
                                    ->preload(),
                            ),
                        TextConstraint::make('hsgrad')
                            ->label('HS Grad')
                            ->icon('heroicon-m-academic-cap'),
                        RelationshipConstraint::make('status')
                            ->icon('heroicon-m-flag')
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('source')
                            ->icon('heroicon-m-arrow-left-on-rectangle')
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->multiple()
                                    ->preload(),
                            ),
                        Constraint::make('subscribed')
                            ->icon('heroicon-m-bell')
                            ->operators([
                                Operator::make('subscribed')
                                    ->label(fn (bool $isInverse): string => $isInverse ? 'Not subscribed' : 'Subscribed')
                                    ->summary(fn (bool $isInverse): string => $isInverse ? 'You are not subscribed' : 'You are subscribed')
                                    ->baseQuery(fn (Builder $query, bool $isInverse) => $query->{$isInverse ? 'whereDoesntHave' : 'whereHas'}(
                                        'subscriptions.user',
                                        fn (Builder $query) => $query->whereKey(auth()->user()),
                                    )),
                            ]),
                        Constraint::make('careTeam')
                            ->icon('heroicon-m-user-group')
                            ->operators([
                                Operator::make('careTeam')
                                    ->label(fn (bool $isInverse): string => $isInverse ? 'Not my care team' : 'My care team')
                                    ->summary('Care team')
                                    ->baseQuery(fn (Builder $query, bool $isInverse) => $query->{$isInverse ? 'whereDoesntHave' : 'whereHas'}(
                                        'careTeam',
                                        fn (Builder $query) => $query->whereKey(auth()->user()),
                                    )),
                            ]),
                    ])
                    ->constraintPickerColumns([
                        'md' => 2,
                        'lg' => 3,
                        'xl' => 4,
                    ])
                    ->constraintPickerWidth('7xl'),
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                ViewAction::make()
                    ->authorize('view')
                    ->url(fn (Prospect $record) => ProspectResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
