<?php

namespace AdvisingApp\Prospect\Filament\Resources\ProspectCategoryResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use AdvisingApp\Prospect\Filament\Resources\ProspectCategoryResource;

class CreateProspectCategory extends CreateRecord
{
    protected static string $resource = ProspectCategoryResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Category Name')
                    ->required()
                    ->string()
                    ->maxLength(255)
                    ->unique(),
                Textarea::make('description')
                    ->label('Description')
                    ->maxLength(65535)
                    ->string(),
            ])->columns(1);
    }
}
