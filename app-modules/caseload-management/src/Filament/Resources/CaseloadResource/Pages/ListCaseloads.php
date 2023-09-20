<?php

namespace Assist\CaseloadManagement\Filament\Resources\CaseloadResource\Pages;

use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Assist\CaseloadManagement\Filament\Resources\CaseloadResource;

class ListCaseloads extends ListRecords
{
    protected static string $resource = CaseloadResource::class;

    public function table(Table $table): Table
    {
        return parent::table($table)->columns([
            TextColumn::make('name'),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
