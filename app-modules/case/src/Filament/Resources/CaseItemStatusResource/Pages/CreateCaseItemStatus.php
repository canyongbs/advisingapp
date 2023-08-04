<?php

namespace Assist\Case\Filament\Resources\CaseItemStatusResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Assist\Case\Enums\ColumnColorOptions;
use Filament\Resources\Pages\CreateRecord;
use Assist\Case\Filament\Resources\CaseItemStatusResource;

class CreateCaseItemStatus extends CreateRecord
{
    protected static string $resource = CaseItemStatusResource::class;

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
                    ->options(ColumnColorOptions::class)
                    ->required()
                    ->enum(ColumnColorOptions::class),
            ]);
    }
}
