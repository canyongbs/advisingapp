<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Actions;

use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Gate;

class EditStudentAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->modalHeading('Edit')
            ->modalSubmitActionLabel('Save changes')
            ->mountUsing(function (Form $form, $record) {
                $form->fill($record->attributesToArray());
            })
            ->form([
                Section::make('Personal Information')
                    ->schema([
                        TextInput::make('otherid')
                            ->label('Other ID')
                            ->string()
                            ->maxLength(255),
                        TextInput::make(Student::displayFirstNameKey())
                            ->label('First Name')
                            ->string()
                            ->maxLength(255),
                        TextInput::make(Student::displayLastNameKey())
                            ->label('Last Name')
                            ->string()
                            ->maxLength(255),
                        TextInput::make(Student::displayNameKey())
                            ->label('Full Name')
                            ->string()
                            ->maxLength(255),
                        TextInput::make('preferred')
                            ->label('Preferred Name')
                            ->string()
                            ->maxLength(255),
                        DatePicker::make('birthdate')
                            ->label('Birthdate')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->format('Y-m-d')
                            ->displayFormat('Y-m-d')
                            ->maxDate(now()),
                        TextInput::make('hsgrad')
                            ->label('High School Graduation Date')
                            ->nullable()
                            ->numeric(),
                    ])
                    ->columns(3),
                Section::make('Contact Information')
                    ->schema([
                        TextInput::make('email')
                            ->label('Primary Email')
                            ->email()
                            ->required(),
                        TextInput::make('email_2')
                            ->label('Other Email')
                            ->email(),
                        TextInput::make('mobile')
                            ->label('Mobile')
                            ->string()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Other Phone')
                            ->string()
                            ->maxLength(255),
                        TextInput::make('address')
                            ->label('Address')
                            ->string()
                            ->maxLength(255),
                        TextInput::make('address2')
                            ->label('Apartment/Unit Number')
                            ->string()
                            ->maxLength(255),
                        TextInput::make('address3')
                            ->label('Additional Address')
                            ->string()
                            ->maxLength(255),
                        TextInput::make('city')
                            ->label('City')
                            ->string()
                            ->maxLength(255),
                        TextInput::make('state')
                            ->label('State')
                            ->string()
                            ->maxLength(255),
                        TextInput::make('postal')
                            ->label('Postal')
                            ->string()
                            ->maxLength(255),
                    ])
                    ->columns(3),
                Section::make('Engagement Restrictions')
                    ->schema([
                        Toggle::make('sms_opt_out')
                            ->label('SMS Opt Out'),
                        Toggle::make('email_bounce')
                            ->label('Email Bounce'),
                        Toggle::make('dual')
                            ->label('Dual'),
                        Toggle::make('ferpa')
                            ->label('FERPA'),
                        Toggle::make('firstgen')
                            ->label('Firstgen'),
                        Toggle::make('sap')
                            ->label('SAP'),
                        TextInput::make('holds')
                            ->label('Holds'),
                        DatePicker::make('dfw')
                            ->label('DFW')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->format('Y-m-d')
                            ->displayFormat('Y-m-d'),
                        TextInput::make('ethnicity')
                            ->label('Ethnicity')
                            ->string(),
                        DateTimePicker::make('lastlmslogin')
                            ->label('Last LMS login')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->format('Y-m-d H:i:s')
                            ->displayFormat('Y-m-d H:i:s'),
                        TextInput::make('f_e_term')
                            ->label('First Enrollement Term')
                            ->string()
                            ->maxLength(255),
                        TextInput::make('mr_e_term')
                            ->label('Most Recent Enrollement Term')
                            ->string()
                            ->maxLength(255),
                    ])
                    ->columns(3),
            ])
            ->action(function (array $data, Student $student) {
                $student->update($data);
                Notification::make()
                    ->title('Saved.')
                    ->success()
                    ->send();
            })
            ->authorize(function ($record) {
                return auth()->user()->can('update', $record);
            });
    }
}
