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

use AdvisingApp\Prospect\Filament\Resources\Prospects\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Widgets\Concerns\InteractsWithPageFilters;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\StudentResource;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Models\Task;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

class MostRecentTasksTable extends BaseWidget
{
    use InteractsWithPageFilters;

    public string $cacheTag;

    public string $educatableType;

    protected static ?string $heading = 'Most Recent Tasks Added';

    protected static ?string $pollingInterval = null;

    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 4,
        'lg' => 4,
    ];

    public function mount(string $cacheTag, string $educatableType)
    {
        $this->cacheTag = $cacheTag;

        $this->educatableType = $educatableType;
    }

    #[On('refresh-widgets')]
    public function refreshWidget() {}

    public function table(Table $table): Table
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        $groupId = $this->getSelectedGroup();

        return $table
            ->query(function () use ($startDate, $endDate, $groupId): Builder {
                return Task::query()
                    ->whereHasMorph('concern', $this->educatableType, function (Builder $query) use ($groupId) {
                        $query->when($groupId, fn (Builder $query) => $this->groupFilter($query, $groupId));
                    })
                    ->when($startDate && $endDate, function (Builder $query) use ($startDate, $endDate): Builder {
                        return $query->whereBetween('created_at', [$startDate, $endDate]);
                    })
                    ->orderBy('created_at', 'desc')
                    ->limit(10);
            })
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('status'),
                TextColumn::make('association')
                    ->label('Association')
                    ->state(fn (Task $record): ?string => ! is_null($record->concern) ? match ($record->concern::class) {
                        Student::class => 'Student',
                        Prospect::class => 'Prospect',
                    } : 'Unrelated'),
                TextColumn::make('concern.display_name')
                    ->label('Related To')
                    ->state(fn (Task $record): ?string => $record->concern?->{$record->concern::displayNameKey()} ?? 'N/A')
                    ->url(fn (Task $record) => match ($record->concern ? $record->concern::class : null) {
                        Student::class => StudentResource::getUrl('view', ['record' => $record->concern]),
                        Prospect::class => ProspectResource::getUrl('view', ['record' => $record->concern]),
                        default => null,
                    }),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime(),
            ])
            ->paginated([10]);
    }
}
