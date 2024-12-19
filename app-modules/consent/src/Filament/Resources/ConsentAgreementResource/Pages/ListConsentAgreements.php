<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Consent\Filament\Resources\ConsentAgreementResource\Pages;

use AdvisingApp\Consent\Enums\ConsentAgreementType;
use AdvisingApp\Consent\Filament\Resources\ConsentAgreementResource;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ListConsentAgreements extends ListRecords
{
    protected static string $resource = ConsentAgreementResource::class;

    protected static ?string $title = 'User Agreement';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('type')
                    ->formatStateUsing(fn ($state) => ConsentAgreementType::from($state)->getLabel())
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
                TextColumn::make('type')
                    ->formatStateUsing(fn (ConsentAgreementType $state) => $state->getLabel()),
                TextColumn::make('title'),
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

    protected function getHeaderActions(): array
    {
        return [];
    }
}
