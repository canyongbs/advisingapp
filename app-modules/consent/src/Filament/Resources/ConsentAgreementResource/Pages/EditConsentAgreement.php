<?php

namespace Assist\Consent\Filament\Resources\ConsentAgreementResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Assist\Consent\Filament\Resources\ConsentAgreementResource;

class EditConsentAgreement extends EditRecord
{
    protected static string $resource = ConsentAgreementResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('type')
                    ->disabled()
                    ->helperText('This field is not editable.'),
                TextInput::make('title')
                    ->required(),
                Fieldset::make('Content')
                    ->schema([
                        Textarea::make('description')
                            ->required()
                            ->columnSpan('full'),
                        Textarea::make('body')
                            ->required()
                            ->rows(5)
                            ->columnSpan('full'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
