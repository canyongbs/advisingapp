<?php

namespace AdvisingApp\EnrollmentRecordManager\Filament\Resources\ManageEnrollmentResource\Pages;

use AdvisingApp\EnrollmentRecordManager\Filament\Resources\ManageEnrollmentResource;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;

class CreateManageEnrollment extends CreateRecord
{
    protected static string $resource = ManageEnrollmentResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('sisid')
                    ->label('Student ID')
                    ->required()
                    ->numeric(),
                TextInput::make('division')
                    ->string()
                    ->maxLength(255)
                    ->label('Division'),
                TextInput::make('class_nbr')
                    ->label('Class NBR')
                    ->numeric(),
                TextInput::make('crse_grade_off')
                    ->string()
                    ->maxLength(255)
                    ->label('CRSE grade off'),
                TextInput::make('unt_taken')
                    ->label('UNT taken')
                    ->numeric(),
                TextInput::make('unt_earned')
                    ->label('UNT earned')
                    ->numeric(),
                DateTimePicker::make('last_upd_dt_stmp')
                    ->label('Last UPD date STMP')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->format('Y-m-d H:i:s')
                    ->displayFormat('Y-m-d H:i:s'),
                TextInput::make('section')
                    ->label('Section')
                    ->numeric(),
                TextInput::make('name')
                    ->label('Name')
                    ->string()
                    ->maxLength(255),
                TextInput::make('department')
                    ->label('Department')
                    ->string()
                    ->maxLength(255),
                TextInput::make('faculty_name')
                    ->label('Faculty name')
                    ->string()
                    ->maxLength(255),
                TextInput::make('faculty_email')
                    ->label('Faculty email')
                    ->email(),
                TextInput::make('semester_code')
                    ->label('Semester code')
                    ->numeric(),
                TextInput::make('semester_name')
                    ->label('Semester name')
                    ->string()
                    ->maxLength(255),
                DateTimePicker::make('start_date')
                    ->label('Start date')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->format('Y-m-d H:i:s')
                    ->displayFormat('Y-m-d H:i:s'),
                DateTimePicker::make('end_date')
                    ->label('End date')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->format('Y-m-d H:i:s')
                    ->displayFormat('Y-m-d H:i:s'),
            ]);
    }
}
