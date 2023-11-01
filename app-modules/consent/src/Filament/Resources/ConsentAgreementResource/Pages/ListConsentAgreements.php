<?php

namespace Assist\Consent\Filament\Resources\ConsentAgreementResource\Pages;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Actions\Action;
use App\Filament\Columns\IdColumn;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Assist\Consent\Models\UserConsentAgreement;
use Assist\Assistant\Filament\Pages\AssistantConfiguration;
use Assist\Consent\Filament\Resources\ConsentAgreementResource;

class ListConsentAgreements extends ListRecords
{
    protected static string $resource = ConsentAgreementResource::class;

    protected static ?string $navigationLabel = 'User Agreement';

    protected static ?string $title = 'User Agreement';

    public function getBreadcrumbs(): array
    {
        return [
            AssistantConfiguration::getUrl() => 'Artificial Intelligence',
            $this::getUrl() => 'User Agreement',
        ];
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

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('type'),
                TextColumn::make('title'),
            ])
            ->filters([
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public function getSubNavigation(): array
    {
        return (new AssistantConfiguration())->getSubNavigation();
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
                        UserConsentAgreement::where('consent_agreement_id', $this->record->id)
                            ->delete();
                    }
                }),
            $this->getCancelFormAction(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
