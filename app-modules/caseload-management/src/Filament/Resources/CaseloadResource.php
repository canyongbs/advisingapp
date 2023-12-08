<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\CaseloadManagement\Filament\Resources;

use Exception;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use AdvisingApp\Prospect\Models\Prospect;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use AdvisingApp\AssistDataModel\Models\Student;
use App\Filament\Tables\Filters\QueryBuilder;
use AdvisingApp\CaseloadManagement\Models\Caseload;
use AdvisingApp\CaseloadManagement\Enums\CaseloadModel;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use AdvisingApp\CaseloadManagement\Filament\Resources\CaseloadResource\Pages\EditCaseload;
use AdvisingApp\CaseloadManagement\Filament\Resources\CaseloadResource\Pages\ListCaseloads;
use AdvisingApp\CaseloadManagement\Filament\Resources\CaseloadResource\Pages\CreateCaseload;

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
                    QueryBuilder\Constraints\TextConstraint::make('programFoi')
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
        ];
    }

    private static function prospectFilters(): array
    {
        return [
            QueryBuilder::make()
                ->constraints([
                    TextConstraint::make('first_name')
                        ->icon('heroicon-m-user'),
                    TextConstraint::make('last_name')
                        ->icon('heroicon-m-user'),
                    TextConstraint::make('full_name')
                        ->icon('heroicon-m-user'),
                    TextConstraint::make('preferred')
                        ->label('Preferred Name')
                        ->icon('heroicon-m-user'),
                    TextConstraint::make('email')
                        ->label('Email Address')
                        ->icon('heroicon-m-envelope'),
                    TextConstraint::make('email_2')
                        ->label('Email Address 2')
                        ->icon('heroicon-m-envelope'),
                    TextConstraint::make('mobile')
                        ->icon('heroicon-m-phone'),
                    TextConstraint::make('phone')
                        ->icon('heroicon-m-phone'),
                    TextConstraint::make('address')
                        ->icon('heroicon-m-map-pin'),
                    TextConstraint::make('address_2')
                        ->icon('heroicon-m-map-pin'),
                    BooleanConstraint::make('sms_opt_out')
                        ->label('SMS Opt Out')
                        ->icon('heroicon-m-chat-bubble-bottom-center'),
                    BooleanConstraint::make('email_bounce')
                        ->icon('heroicon-m-arrow-uturn-left'),
                    TextConstraint::make('hsgrad')
                        ->label('HS Grad')
                        ->icon('heroicon-m-academic-cap'),
                    RelationshipConstraint::make('status')
                        ->icon('heroicon-m-flag')
                        ->selectable(
                            RelationshipConstraint\Operators\IsRelatedToOperator::make()
                                ->titleAttribute('name')
                                ->multiple()
                                ->preload(),
                        ),
                    RelationshipConstraint::make('source')
                        ->icon('heroicon-m-arrow-left-on-rectangle')
                        ->selectable(
                            RelationshipConstraint\Operators\IsRelatedToOperator::make()
                                ->titleAttribute('name')
                                ->multiple()
                                ->preload(),
                        ),
                ])
                ->constraintPickerColumns([
                    'md' => 2,
                    'lg' => 3,
                    'xl' => 4,
                ])
                ->constraintPickerWidth('7xl'),
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
                    return $record->status->color->value;
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
