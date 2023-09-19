<?php

namespace App\Filament\Resources\CaseloadResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\CaseloadResource;

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
            Actions\CreateAction::make(),
        ];
    }
}
