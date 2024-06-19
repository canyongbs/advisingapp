<?php

namespace AdvisingApp\Prospect\Filament\Resources\ProspectCategoryResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use AdvisingApp\Prospect\Filament\Resources\ProspectCategoryResource;

class EditProspectCategory extends EditRecord
{
    protected static string $resource = ProspectCategoryResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Category Name')
                    ->required()
                    ->maxLength(255)
                    ->string(),
                Textarea::make('description')
                    ->label('Description')
                    ->maxLength(255)
                    ->string(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
