<?php

namespace Assist\Team\Filament\Resources\TeamResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Assist\Team\Filament\Resources\TeamResource;

class CreateTeam extends CreateRecord
{
    protected static string $resource = TeamResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->string()
                    ->maxLength(255),
                Textarea::make('description')
                    ->required()
                    ->string(),
            ]);
    }
}
