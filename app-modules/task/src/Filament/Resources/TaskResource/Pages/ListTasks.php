<?php

namespace Assist\Task\Filament\Resources\TaskResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use Assist\Task\Models\Task;
use Assist\Task\Enums\TaskStatus;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Assist\Prospect\Models\Prospect;
use Filament\Infolists\Components\Grid;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\UserResource;
use Filament\Infolists\Components\Split;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Filters\SelectFilter;
use Assist\AssistDataModel\Models\Student;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\Task\Filament\Resources\TaskResource;
use Assist\Prospect\Filament\Resources\ProspectResource;
use Assist\AssistDataModel\Filament\Resources\StudentResource;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('description')
                    ->searchable()
                    ->wrap()
                    ->limit(50),
                TextColumn::make('status')
                    ->formatStateUsing(fn (TaskStatus $state): string => str($state->value)->title()->headline())
                    ->badge()
                    ->color(fn (Task $record) => $record->status->getTableColor()),
                TextColumn::make('due')
                    ->label('Due Date')
                    ->sortable(),
                TextColumn::make('assignedTo.name')
                    ->label('Assigned To')
                    ->url(fn (Task $record) => $record->assignedTo ? UserResource::getUrl('view', ['record' => $record->assignedTo]) : null),
                TextColumn::make('concern.full')
                    ->label('Concern')
                    ->url(fn (Task $record) => match ($record->concern ? $record->concern::class : null) {
                        Student::class => StudentResource::getUrl('view', ['record' => $record->concern]),
                        Prospect::class => ProspectResource::getUrl('view', ['record' => $record->concern]),
                        default => null,
                    }),
            ])
            ->filters([
                Filter::make('my_tasks')
                    ->label('My Tasks')
                    ->query(
                        fn ($query) => $query->where('assigned_to', auth()->id())
                    )
                    ->default(),
                SelectFilter::make('assignedTo')
                    ->label('Assigned To')
                    ->relationship('assignedTo', 'name')
                    ->searchable()
                    ->multiple(),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(TaskStatus::cases())->mapWithKeys(fn (TaskStatus $direction) => [$direction->value => \Livewire\str($direction->name)->title()->headline()]))
                    ->multiple()
                    ->default(
                        [
                            TaskStatus::PENDING->value,
                            TaskStatus::IN_PROGRESS->value,
                        ]
                    ),
            ])
            ->actions([
                ViewAction::make()
                    //->action(fn (Task $record) => $record->update(['status' => TaskStatus::COMPLETED]))
                    ->extraModalFooterActions(
                        [
                            Action::make('mark_as_completed')
                                ->label('Mark as Completed')
                                ->action(fn (Task $record) => $record->update(['status' => TaskStatus::COMPLETED]))
                                ->cancelParentActions()
                                ->hidden(fn (Task $record) => $record->status->value === TaskStatus::COMPLETED->value),
                        ]
                    )
                    ->infolist(
                        [
                            Split::make(
                                [
                                    Grid::make()
                                        ->schema(
                                            [
                                                TextEntry::make('description')
                                                    ->columnSpanFull(),
                                                TextEntry::make('assignedTo.name')
                                                    ->label('Assigned To')
                                                    ->url(fn (Task $record) => $record->assignedTo ? UserResource::getUrl('view', ['record' => $record->assignedTo]) : null),
                                                TextEntry::make('concern.full')
                                                    ->label('Concern')
                                                    ->url(fn (Task $record) => match ($record->concern ? $record->concern::class : null) {
                                                        Student::class => StudentResource::getUrl('view', ['record' => $record->concern]),
                                                        Prospect::class => ProspectResource::getUrl('view', ['record' => $record->concern]),
                                                        default => null,
                                                    }),
                                            ]
                                        ),
                                    Fieldset::make('metadata')
                                        ->label('Metadata')
                                        ->schema(
                                            [
                                                TextEntry::make('status')
                                                    ->formatStateUsing(fn (TaskStatus $state): string => str($state->value)->title()->headline())
                                                    ->badge()
                                                    ->color(fn (Task $record) => $record->status->getTableColor()),
                                                TextEntry::make('due')
                                                    ->label('Due Date'),
                                            ]
                                        ),
                                ]
                            )->from('md'),
                        ]
                    ),
                //->modalSubmitAction(fn (StaticAction $action) => $action->label('Mark as Completed')),
                EditAction::make(),
            ])
            ->recordUrl(null)
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
