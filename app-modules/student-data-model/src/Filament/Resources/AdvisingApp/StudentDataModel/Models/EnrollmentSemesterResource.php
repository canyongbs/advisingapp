<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\AdvisingApp\StudentDataModel\Models;

use AdvisingApp\StudentDataModel\Filament\Resources\AdvisingApp\StudentDataModel\Models\EnrollmentSemesterResource\Pages\ManageEnrollmentSemesters;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\EnrollmentSemester;
use App\Features\EnrollmentSemesterFeature;
use App\Filament\Clusters\ConstituentManagement;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EnrollmentSemesterResource extends Resource
{
    protected static ?string $model = EnrollmentSemester::class;

    protected static ?string $cluster = ConstituentManagement::class;

    protected static ?string $navigationGroup = 'Students';

    protected static ?string $navigationLabel = 'Semester Order';

    protected static ?int $navigationSort = 70;

    public static function canAccess(): bool
    {
        return EnrollmentSemesterFeature::active() && parent::canAccess();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->datalist(fn (string $operation): array => ($operation === 'create') ? Enrollment::query()
                        ->whereNotIn('semester_name', EnrollmentSemester::query()->select('name'))
                        ->distinct()
                        ->orderBy('semester_name')
                        ->limit(250)
                        ->pluck('semester_name')
                        ->all() : []),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
            ])
            ->defaultSort('order')
            ->reorderable('order', condition: auth()->user()->can('settings.*.update'))
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEnrollmentSemesters::route('/'),
        ];
    }
}
