<?php

namespace Assist\CaseloadManagement\Filament\Resources;

use Exception;
use Filament\Resources\Resource;
use Assist\Prospect\Models\Prospect;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use App\Filament\Tables\Filters\QueryBuilder;
use Assist\CaseloadManagement\Models\Caseload;
use Assist\CaseloadManagement\Enums\CaseloadModel;
use Assist\CaseloadManagement\Filament\Resources\CaseloadResource\Pages\EditCaseload;
use Assist\CaseloadManagement\Filament\Resources\CaseloadResource\Pages\ListCaseloads;
use Assist\CaseloadManagement\Filament\Resources\CaseloadResource\Pages\CreateCaseload;

class CaseloadResource extends Resource
{
    protected static ?string $model = Caseload::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?string $navigationGroup = 'Mass Engagement';

    protected static ?int $navigationSort = 1;

    public static function filters(CaseloadModel $subject): array
    {
        return match ($subject) {
            CaseloadModel::Student => static::studentFilters(),
            CaseloadModel::Prospect => static::prospectFilters(),
            default => throw new Exception("{$subject->name} filters not implemented"),
        };
    }

    public static function columns(CaseloadModel $subject): array
    {
        return match ($subject) {
            CaseloadModel::Student => static::studentColumns(),
            CaseloadModel::Prospect => static::prospectColumns(),
            default => throw new Exception("{$subject->name} columns not implemented"),
        };
    }

