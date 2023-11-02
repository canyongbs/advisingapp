<?php

namespace Assist\Consent\Filament\Resources\ConsentAgreementResource\Pages;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
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

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can(['consent_agreement.view-any', 'consent_agreement.*.view', 'consent_agreement.*.update']);
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
                EditAction::make('edit')
                    ->modalSubmitAction(false)
                    ->extraModalFooterActions(
                        [
                            Action::make('save_changes')
                                ->requiresConfirmation()
                                ->modalIconColor('warning')
                                ->modalHeading(fn ($record) => "Save Changes to {$record->title}?")
                                ->modalDescription(fn ($record) => $record->type->getModalDescription())
                                ->modalSubmitActionLabel('I understand, save changes')
                                ->modalWidth('xl')
                                ->action(function () {
                                    $this->unmountTableAction();

                                    $this->callMountedTableAction();
                                }),
                        ]
                    ),
            ])
            ->bulkActions([]);
    }

    public function getSubNavigation(): array
    {
        return (new AssistantConfiguration())->getSubNavigation();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
