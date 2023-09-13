<?php

namespace Assist\Prospect\Filament\Resources\ProspectResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use Assist\Prospect\Models\Prospect;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\Prospect\Filament\Resources\ProspectResource;
use Assist\Engagement\Filament\Actions\BulkEngagementAction;

class ListProspects extends ListRecords
{
    protected static string $resource = ProspectResource::class;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('full')
                    ->label('Name')
                    ->translateLabel()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where('full', 'ilike', "%{$search}%");
                    })
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('mobile')
                    ->label('Mobile')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->translateLabel()
                    ->state(function (Prospect $record) {
                        return $record->status->name;
                    })
                    ->color(function (Prospect $record) {
                        return $record->status->color;
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->join('prospect_statuses', 'prospects.status_id', '=', 'prospect_statuses.id')
                            ->orderBy('prospect_statuses.name', $direction);
                    }),
                TextColumn::make('source.name')
                    ->label('Source')
                    ->translateLabel()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->translateLabel()
                    ->dateTime('g:ia - M j, Y ')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->relationship('status', 'name')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('source')
                    ->relationship('source', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkEngagementAction::make(context: 'prospects'),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
