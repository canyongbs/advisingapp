<?php

namespace AdvisingApp\Prospect\Filament\Resources\ProspectTagResource\Pages;

use Filament\Forms\Form;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use AdvisingApp\Prospect\Filament\Resources\ProspectTagResource;

class EditProspectTag extends EditRecord
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

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
