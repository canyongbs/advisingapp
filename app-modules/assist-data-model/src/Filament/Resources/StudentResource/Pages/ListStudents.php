<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\Engagement\Filament\Actions\BulkEngagementAction;
use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\Notifications\Filament\Actions\SubscribeBulkAction;
use Filament\Tables\Actions\CreateAction as TableCreateAction;
use Assist\Notifications\Filament\Actions\SubscribeTableAction;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('full')
                    ->label('Name')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where('full', 'ilike', "%{$search}%");
                    })
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('mobile')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('sisid')
                    ->searchable(),
                TextColumn::make('otherid')
                    ->searchable(),
            ])
            ->filters([
                Filter::make('subscribed')
                    ->query(fn (Builder $query): Builder => $query->whereRelation('subscriptions.user', 'id', auth()->id())),
                TernaryFilter::make('sap')
                    ->label('SAP'),
                TernaryFilter::make('dual'),
                TernaryFilter::make('ferpa')
                    ->label('FERPA'),
                Filter::make('has_service_request')
                    ->query(fn (Student $record): bool => $record->serviceRequests()->count() > 0),
            ])
            ->actions([
                ViewAction::make(),
                SubscribeTableAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    SubscribeBulkAction::make(),
                    BulkEngagementAction::make(context: 'students'),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                TableCreateAction::make(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
