<?php

namespace AdvisingApp\ProgramRecordManager\Filament\Resources;

use AdvisingApp\ProgramRecordManager\Filament\Resources\ManageProgramResource\Pages;
use AdvisingApp\ProgramRecordManager\Filament\Resources\ManageProgramResource\RelationManagers;
use AdvisingApp\ProgramRecordManager\Models\ManageableProgram;
use App\Filament\Clusters\ConstituentManagement;
use App\Models\ManageProgram;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageProgramResource extends Resource
{
    protected static ?string $model = ManageableProgram::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = ConstituentManagement::class;

    protected static ?string $navigationGroup = 'Students';

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
                Select::make('acad_career')
                    ->string()
                    ->maxLength(255)
                    ->required()
                    ->label('ACAD career'),
                Select::make('division')
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
                    ->displayFormat('Y-m-d H:i:s'),
                DateTimePicker::make('declare_dt')
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
            'index' => Pages\ListManagePrograms::route('/'),
            'create' => Pages\CreateManageProgram::route('/create'),
        ];
    }
}
