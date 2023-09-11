<?php

namespace Assist\Prospect\Filament\Resources\ProspectResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
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
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where('email', 'ilike', "%{$search}%");
                    })
                    ->sortable(),
                TextColumn::make('mobile')
                    ->label('Mobile')
                    ->translateLabel()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where('mobile', 'ilike', "%{$search}%");
                    })
                    ->sortable(),
                TextColumn::make('birthdate')
                    ->label('Birthdate')
                    ->translateLabel()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where('birthdate', 'ilike', "%{$search}%");
                    })
                    ->sortable(),
            ])
            ->filters([
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
