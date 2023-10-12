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
                QueryBuilder::make('query')
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
                            ->label('Mobile')
                            ->icon('heroicon-m-phone'),
                        QueryBuilder\Constraints\TextConstraint::make('address')
                            ->label('Address')
                            ->icon('heroicon-m-map-pin'),
                    ]),
//                TernaryFilter::make('sap')
//                    ->label('SAP'),
//                TernaryFilter::make('dual'),
//                TernaryFilter::make('ferpa')
//                    ->label('FERPA'),
//                Filter::make('holds')
//                    ->form([
//                        TextInput::make('hold'),
//                    ])
//                    ->query(function (Builder $query, array $data): Builder {
//                        return $query
//                            ->when(
//                                $data['hold'],
//                                fn (Builder $query, $hold): Builder => $query->where('holds', 'ilike', "%{$hold}%"),
//                            );
//                    }),
//                Filter::make('subscribed')
//                    ->query(fn (Builder $query): Builder => $query->whereRelation('subscriptions.user', 'id', auth()->id())),
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
