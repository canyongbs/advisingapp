<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources;

use AdvisingApp\StudentDataModel\Filament\Resources\ManageProgramResource\Pages\CreateManageProgram;
use AdvisingApp\StudentDataModel\Filament\Resources\ManageProgramResource\Pages\ListManagePrograms;
use AdvisingApp\StudentDataModel\Models\Program;
use App\Features\ManageStudentConfigurationFeature;
use App\Filament\Clusters\ConstituentManagement;
use App\Settings\ManageStudentConfigurationSettings;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;

class ManageProgramResource extends Resource
{
    protected static ?string $model = Program::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?string $cluster = ConstituentManagement::class;

    protected static ?string $navigationGroup = 'Students';

    protected static ?string $label = 'Programs';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return ManageStudentConfigurationFeature::active() && $user->can('student_record_manager.configuration') && app(ManageStudentConfigurationSettings::class)->is_enabled;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->disabled(false)
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
                    ->required()
                    ->label('Cum GPA')
                    ->numeric(),
                TextInput::make('semester')
                    ->required()
                    ->label('Semester')
                    ->rules(['digits_between:1,4'])
                    ->numeric(),
                TextInput::make('descr')
                    ->required()
                    ->label('DESCR'),
                TextInput::make('foi')
                    ->required()
                    ->label('Field of interest'),
                DateTimePicker::make('change_dt')
                    ->required()
                    ->label('Change date')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->format('Y-m-d H:i:s')
                    ->displayFormat('Y-m-d H:i:s'),
                DateTimePicker::make('declare_dt')
                    ->required()
                    ->label('Declare date')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->format('Y-m-d H:i:s')
                    ->displayFormat('Y-m-d H:i:s'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListManagePrograms::route('/'),
            'create' => CreateManageProgram::route('/create'),
        ];
    }
}
