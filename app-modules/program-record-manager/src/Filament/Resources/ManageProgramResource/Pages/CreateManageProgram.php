<?php

namespace AdvisingApp\ProgramRecordManager\Filament\Resources\ManageProgramResource\Pages;

use AdvisingApp\ProgramRecordManager\Filament\Resources\ManageProgramResource;
use Filament\Actions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateManageProgram extends CreateRecord
{
    protected static string $resource = ManageProgramResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('sisid')
                    ->label('Student ID')
                    ->required()
                    ->numeric(),
                TextInput::make('otherid')
                    ->label('Other ID')
                    ->required()
                    ->numeric(),
                TextInput::make('acad_career')
                    ->string()
                    ->maxLength(255)
                    ->required()
                    ->label('ACAD career'),
                TextInput::make('division')
                    ->string()
                    ->maxLength(255)
                    ->required()
                    ->label('Division'),
                TextInput::make('acad_plan')
                    ->required()
                    ->label('ACAD plan'),
                TextInput::make('prog_status')
                    ->required()
                    ->label('PROG status')
                    ->default('AC'),
                TextInput::make('cum_gpa')
                    ->label('Cum GPA')
                    ->numeric(),
                TextInput::make('semester')
                    ->label('Semester')
                    ->rules(['digits_between:1,4'])
                    ->numeric(),
                TextInput::make('descr')
                    ->label('DESCR')
                    ->numeric(),
                TextInput::make('foi')
                    ->label('Field of interest'),
                DateTimePicker::make('change_dt')
                    ->label('Change date')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->format('Y-m-d H:i:s')
                    ->required()
                    ->displayFormat('Y-m-d H:i:s'),
                DateTimePicker::make('declare_dt')
                    ->label('Declare date')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->format('Y-m-d H:i:s')
                    ->required()
                    ->displayFormat('Y-m-d H:i:s'),
            ]);
    }
}
