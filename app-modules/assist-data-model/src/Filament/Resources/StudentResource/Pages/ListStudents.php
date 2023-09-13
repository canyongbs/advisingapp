<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use Filament\Tables\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\Notifications\Actions\SubscriptionToggle;
use Assist\Engagement\Filament\Actions\BulkEngagementAction;
use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Filament\Tables\Actions\CreateAction as TableCreateAction;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('full')
                    ->label('Name')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where('full', 'ilike', "%{$search}%");
                    })
                    ->sortable(),
            ])
            ->filters([
                Filter::make('subscribed')
                    ->query(fn (Builder $query): Builder => $query->whereRelation('subscriptions.user', 'id', auth()->id())),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('subscribe')
                    ->label(fn (Student $record) => $record->subscriptions()->whereHas('user', fn (Builder $query) => $query->where('user_id', auth()->id()))->exists() ? 'Unsubscribe' : 'Subscribe')
                    ->icon(fn (Student $record) => $record->subscriptions()->whereHas('user', fn (Builder $query) => $query->where('user_id', auth()->id()))->exists() ? 'heroicon-s-bell-slash' : 'heroicon-s-bell')
                    ->action(fn (Student $record) => resolve(SubscriptionToggle::class)->handle(auth()->user(), $record)),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('toggle_subscription')
                        ->icon('heroicon-s-bell')
                        ->action(fn (Collection $records) => $records->each(fn (Student $record) => resolve(SubscriptionToggle::class)->handle(auth()->user(), $record))),
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
