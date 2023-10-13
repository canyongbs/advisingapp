<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Actions\CreateAction;
use Filament\Tables\Filters\Filter;
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
        return parent::table($table)
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
                        QueryBuilder\Constraints\RelationshipConstraint::make('performances')
                            ->multiple()
                            ->attributeLabel(fn (array $settings): string => Str::plural('performance', $settings['count']))
                            ->icon('heroicon-m-presentation-chart-line'),
                        QueryBuilder\Constraints\RelationshipConstraint::make('enrollments')
                            ->multiple()
                            ->attributeLabel(fn (array $settings): string => Str::plural('enrollment', $settings['count']))
                            ->icon('heroicon-m-folder-open'),
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
