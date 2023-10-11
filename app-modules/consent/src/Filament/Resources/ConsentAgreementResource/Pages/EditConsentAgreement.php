<?php

namespace Assist\Consent\Filament\Resources\ConsentAgreementResource\Pages;

use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Assist\Consent\Filament\Resources\ConsentAgreementResource;

class EditConsentAgreement extends EditRecord
{
    protected static string $resource = ConsentAgreementResource::class;

    public function getTitle(): string | Htmlable
    {
        return "Edit {$this->record->title}";
    }

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

    protected function getFormActions(): array
    {
        return [
            Action::make('Save Changes')
                ->requiresConfirmation()
                ->modalIconColor('warning')
                ->modalHeading("Save Changes to {$this->record->title}?")
                ->modalDescription($this->record->type->getModalDescription())
                ->modalSubmitActionLabel('I understand, save changes')
                ->modalWidth('xl')
                ->action(function () {
                    $this->save();

                    if ($this->record->users->count() > 0) {
                        $this->record->users()->detach();
                    }
                }),
            $this->getCancelFormAction(),
        ];
    }
}
