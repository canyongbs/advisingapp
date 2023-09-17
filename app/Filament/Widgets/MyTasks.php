<?php

namespace App\Filament\Widgets;

use Filament\Tables\Table;
use Assist\Task\Models\Task;
use Assist\Task\Enums\TaskStatus;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\TableWidget as BaseWidget;
use Assist\Task\Filament\Resources\TaskResource\Components\TaskViewAction;

class MyTasks extends BaseWidget
{
    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 1,
        'lg' => 2,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->heading('My Tasks')
            ->query(
                auth()->user()
                    ->assignedTasks()
                    ->getQuery()
                    ->byDueDateDesc()
            )
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
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(TaskStatus::cases())->mapWithKeys(fn (TaskStatus $direction) => [$direction->value => \Livewire\str($direction->name)->title()->headline()]))
                    ->multiple(),
            ])
            ->actions([
                TaskViewAction::make(),
            ])
            ->paginated([5]);
    }
}
