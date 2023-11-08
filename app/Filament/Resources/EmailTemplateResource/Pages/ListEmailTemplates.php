<?php

namespace App\Filament\Resources\EmailTemplateResource\Pages;

use Filament\Tables\Table;
use App\Models\EmailTemplate;
use Filament\Actions\CreateAction;
use Assist\Division\Models\Division;
use Filament\Tables\Actions\EditAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Columns\OpenSearch\TextColumn;
use App\Filament\Resources\EmailTemplateResource;
use Assist\Division\Filament\Resources\DivisionResource;

class ListEmailTemplates extends ListRecords
{
    protected static string $resource = EmailTemplateResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('model.name')
                    ->label('Related To')
                    ->getStateUsing(fn (EmailTemplate $record): ?string => $record->model->name)
                    ->url(fn (EmailTemplate $record) => match ($record->model ? $record->model::class : null) {
                        Division::class => DivisionResource::getUrl('edit', ['record' => $record->model]),
                        default => null,
                    }),
            ])
            ->filters([
            ])
            ->actions([
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
            CreateAction::make(),
        ];
    }
}
