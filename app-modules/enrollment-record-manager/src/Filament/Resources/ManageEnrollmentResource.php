<?php

namespace AdvisingApp\EnrollmentRecordManager\Filament\Resources;

use AdvisingApp\EnrollmentRecordManager\Filament\Resources\ManageEnrollmentResource\Pages;
use AdvisingApp\EnrollmentRecordManager\Models\ManageableEnrollment;
use App\Filament\Clusters\ConstituentManagement;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;

class ManageEnrollmentResource extends Resource
{
    protected static ?string $model = ManageableEnrollment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = ConstituentManagement::class;

    protected static ?string $navigationGroup = 'Students';

    public static function form(Form $form): Form
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListManageEnrollments::route('/'),
            'create' => Pages\CreateManageEnrollment::route('/create'),
        ];
    }
}
