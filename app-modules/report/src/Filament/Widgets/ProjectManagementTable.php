<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Report\Filament\Widgets;

use AdvisingApp\Project\Models\Project;
use AdvisingApp\Report\Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

class ProjectManagementTable extends BaseWidget
{
    use InteractsWithPageFilters;

    #[Locked]
    public string $cacheTag;

    protected static ?string $heading = 'Projects';

    protected static ?string $pollingInterval = null;

    protected int | string | array $columnSpan = [
        'sm' => 12,
        'md' => 12,
        'lg' => 12,
    ];

    public function mount(string $cacheTag): void
    {
        $this->cacheTag = $cacheTag;
    }

    #[On('refresh-widgets')]
    public function refreshWidget(): void {}

    public function table(Table $table): Table
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        return $table
            ->query(
                Project::query()
                    ->when($startDate && $endDate, function (Builder $query) use ($startDate, $endDate): void {
                        $query->whereBetween('created_at', [$startDate, $endDate]);
                    })
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Project Name')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('M d, Y h:i A'),
                TextColumn::make('managerUsers.name')
                    ->label('Managers')
                    ->formatStateUsing(fn ($record) => $record->managerUsers->count())
                    ->searchable(),
                TextColumn::make('auditorUsers.name')
                    ->label('Auditors')
                    ->formatStateUsing(fn ($record) => $record->auditorUsers->count())
                    ->searchable(),
                TextColumn::make('files_count')
                    ->label('Files')
                    ->counts('files'),
                TextColumn::make('pipelines_count')
                    ->label('Pipelines')
                    ->counts('pipelines'),
                TextColumn::make('milestones_count')
                    ->label('Milestones')
                    ->counts('milestones'),
                TextColumn::make('tasks_count')
                    ->label('Tasks')
                    ->counts('tasks'),
            ])
            ->paginated([5])
            ->filtersFormWidth(Width::Small);
    }
}
