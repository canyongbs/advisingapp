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
        return parent::form($form)
            ->schema([
                TextInput::make('type')
                    ->readonly()
                    ->helperText('This field is not editable.'),
                TextInput::make('title')
                    ->required(),
                Fieldset::make('Content')
                    ->schema([
                        Textarea::make('description')
                            ->required()
                            ->columnSpan([
                                'sm' => 2,
                                'xl' => 3,
                                '2xl' => 4,
                            ]),
                        Textarea::make('body')
                            ->required()
                            ->rows(5)
                            ->columnSpan([
                                'sm' => 2,
                                'xl' => 3,
                                '2xl' => 4,
                            ]),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
