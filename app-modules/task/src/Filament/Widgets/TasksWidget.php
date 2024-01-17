<?php

namespace AdvisingApp\Task\Filament\Widgets;

use App\Models\User;
use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use AdvisingApp\Task\Enums\TaskStatus;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\TableWidget as BaseWidget;
use AdvisingApp\Task\Filament\Resources\TaskResource\Components\TaskViewAction;

abstract class TasksWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading($this->title())
            ->query(function () {
                /** @var User $user */
                $user = auth()->user();

                return $user
                    ->assignedTasks()
                    ->whereMorphedTo('concern', $this->concern())
                    ->getQuery()
                    ->byNextDue();
            })
            ->columns([
                IdColumn::make(),
                TextColumn::make('description')
                    ->searchable()
                    ->wrap()
                    ->limit(50),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('due')
                    ->label('Due Date')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(TaskStatus::class)
                    ->multiple()
                    ->default([TaskStatus::InProgress->value, TaskStatus::Pending->value]),
            ])
            ->actions([
                TaskViewAction::make(),
            ])
            ->paginated([5]);
    }

    abstract public function title(): string;

    abstract public function concern(): string;
}
