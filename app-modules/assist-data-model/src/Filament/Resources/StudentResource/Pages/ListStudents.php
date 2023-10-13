<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

use App\Filament\Tables\Filters\QueryBuilder;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\BulkActionGroup;
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
                        QueryBuilder\Constraints\RelationshipConstraint::make('programs')
                            ->multiple()
                            ->icon('heroicon-m-academic-cap'),
                        QueryBuilder\Constraints\RelationshipConstraint::make('performances')
                            ->multiple()
                            ->icon('heroicon-m-presentation-chart-line'),
                        QueryBuilder\Constraints\RelationshipConstraint::make('enrollments')
                            ->multiple()
                            ->icon('heroicon-m-folder-open'),
                    ]),
                Filter::make('subscribed')
                    ->query(fn (Builder $query): Builder => $query->whereRelation('subscriptions.user', 'id', auth()->id())),
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
