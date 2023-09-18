<?php

namespace Assist\Prospect\Filament\Resources\ProspectResource\Pages;

use App\Models\User;
use Filament\Actions;
use Filament\Forms\Form;
use Assist\Prospect\Models\Prospect;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\DatePicker;
use Assist\Prospect\Models\ProspectSource;
use Assist\Prospect\Models\ProspectStatus;
use Assist\Prospect\Filament\Resources\ProspectResource;

class EditProspect extends EditRecord
{
    protected static string $resource = ProspectResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                Select::make('status_id')
                    ->label('Status')
                    ->translateLabel()
                    ->relationship('status', 'name')
                    ->exists(
                        table: (new ProspectStatus())->getTable(),
                        column: (new ProspectStatus())->getKeyName()
                    ),
                Select::make('source_id')
                    ->label('Source')
                    ->translateLabel()
                    ->relationship('source', 'name')
                    ->exists(
                        table: (new ProspectSource())->getTable(),
                        column: (new ProspectSource())->getKeyName()
                    ),
                TextInput::make('first_name')
                    ->label('First Name')
                    ->translateLabel()
                    ->required()
                    ->string(),
                TextInput::make('last_name')
                    ->label('Last Name')
                    ->translateLabel()
                    ->required()
                    ->string(),
                TextInput::make(Prospect::displayNameKey())
                    ->label('Full Name')
                    ->translateLabel()
                    ->required()
                    ->string(),
                TextInput::make('preferred')
                    ->label('Preferred Name')
                    ->translateLabel()
                    ->string(),
                Textarea::make('description')
                    ->label('Description')
                    ->translateLabel()
                    ->string(),
                TextInput::make('email')
                    ->label('Primary Email')
                    ->translateLabel()
                    ->email(),
                TextInput::make('email_2')
                    ->label('Other Email')
                    ->translateLabel()
                    ->email(),
                TextInput::make('mobile')
                    ->label('Mobile')
                    ->translateLabel()
                    ->string(),
                Radio::make('sms_opt_out')
                    ->label('SMS Opt Out')
                    ->translateLabel()
                    ->boolean(),
                Radio::make('email_bounce')
                    ->label('Email Bounce')
                    ->translateLabel()
                    ->boolean(),
                TextInput::make('phone')
                    ->label('Other Phone')
                    ->translateLabel()
                    ->string(),
                TextInput::make('address')
                    ->label('Address')
                    ->translateLabel()
                    ->string(),
                TextInput::make('address_2')
                    ->label('Address 2')
                    ->translateLabel()
                    ->string(),
                // TODO: Display this based on system configurable data format
                DatePicker::make('birthdate')
                    ->label('Birthdate')
                    ->translateLabel()
                    ->native(false)
                    ->closeOnDateSelection()
                    ->format('Y-m-d')
                    ->displayFormat('Y-m-d')
                    ->maxDate(now()),
                TextInput::make('hsgrad')
                    ->label('High School Graduation Date')
                    ->translateLabel()
                    ->nullable()
                    ->numeric()
                    ->minValue(1920)
                    ->maxValue(now()->addYears(25)->year),
                Select::make('assigned_to_id')
                    ->label('Assigned To')
                    ->translateLabel()
                    ->relationship('assignedTo', 'name')
                    ->searchable()
                    ->nullable()
                    ->exists(
                        table: (new User())->getTable(),
                        column: (new User())->getKeyName()
                    ),
                Select::make('created_by_id')
                    ->label('Created By')
                    ->translateLabel()
                    ->relationship('createdBy', 'name')
                    ->searchable()
                    ->nullable()
                    ->exists(
                        table: (new User())->getTable(),
                        column: (new User())->getKeyName()
                    ),
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
