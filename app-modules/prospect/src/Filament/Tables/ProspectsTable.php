<?php

namespace AdvisingApp\Prospect\Filament\Tables;

use Filament\Tables\Table;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use AdvisingApp\Prospect\Models\Prospect;
use Filament\Tables\Filters\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;

class ProspectsTable
{
    public function __invoke(Table $table): Table
    {
        return $table
            ->query(fn () => Prospect::query())
            ->columns([
                TextColumn::make(Prospect::displayNameKey())
                    ->label('Name')
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->sortable(),
                TextColumn::make('mobile')
                    ->label('Mobile')
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
                        QueryBuilder\Constraints\DateConstraint::make('created_at')
                            ->icon('heroicon-m-calendar'),
                        TextConstraint::make('email')
                            ->label('Email Address')
                            ->icon('heroicon-m-envelope'),
                        TextConstraint::make('email_2')
                            ->label('Email Address 2')
                            ->icon('heroicon-m-envelope'),
                        TextConstraint::make('mobile')
                            ->icon('heroicon-m-phone'),
                        TextConstraint::make('phone')
                            ->icon('heroicon-m-phone'),
                        TextConstraint::make('address')
                            ->icon('heroicon-m-map-pin'),
                        TextConstraint::make('address_2')
                            ->icon('heroicon-m-map-pin'),
                        BooleanConstraint::make('sms_opt_out')
                            ->label('SMS Opt Out')
                            ->icon('heroicon-m-chat-bubble-bottom-center'),
                        BooleanConstraint::make('email_bounce')
                            ->icon('heroicon-m-arrow-uturn-left'),
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
                    ])
                    ->constraintPickerColumns([
                        'md' => 2,
                        'lg' => 3,
                        'xl' => 4,
                    ])
                    ->constraintPickerWidth('7xl'),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                ViewAction::make()
                    ->authorize('view')
                    ->url(fn (Prospect $record) => ProspectResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