    public static function actions(CaseloadModel $subject): array
    {
        return match ($subject) {
            CaseloadModel::Student => static::studentActions(),
            CaseloadModel::Prospect => static::prospectActions(),
            default => throw new Exception("{$subject->name} actions not implemented"),
        };
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCaseloads::route('/'),
            'create' => CreateCaseload::route('/create'),
            'edit' => EditCaseload::route('/{record}/edit'),
        ];
    }

    private static function studentFilters(): array
    {
        return [
            QueryBuilder::make()
                ->constraints([
                    QueryBuilder\Constraints\TextConstraint::make('full_name')
                        ->label('Full Name')
                        ->icon('heroicon-m-user'),
                    QueryBuilder\Constraints\TextConstraint::make('first')
                        ->label('First Name')
                        ->icon('heroicon-m-user'),
                    QueryBuilder\Constraints\TextConstraint::make('last')
                        ->label('Last Name')
                        ->icon('heroicon-m-user'),
                    QueryBuilder\Constraints\TextConstraint::make('preferred')
                        ->label('Preferred Name')
                        ->icon('heroicon-m-user'),
                    QueryBuilder\Constraints\TextConstraint::make('sisid')
                        ->label('Student ID')
                        ->icon('heroicon-m-finger-print'),
                    QueryBuilder\Constraints\TextConstraint::make('otherid')
                        ->label('Other ID')
                        ->icon('heroicon-m-finger-print'),
                    QueryBuilder\Constraints\TextConstraint::make('email')
                        ->label('Email Address')
                        ->icon('heroicon-m-envelope'),
                    QueryBuilder\Constraints\TextConstraint::make('mobile')
                        ->icon('heroicon-m-phone'),
                    QueryBuilder\Constraints\TextConstraint::make('address')
                        ->icon('heroicon-m-map-pin'),
                    QueryBuilder\Constraints\TextConstraint::make('holds')
                        ->icon('heroicon-m-exclamation-triangle'),
                    QueryBuilder\Constraints\BooleanConstraint::make('sap')
                        ->label('SAP')
                        ->icon('heroicon-m-academic-cap'),
                    QueryBuilder\Constraints\BooleanConstraint::make('dual'),
                    QueryBuilder\Constraints\BooleanConstraint::make('ferpa')
                        ->label('FERPA')
                        ->icon('heroicon-m-lock-open'),
                    QueryBuilder\Constraints\Constraint::make('subscribed')
                        ->icon('heroicon-m-bell')
                        ->operators([
                            QueryBuilder\Constraints\Operators\Operator::make('subscribed')
                                ->label(fn (bool $isInverse): string => $isInverse ? 'Not subscribed' : 'Subscribed')
                                ->summary(fn (bool $isInverse): string => $isInverse ? 'You are not subscribed' : 'You are subscribed')
                                ->baseQuery(fn (Builder $query, bool $isInverse) => $query->{$isInverse ? 'whereDoesntHave' : 'whereHas'}(
                                    'subscriptions.user',
                                    fn (Builder $query) => $query->whereKey(auth()->user()),
                                )),
                        ]),
                    QueryBuilder\Constraints\RelationshipConstraint::make('programs')
                        ->multiple()
                        ->attributeLabel(fn (array $settings): string => Str::plural('program', $settings['count']))
                        ->icon('heroicon-m-academic-cap'),
                    QueryBuilder\Constraints\TextConstraint::make('programSisid')
                        ->label('Program SISID')
                        ->relationship('programs', 'sisid'),
                    QueryBuilder\Constraints\TextConstraint::make('programOtherid')
                        ->label('Program STUID')
                        ->relationship('programs', 'otherid'),
                    QueryBuilder\Constraints\TextConstraint::make('programDivision')
                        ->label('Program College')
                        ->relationship('programs', 'division'),
                    QueryBuilder\Constraints\TextConstraint::make('programDescr')
                        ->label('Program Description')
                        ->relationship('programs', 'descr'),
                    QueryBuilder\Constraints\TextConstraint::make('programFoi')
                        ->label('Program Field of Interest')
                        ->relationship('programs', 'foi'),
                    QueryBuilder\Constraints\NumberConstraint::make('programCumGpa')
                        ->label('Program Cumulative GPA')
                        ->relationship('programs', 'cum_gpa'),
                    QueryBuilder\Constraints\RelationshipConstraint::make('enrollments')
                        ->multiple()
                        ->attributeLabel(fn (array $settings): string => Str::plural('enrollment', $settings['count']))
                        ->icon('heroicon-m-folder-open'),
                    QueryBuilder\Constraints\TextConstraint::make('enrollmentSisid')
                        ->label('Enrollment SISID')
                        ->relationship('enrollments', 'sisid'),
                    QueryBuilder\Constraints\TextConstraint::make('enrollmentDivision')
                        ->label('Enrollment College')
                        ->relationship('enrollments', 'division'),
                    QueryBuilder\Constraints\TextConstraint::make('enrollmentClassNbr')
                        ->label('Enrollment Course')
                        ->relationship('enrollments', 'class_nbr'),
                    QueryBuilder\Constraints\TextConstraint::make('enrollmentCrseGradeOff')
                        ->label('Enrollment Grade')
                        ->relationship('enrollments', 'crse_grade_off'),
                    QueryBuilder\Constraints\NumberConstraint::make('enrollmentUntTaken')
                        ->label('Enrollment Attempted')
                        ->relationship('enrollments', 'unt_taken'),
                    QueryBuilder\Constraints\NumberConstraint::make('enrollmentUntEarned')
                        ->label('Enrollment Earned')
                        ->relationship('enrollments', 'unt_earned'),
                    QueryBuilder\Constraints\RelationshipConstraint::make('performances')
                        ->multiple()
                        ->attributeLabel(fn (array $settings): string => Str::plural('performance', $settings['count']))
                        ->icon('heroicon-m-presentation-chart-line'),
                    QueryBuilder\Constraints\TextConstraint::make('performanceSisid')
                        ->label('Performance SISID')
                        ->relationship('performances', 'sisid'),
                    QueryBuilder\Constraints\TextConstraint::make('performanceAcadCareer')
                        ->label('Performance Academic Career')
                        ->relationship('performances', 'acad_career'),
                    QueryBuilder\Constraints\TextConstraint::make('performanceDivision')
                        ->label('Performance College')
                        ->relationship('performances', 'division'),
                    QueryBuilder\Constraints\BooleanConstraint::make('performanceFirstGen')
                        ->label('Performance First Gen')
                        ->relationship('performances', 'first_gen'),
                    QueryBuilder\Constraints\NumberConstraint::make('performanceCumAtt')
                        ->label('Performance Cumulative Attempted')
                        ->relationship('performances', 'cum_att'),
                    QueryBuilder\Constraints\NumberConstraint::make('performanceCumErn')
                        ->label('Performance Cumulative Earned')
                        ->relationship('performances', 'cum_ern'),
                    QueryBuilder\Constraints\NumberConstraint::make('performancePctErn')
                        ->label('Performance Percent Earned')
                        ->relationship('performances', 'pct_ern'),
                    QueryBuilder\Constraints\NumberConstraint::make('performanceCumGpa')
                        ->label('Performance Cumulative GPA')
                        ->relationship('performances', 'cum_gpa'),
                ])
                ->constraintPickerColumns([
                    'md' => 2,
                    'lg' => 3,
                    'xl' => 4,
                ])
                ->constraintPickerWidth('7xl'),
        ];
    }

    private static function prospectFilters(): array
    {
        return [
            SelectFilter::make('status')
                ->relationship('status', 'name')
                ->multiple()
                ->preload(),
            SelectFilter::make('source')
                ->relationship('source', 'name')
                ->multiple()
                ->preload(),
        ];
    }

    private static function studentColumns(): array
    {
        return [
            TextColumn::make(Student::displayNameKey())
                ->label('Name')
                ->sortable(),
            TextColumn::make('email'),
            TextColumn::make('mobile'),
            TextColumn::make('phone'),
            TextColumn::make('sisid'),
            TextColumn::make('otherid'),
        ];
    }

    private static function prospectColumns(): array
    {
        return [
            TextColumn::make(Prospect::displayNameKey())
                ->label('Name')
                ->sortable(),
            TextColumn::make('email')
                ->label('Email')
                ->sortable(),
            TextColumn::make('mobile')
                ->label('Mobile')
                ->sortable(),
            TextColumn::make('status')
                ->badge()
                ->state(function (Prospect $record) {
                    return $record->status->name;
                })
                ->color(function (Prospect $record) {
                    return $record->status->color;
                })
                ->sortable(query: function (Builder $query, string $direction): Builder {
                    return $query
                        ->join('prospect_statuses', 'prospects.status_id', '=', 'prospect_statuses.id')
                        ->orderBy('prospect_statuses.name', $direction);
                }),
            TextColumn::make('source.name')
                ->label('Source')
                ->sortable(),
            TextColumn::make('created_at')
                ->label('Created')
                ->dateTime('g:ia - M j, Y ')
                ->sortable(),
        ];
    }

    private static function studentActions(): array
    {
        return [
            ViewAction::make(),
            EditAction::make(),
        ];
    }

    private static function prospectActions(): array
    {
        return [
            ViewAction::make(),
            EditAction::make(),
        ];
    }
}
