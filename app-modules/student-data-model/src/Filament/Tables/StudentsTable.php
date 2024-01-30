<?php

namespace AdvisingApp\StudentDataModel\Filament\Tables;

use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;

class StudentsTable
{
    public function __invoke(Table $table): Table
    {
        return $table
            ->query(fn () => Student::query())
            ->columns([
                TextColumn::make(Student::displayNameKey())
                    ->label('Name')
                    ->sortable(),
                TextColumn::make('email'),
                TextColumn::make('mobile'),
                TextColumn::make('phone'),
                TextColumn::make('sisid'),
                TextColumn::make('otherid'),
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraints([
                        TextConstraint::make('full_name')
                            ->label('Full Name')
                            ->icon('heroicon-m-user'),
                        TextConstraint::make('first')
                            ->label('First Name')
                            ->icon('heroicon-m-user'),
                        TextConstraint::make('last')
                            ->label('Last Name')
                            ->icon('heroicon-m-user'),
                        TextConstraint::make('preferred')
                            ->label('Preferred Name')
                            ->icon('heroicon-m-user'),
                        TextConstraint::make('sisid')
                            ->label('Student ID')
                            ->icon('heroicon-m-finger-print'),
                        TextConstraint::make('otherid')
                            ->label('Other ID')
                            ->icon('heroicon-m-finger-print'),
                        TextConstraint::make('email')
                            ->label('Email Address')
                            ->icon('heroicon-m-envelope'),
                        TextConstraint::make('mobile')
                            ->icon('heroicon-m-phone'),
                        TextConstraint::make('address')
                            ->icon('heroicon-m-map-pin'),
                        TextConstraint::make('holds')
                            ->icon('heroicon-m-exclamation-triangle'),
                        BooleanConstraint::make('sap')
                            ->label('SAP')
                            ->icon('heroicon-m-academic-cap'),
                        BooleanConstraint::make('dual'),
                        BooleanConstraint::make('ferpa')
                            ->label('FERPA')
                            ->icon('heroicon-m-lock-open'),
                        Constraint::make('subscribed')
                            ->icon('heroicon-m-bell')
                            ->operators([
                                Operator::make('subscribed')
                                    ->label(fn (bool $isInverse): string => $isInverse ? 'Not subscribed' : 'Subscribed')
                                    ->summary(fn (bool $isInverse): string => $isInverse ? 'You are not subscribed' : 'You are subscribed')
                                    ->baseQuery(fn (Builder $query, bool $isInverse) => $query->{$isInverse ? 'whereDoesntHave' : 'whereHas'}(
                                        'subscriptions.user',
                                        fn (Builder $query) => $query->whereKey(auth()->user()),
                                    )),
                            ]),
                        RelationshipConstraint::make('programs')
                            ->multiple()
                            ->attributeLabel(fn (array $settings): string => Str::plural('program', $settings['count']))
                            ->icon('heroicon-m-academic-cap'),
                        TextConstraint::make('programSisid')
                            ->label('Program SISID')
                            ->relationship('programs', 'sisid'),
                        TextConstraint::make('programOtherid')
                            ->label('Program STUID')
                            ->relationship('programs', 'otherid'),
                        TextConstraint::make('programDivision')
                            ->label('Program College')
                            ->relationship('programs', 'division'),
                        TextConstraint::make('programDescr')
                            ->label('Program Description')
                            ->relationship('programs', 'descr'),
                        TextConstraint::make('programFoi')
                            ->label('Program Field of Interest')
                            ->relationship('programs', 'foi'),
                        NumberConstraint::make('programCumGpa')
                            ->label('Program Cumulative GPA')
                            ->relationship('programs', 'cum_gpa'),
                        RelationshipConstraint::make('enrollments')
                            ->multiple()
                            ->attributeLabel(fn (array $settings): string => Str::plural('enrollment', $settings['count']))
                            ->icon('heroicon-m-folder-open'),
                        TextConstraint::make('enrollmentSisid')
                            ->label('Enrollment SISID')
                            ->relationship('enrollments', 'sisid'),
                        TextConstraint::make('enrollmentDivision')
                            ->label('Enrollment College')
                            ->relationship('enrollments', 'division'),
                        TextConstraint::make('enrollmentClassNbr')
                            ->label('Enrollment Course')
                            ->relationship('enrollments', 'class_nbr'),
                        TextConstraint::make('enrollmentCrseGradeOff')
                            ->label('Enrollment Grade')
                            ->relationship('enrollments', 'crse_grade_off'),
                        NumberConstraint::make('enrollmentUntTaken')
                            ->label('Enrollment Attempted')
                            ->relationship('enrollments', 'unt_taken'),
                        NumberConstraint::make('enrollmentUntEarned')
                            ->label('Enrollment Earned')
                            ->relationship('enrollments', 'unt_earned'),
                        RelationshipConstraint::make('performances')
                            ->multiple()
                            ->attributeLabel(fn (array $settings): string => Str::plural('performance', $settings['count']))
                            ->icon('heroicon-m-presentation-chart-line'),
                        TextConstraint::make('performanceSisid')
                            ->label('Performance SISID')
                            ->relationship('performances', 'sisid'),
                        TextConstraint::make('performanceAcadCareer')
                            ->label('Performance Academic Career')
                            ->relationship('performances', 'acad_career'),
                        TextConstraint::make('performanceDivision')
                            ->label('Performance College')
                            ->relationship('performances', 'division'),
                        BooleanConstraint::make('performanceFirstGen')
                            ->label('Performance First Gen')
                            ->relationship('performances', 'first_gen'),
                        NumberConstraint::make('performanceCumAtt')
                            ->label('Performance Cumulative Attempted')
                            ->relationship('performances', 'cum_att'),
                        NumberConstraint::make('performanceCumErn')
                            ->label('Performance Cumulative Earned')
                            ->relationship('performances', 'cum_ern'),
                        NumberConstraint::make('performancePctErn')
                            ->label('Performance Percent Earned')
                            ->relationship('performances', 'pct_ern'),
                        NumberConstraint::make('performanceCumGpa')
                            ->label('Performance Cumulative GPA')
                            ->relationship('performances', 'cum_gpa'),
                    ])
                    ->constraintPickerColumns([
                        'md' => 2,
                        'lg' => 3,
                        'xl' => 4,
                    ])
                    ->constraintPickerWidth('7xl'),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                ViewAction::make()
                    ->authorize('view')
                    ->url(fn (Student $record) => StudentResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
