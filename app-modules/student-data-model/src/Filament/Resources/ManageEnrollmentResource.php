<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources;

use AdvisingApp\StudentDataModel\Filament\Resources\ManageEnrollmentResource\Pages\ListManageEnrollments;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use App\Features\ManageStudentConfigurationFeature;
use App\Filament\Clusters\ConstituentManagement;
use App\Settings\ManageStudentConfigurationSettings;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;

class ManageEnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = ConstituentManagement::class;

    protected static ?string $navigationGroup = 'Students';

    protected static ?string $label = 'Enrollments';

    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return ManageStudentConfigurationFeature::active()
            && $user->can('student_record_manager.view-any')
            && app(ManageStudentConfigurationSettings::class)->is_enabled;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('sisid')
                    ->label('Student ID')
                    ->string()
                    ->maxLength(255)
                    ->required(),
                TextInput::make('division')
                    ->string()
                    ->maxLength(255)
                    ->label('Division'),
                TextInput::make('class_nbr')
                    ->label('Class NBR')
                    ->string()
                    ->maxLength(255),
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
                    ->string()
                    ->maxLength(255),
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
                    ->string()
                    ->maxLength(255),
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
            'index' => ListManageEnrollments::route('/'),
        ];
    }
}
