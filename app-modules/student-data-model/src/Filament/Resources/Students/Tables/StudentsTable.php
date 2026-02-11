<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\StudentDataModel\Filament\Resources\Students\Tables;

use AdvisingApp\Alert\Filament\Filters\AlertStatusConstraint;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\StudentResource;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\ExistingValuesSelectConstraint;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\EqualsOperatorWithEnrollmentSemester;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\HasMaxOperatorWithEnrollmentSemester;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\HasMinOperatorWithEnrollmentSemester;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsEmptyOperatorWithEnrollmentSemester;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Str;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(fn () => Student::query())
            ->columns([
                TextColumn::make(Student::displayNameKey())
                    ->label('Name')
                    ->sortable(),
                TextColumn::make('primaryEmailAddress.address')
                    ->label('Email')
                    ->sortable(),
                TextColumn::make('primaryPhoneNumber.number')
                    ->label('Phone')
                    ->sortable(),
                TextColumn::make('sisid'),
                TextColumn::make('otherid'),
            ])
            ->filters([
                QueryBuilder::make()
                    // WARNING: Removing constraints from this list requires a data migration to clean up
                    // existing Groups that reference the removed filter types. See docs/explanations/maintaining-group-filters.md
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
                            ->label('Primary Email')
                            ->relationship('primaryEmailAddress', 'address')
                            ->icon('heroicon-m-envelope'),
                        TextConstraint::make('phone')
                            ->label('Primary Phone')
                            ->relationship('primaryPhoneNumber', 'number')
                            ->icon('heroicon-m-phone'),
                        TextConstraint::make('address')
                            ->label('Primary Address line 1')
                            ->relationship('primaryAddress', 'line_1')
                            ->icon('heroicon-m-map-pin'),
                        TextConstraint::make('address_2')
                            ->label('Primary Address line 2')
                            ->relationship('primaryAddress', 'line_2')
                            ->icon('heroicon-m-map-pin'),
                        RelationshipConstraint::make('tags')
                            ->label('Tags')
                            ->icon('heroicon-m-rectangle-group')
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->multiple()
                                    ->preload(),
                            ),
                        ...self::getAlertConstraints(),
                        TextConstraint::make('holds')
                            ->icon('heroicon-m-exclamation-triangle'),
                        ExistingValuesSelectConstraint::make('gender')
                            ->label('Gender')
                            ->icon('heroicon-m-user-circle')
                            ->multiple(),
                        ExistingValuesSelectConstraint::make('ethnicity')
                            ->label('Ethnicity')
                            ->icon('heroicon-m-globe-alt')
                            ->multiple(),
                        BooleanConstraint::make('sap')
                            ->label('SAP')
                            ->icon('heroicon-m-academic-cap')
                            ->nullable(),
                        BooleanConstraint::make('dual')
                            ->nullable(),
                        BooleanConstraint::make('firstgen')
                            ->label('First Generation')
                            ->icon('heroicon-m-academic-cap')
                            ->nullable(),
                        BooleanConstraint::make('ferpa')
                            ->label('FERPA')
                            ->icon('heroicon-m-lock-open')
                            ->nullable(),
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
                        Constraint::make('careTeam')
                            ->icon('heroicon-m-user-group')
                            ->operators([
                                Operator::make('careTeam')
                                    ->label(fn (bool $isInverse): string => $isInverse ? 'Not my care team' : 'My care team')
                                    ->summary('Care team')
                                    ->baseQuery(fn (Builder $query, bool $isInverse) => $query->{$isInverse ? 'whereDoesntHave' : 'whereHas'}(
                                        'careTeam',
                                        fn (Builder $query) => $query->whereKey(auth()->user()),
                                    )),
                            ]),
                        RelationshipConstraint::make('programs')
                            ->multiple()
                            ->label('Number of Programs')
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
                            ->operators(
                                [
                                    EqualsOperatorWithEnrollmentSemester::class,
                                    HasMaxOperatorWithEnrollmentSemester::class,
                                    HasMinOperatorWithEnrollmentSemester::class,
                                    IsEmptyOperatorWithEnrollmentSemester::class,
                                ]
                            )
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
                        ...self::getEnrollmentTermConstraints(),
                    ])
                    ->constraintPickerColumns([
                        'md' => 2,
                        'lg' => 3,
                        'xl' => 4,
                    ])
                    ->constraintPickerWidth('7xl'),
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                ViewAction::make()
                    ->authorize('view')
                    ->url(fn (Student $record) => StudentResource::getUrl('view', ['record' => $record])),
            ]);
    }

    /**
     * @return array<ExistingValuesSelectConstraint>
     */
    private static function getEnrollmentTermConstraints(): array
    {
        return [
            self::createEnrollmentTermConstraint(
                'firstEnrollmentTerm.semester_name',
                'First Enrolled Term'
            ),
            self::createEnrollmentTermConstraint(
                'mostRecentEnrollmentTerm.semester_name',
                'Most Recent Enrolled Term'
            ),
        ];
    }

    private static function createEnrollmentTermConstraint(string $field, string $label): ExistingValuesSelectConstraint
    {
        return ExistingValuesSelectConstraint::make($field)
            ->label($label)
            ->icon('heroicon-m-academic-cap')
            ->multiple()
            ->options(function () use ($field) {
                [$relationship, $column] = explode('.', $field, 2);

                return Student::query()
                    ->join('enrollments', function (JoinClause $join) use ($relationship) {
                        $join->on('students.sisid', '=', 'enrollments.sisid');

                        if ($relationship === 'firstEnrollmentTerm') {
                            $join->whereRaw('enrollments.start_date = (
                                SELECT MIN(e2.start_date)
                                FROM enrollments e2
                                WHERE e2.sisid = students.sisid
                                AND e2.semester_name IS NOT NULL
                                AND e2.start_date IS NOT NULL
                            )');
                        } elseif ($relationship === 'mostRecentEnrollmentTerm') {
                            $join->whereRaw('enrollments.start_date = (
                                SELECT MAX(e2.start_date)
                                FROM enrollments e2
                                WHERE e2.sisid = students.sisid
                                AND e2.semester_name IS NOT NULL
                                AND e2.start_date IS NOT NULL
                            )');
                        }
                    })
                    ->whereNotNull("enrollments.{$column}")
                    ->distinct()
                    ->orderBy("enrollments.{$column}")
                    ->pluck("enrollments.{$column}")
                    ->filter()
                    ->unique()
                    ->sort()
                    ->mapWithKeys(fn (?string $option): array => $option ? [strtolower($option) => $option] : [])
                    ->all();
            });
    }

    /**
     * @return array<AlertStatusConstraint>
     */
    private static function getAlertConstraints(): array
    {
        return [
            AlertStatusConstraint::make('alertStatus'),
        ];
    }
}
