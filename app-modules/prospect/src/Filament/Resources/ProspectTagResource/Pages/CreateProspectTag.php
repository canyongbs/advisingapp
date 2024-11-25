<?php

namespace AdvisingApp\Prospect\Filament\Resources\ProspectTagResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use AdvisingApp\Prospect\Filament\Resources\ProspectTagResource;

class CreateProspectTag extends CreateRecord
{
    protected static string $resource = ProspectTagResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->string(),
            ]);
    }
}
