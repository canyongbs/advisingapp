<?php

namespace Assist\Case\Filament\Resources\ServiceRequestResource\Pages;

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
use Assist\Case\Filament\Resources\ServiceRequestResource;

class ListServiceRequests extends ListRecords
{
    protected static string $resource = ServiceRequestResource::class;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('service_request_number')
                    ->label('Service Request #')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('respondent.full')
                    ->label('Respondent')
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

                        // Update this if any other relations are added to the ServiceRequest model respondent relationship
                        return $query->join('students', function (JoinClause $join) {
                            $join->on('service_requests.respondent_id', '=', 'students.sisid')
                                ->where('service_requests.respondent_type', '=', 'student');
                        })->orderBy('full', $direction);
                    }),
                TextColumn::make('respondent.sisid')
                    ->label('SIS ID')
                    ->searchable()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        // Update this if any other relations are added to the ServiceRequest model respondent relationship
                        return $query->join('students', function (JoinClause $join) {
                            $join->on('service_requests.respondent_id', '=', 'students.sisid')
                                ->where('service_requests.respondent_type', '=', 'student');
                        })->orderBy('sisid', $direction);
                    }),
                TextColumn::make('respondent.otherid')
                    ->label('Other ID')
                    ->searchable()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        // Update this if any other relations are added to the ServiceRequest model respondent relationship
                        return $query->join('students', function (JoinClause $join) {
                            $join->on('service_requests.respondent_id', '=', 'students.sisid')
                                ->where('service_requests.respondent_type', '=', 'student');
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
                SelectFilter::make('priority')
                    ->relationship('priority', 'name')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('status')
                    ->relationship('status', 'name')
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
                ->label('Add Service Request'),
        ];
    }
}
