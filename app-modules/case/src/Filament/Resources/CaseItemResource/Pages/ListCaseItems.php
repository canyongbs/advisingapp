<?php

namespace Assist\Case\Filament\Resources\CaseItemResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\Case\Filament\Resources\CaseItemResource;

class ListCaseItems extends ListRecords
{
    protected static string $resource = CaseItemResource::class;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('casenumber')
                    ->label('Case #')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('respondent.full')
                    ->label('Student')
                    ->searchable()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        // TODO: Look into issues with the Power Joins package being able to handle this
                        //ray($query->joinRelationship('respondent', [
                        //    'respondent' => [
                        //        'students' => function ($join) {
                        //            // ...
                        //        },
                        //    ],
                        //])->toSql());

                        // Update this if any other relations are added to the CaseItem model respondent relationship
                        return $query->join('students', function (JoinClause $join) {
                            $join->on('case_items.respondent_id', '=', 'students.sisid')
                                ->where('case_items.respondent_type', '=', 'student');
                        })->orderBy('full', $direction);
                    }),
                TextColumn::make('respondent.sisid')
                    ->label('SIS ID')
                    ->searchable()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        // Update this if any other relations are added to the CaseItem model respondent relationship
                        return $query->join('students', function (JoinClause $join) {
                            $join->on('case_items.respondent_id', '=', 'students.sisid')
                                ->where('case_items.respondent_type', '=', 'student');
                        })->orderBy('sisid', $direction);
                    }),
                TextColumn::make('respondent.otherid')
                    ->label('Other ID')
                    ->searchable()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        // Update this if any other relations are added to the CaseItem model respondent relationship
                        return $query->join('students', function (JoinClause $join) {
                            $join->on('case_items.respondent_id', '=', 'students.sisid')
                                ->where('case_items.respondent_type', '=', 'student');
                        })->orderBy('otherid', $direction);
                    }),
                TextColumn::make('institution.name')
                    ->label('Institution')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('assignedTo.name')
                    ->label('Assigned to')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                // TODO: Figure out how to get this to display a list of existing items rather than a search
                SelectFilter::make('priority')
                    ->relationship('priority', 'name')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('state')
                    ->relationship('state', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Add Case'),
        ];
    }
}
