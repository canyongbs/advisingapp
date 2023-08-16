<?php

namespace Assist\Audit\Filament\Resources\AuditResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Assist\Audit\Filament\Resources\AuditResource;

class ListAudits extends ListRecords
{
    protected static string $resource = AuditResource::class;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('auditable_type')
                    ->label('Auditable')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Change Agent (User)')
                    ->sortable(),
                TextColumn::make('event')
                    ->label('Event')
                    ->sortable(),
            ])
            ->filters([
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
