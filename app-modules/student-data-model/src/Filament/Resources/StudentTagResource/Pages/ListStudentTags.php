<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\StudentTagResource\Pages;

use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentTagResource;

class ListStudentTags extends ListRecords
{
    protected static string $resource = StudentTagResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Created At'),
            ])
            ->filters([])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
