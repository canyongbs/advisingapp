<?php

namespace Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use App\Concerns\FilterTableWithOpenSearch;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\ServiceManagement\Models\ServiceRequest;
use App\Filament\Columns\OpenSearch\TextColumn as OpenSearchTextColumn;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource;
use App\Filament\Filters\OpenSearch\SelectFilter as OpenSearchSelectFilter;

class ListServiceRequests extends ListRecords
{
    use FilterTableWithOpenSearch;

    protected static string $resource = ServiceRequestResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                OpenSearchTextColumn::make('service_request_number')
                    ->label('Service Request #')
                    ->searchable()
                    ->sortable(),
                OpenSearchTextColumn::make('respondent_name')
                    ->label('Related To')
                    ->getStateUsing(fn (ServiceRequest $record) => $record->respondent->{$record->respondent::displayNameKey()})
                    ->searchable(query: fn (Builder $query, $search) => $query->educatableSearch(relationship: 'respondent', search: $search))
                // TODO: Find a way to get IDE to recognize educatableSort() method
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query->educatableSort($direction)),
                OpenSearchTextColumn::make('respondent_id')
                    ->label('SIS ID')
                    ->searchable()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        // Update this if any other relations are added to the ServiceRequest model respondent relationship
                        return $query->join('students', function (JoinClause $join) {
                            $join->on('service_requests.respondent_id', '=', 'students.sisid')
                                ->where('service_requests.respondent_type', '=', 'student');
                        })->orderBy('sisid', $direction);
                    }),
                OpenSearchTextColumn::make('respondent_otherid')
                    ->label('Other ID')
                    ->searchable()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        // Update this if any other relations are added to the ServiceRequest model respondent relationship
                        return $query->join('students', function (JoinClause $join) {
                            $join->on('service_requests.respondent_id', '=', 'students.sisid')
                                ->where('service_requests.respondent_type', '=', 'student');
                        })->orderBy('otherid', $direction);
                    })
                    ->getStateUsing(fn (ServiceRequest $record) => $record->respondent->otherid),
                OpenSearchTextColumn::make('division_name')
                    ->label('Division')
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(fn (ServiceRequest $record) => $record->division->name),
                OpenSearchTextColumn::make('assigned_to_name')
                    ->label('Assigned to')
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(fn (ServiceRequest $record) => $record->assignedTo->name),
            ])
            ->filters([
                OpenSearchSelectFilter::make('priority_id')
                    ->label('Priority')
                    ->relationship('priority', 'name')
                    ->multiple()
                    ->preload(),
                OpenSearchSelectFilter::make('status_id')
                    ->label('Status')
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
