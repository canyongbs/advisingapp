<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestResource\Pages;

use Filament\Forms\Form;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\DateTimePicker;
use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestResource;

class EditChangeRequest extends EditRecord
{
    protected static string $resource = ChangeRequestResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Change Request Details')
                    ->description(function ($record) {
                        return $record->isApproved()
                            ? 'This change request has been approved and can no longer be edited.'
                            : null;
                    })
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->columnSpanFull()
                            ->disabled(fn ($record) => $record->isApproved()),
                        TextInput::make('description')
                            ->required()
                            ->columnSpanFull()
                            ->disabled(fn ($record) => $record->isApproved()),
                        Select::make('change_request_type_id')
                            ->relationship('type', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1)
                            ->disabled(fn ($record) => $record->isApproved()),
                        Select::make('change_request_status_id')
                            ->relationship('status', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1)
                            ->disabled(),
                        Textarea::make('reason')
                            ->label('Reason for change')
                            ->rows(5)
                            ->required()
                            ->columnSpanFull()
                            ->disabled(fn ($record) => $record->isApproved()),
                        Textarea::make('backout_strategy')
                            ->rows(5)
                            ->required()
                            ->columnSpanFull()
                            ->disabled(fn ($record) => $record->isApproved()),
                        DateTimePicker::make('start_time')
                            ->required()
                            ->columnSpan(1)
                            ->disabled(fn ($record) => $record->isApproved()),
                        DateTimePicker::make('end_time')
                            ->required()
                            ->columnSpan(1)
                            ->disabled(fn ($record) => $record->isApproved()),
                    ])
                    ->columns(2),
                Section::make('Risk Management')
                    ->schema([
                        TextInput::make('impact')
                            ->reactive()
                            ->helperText('Please enter a number between 1 and 5.')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5)
                            ->columnSpan(1)
                            ->disabled(fn ($record) => $record->isApproved()),
                        TextInput::make('likelihood')
                            ->reactive()
                            ->helperText('Please enter a number between 1 and 5.')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5)
                            ->columnSpan(1)
                            ->disabled(fn ($record) => $record->isApproved()),
                        ViewField::make('risk_score')
                            ->view('filament.forms.components.change-request.calculated-risk-score'),
                    ])
                    ->columns(3),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->disabled(fn ($record) => $record->isApproved()),
        ];
    }
}
