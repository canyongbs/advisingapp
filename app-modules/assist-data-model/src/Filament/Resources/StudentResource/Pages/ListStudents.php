<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Actions\CreateAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use Filament\Tables\Actions\BulkActionGroup;
use App\Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\Engagement\Filament\Actions\BulkEngagementAction;
use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\Notifications\Filament\Actions\SubscribeBulkAction;
use Filament\Tables\Actions\CreateAction as TableCreateAction;
use Assist\Notifications\Filament\Actions\SubscribeTableAction;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(Student::displayNameKey())
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('mobile')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('sisid')
                    ->searchable(),
                TextColumn::make('otherid')
                    ->searchable(),
            ])
            ->filters([
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
                        QueryBuilder\Constraints\TextConstraint::make('programDeclareDt')
                            ->label('Program Start Date')
                            ->relationship('programs', 'declare_dt'),
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
                    ]),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                ViewAction::make(),
                SubscribeTableAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    SubscribeBulkAction::make(),
                    BulkEngagementAction::make(context: 'students'),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                TableCreateAction::make(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
