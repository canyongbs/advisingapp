<?php

namespace Assist\Prospect\Filament\Resources\ProspectStatusResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Assist\Prospect\Enums\ProspectStatusColorOptions;
use Assist\Prospect\Filament\Resources\ProspectStatusResource;

class EditProspectStatus extends EditRecord
{
    protected static string $resource = ProspectStatusResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->translateLabel()
                    ->required()
                    ->string(),
                Select::make('color')
                    ->label('Color')
                    ->translateLabel()
                    ->searchable()
                    ->options(ProspectStatusColorOptions::class)
                    ->required()
                    ->enum(ProspectStatusColorOptions::class),
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
