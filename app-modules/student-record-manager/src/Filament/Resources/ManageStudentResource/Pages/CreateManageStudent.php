<?php

namespace AdvisingApp\StudentRecordManager\Filament\Resources\ManageStudentResource\Pages;

use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentRecordManager\Filament\Resources\ManageStudentResource;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateManageStudent extends CreateRecord
{
    protected static string $resource = ManageStudentResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Personal Information')
                    ->schema([
                        TextInput::make('sisid')
                            ->label('Student ID')
                            ->required()
                            ->numeric(),
                        TextInput::make('otherid')
                            ->label('Other ID')
                            ->numeric(),
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
                            ->numeric()
                            ->minValue(1920)
                            ->maxValue(now()->addYears(25)->year),
                    ])
                    ->columns(3),
                Section::make('Contact Information')
                    ->schema([
                        TextInput::make('email')
                            ->label('Primary Email')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('email_2')
                            ->label('Other Email')
                            ->email()
                            ->maxLength(255),
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
                        Radio::make('sms_opt_out')
                            ->label('SMS Opt Out')
                            ->boolean(),
                        Radio::make('email_bounce')
                            ->label('Email Bounce')
                            ->boolean(),
                        Radio::make('dual')
                            ->label('Dual')
                            ->boolean(),
                        Radio::make('ferpa')
                            ->label('FERPA')
                            ->boolean(),
                        DatePicker::make('dfw')
                            ->label('DFW')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->format('Y-m-d')
                            ->displayFormat('Y-m-d'),
                        Radio::make('sap')
                            ->label('SAP')
                            ->boolean(),
                        TextInput::make('holds')
                            ->label('Holds')
                            ->regex('/^[A-Z]{5}$/'),
                        Radio::make('firstgen')
                            ->label('Firstgen')
                            ->boolean(),
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
                            ->numeric(),
                        TextInput::make('mr_e_term')
                            ->label('Most Recent Enrollement Term')
                            ->numeric(),

                    ])
                    ->columns(3),
            ]);
    }
}
