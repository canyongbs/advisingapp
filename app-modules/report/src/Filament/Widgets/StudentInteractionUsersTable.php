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

use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Report\Filament\Exports\StudentInteractionUsersTableExportExporter;
use AdvisingApp\Report\Filament\Widgets\Concerns\InteractsWithPageFilters;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

class StudentInteractionUsersTable extends BaseWidget
{
    use InteractsWithPageFilters;

    #[Locked]
    public string $cacheTag;

    protected static ?string $heading = 'Users Interaction Overview';

    protected static bool $isLazy = false;

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
        $segmentId = $this->getSelectedSegment();

        return $table
            ->headerActions([
                ExportAction::make()
                    ->exporter(StudentInteractionUsersTableExportExporter::class)
                    ->formats([
                        ExportFormat::Csv,
                    ]),
            ])
            ->query(
                function () use ($startDate, $endDate, $segmentId): Builder {
                    return User::query()
                        ->whereHas('interactions', function (Builder $query) use ($startDate, $endDate, $segmentId): Builder {
                            return $query
                                ->whereHasMorph('interactable', Student::class, function (Builder $query) use ($segmentId) {
                                    $query->when(
                                        $segmentId,
                                        fn (Builder $query) => $this->segmentFilter($query, $segmentId)
                                    );
                                })
                                ->when(
                                    $startDate && $endDate,
                                    function (Builder $query) use ($startDate, $endDate): Builder {
                                        return $query->whereBetween('created_at', [$startDate, $endDate]);
                                    }
                                );
                        })
                        ->with([
                            'interactions',
                            'team',
                        ]);
                }
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->description(function ($record) {
                        $jobTitle = $record->job_title ?? null;
                        $teamName = $record->team->name ?? null;

                        if ($jobTitle && $teamName) {
                            return "{$jobTitle} ({$teamName})";
                        } elseif ($jobTitle) {
                            return $jobTitle;
                        } elseif ($teamName) {
                            return $teamName;
                        }

                        return null;
                    })
                    ->searchable(
                        query: function (Builder $query, string $search) {
                            $search = Str::lower($search);
                            $query->where(function ($query) use ($search) {
                                $query->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                                    ->orWhereRaw('LOWER(job_title) LIKE ?', ["%{$search}%"])
                                    ->orWhereHas('team', function ($teamQuery) use ($search) {
                                        $teamQuery->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                                    });
                            });
                        }
                    ),
                TextColumn::make('first_interaction_at')
                    ->label('First')
                    ->getStateUsing(function ($record) use ($startDate, $endDate) {
                        $first = $record
                            ->interactions()
                            ->whereHasMorph('interactable', Student::class)
                            ->when(
                                $startDate && $endDate,
                                fn (Builder $query): Builder => $query->whereBetween('created_at', [$startDate, $endDate])
                            )
                            ->orderBy('created_at')
                            ->first();

                        return $first ? $first->created_at->format('M d, Y') : null;
                    }),
                TextColumn::make('most_recent_interaction_at')
                    ->label('Most Recent')
                    ->getStateUsing(function ($record) use ($startDate, $endDate) {
                        $last = $record
                            ->interactions()
                            ->whereHasMorph('interactable', Student::class)
                            ->when(
                                $startDate && $endDate,
                                fn (Builder $query): Builder => $query->whereBetween('created_at', [$startDate, $endDate])
                            )
                            ->orderByDesc('created_at')
                            ->first();

                        return $last ? $last->created_at->format('M d, Y') : null;
                    }),
                TextColumn::make('total_interactions')
                    ->label('Total')
                    ->getStateUsing(function ($record) use ($startDate, $endDate) {
                        return $record
                            ->interactions()
                            ->whereHasMorph('interactable', Student::class)
                            ->when(
                                $startDate && $endDate,
                                fn (Builder $query): Builder => $query->whereBetween('created_at', [$startDate, $endDate])
                            )
                            ->count();
                    }),
                TextColumn::make('total_interactions_percent')
                    ->label('Total %')
                    ->getStateUsing(function ($record) use ($startDate, $endDate) {
                        $allInteractions = Interaction::whereHasMorph('interactable', Student::class)->count();
                        $userInteractionsCount = $record
                            ->interactions()
                            ->whereHasMorph('interactable', Student::class)
                            ->when(
                                $startDate && $endDate,
                                fn (Builder $query): Builder => $query->whereBetween('created_at', [$startDate, $endDate])
                            )
                            ->count();

                        if ($allInteractions > 0) {
                            $percent = round(($userInteractionsCount / $allInteractions) * 100);

                            return "{$percent}%";
                        }

                        return '0%';
                    }),
                TextColumn::make('avg_interaction_duration')
                    ->label('Avg. Duration')
                    ->getStateUsing(function ($record) use ($startDate, $endDate) {
                        $durations = $record
                            ->interactions()
                            ->whereHasMorph('interactable', Student::class)
                            ->when(
                                $startDate && $endDate,
                                fn (Builder $query): Builder => $query->whereBetween('created_at', [$startDate, $endDate])
                            )
                            ->get()
                            ->map(function ($interaction) {
                                return Carbon::parse($interaction->end_datetime)
                                    ->diffInMinutes(Carbon::parse($interaction->start_datetime), true);
                            })->filter();

                        if ($durations->count() > 0) {
                            $avg = round($durations->avg());

                            return "{$avg} Min.";
                        }

                        return null;
                    }),
            ])
            ->filters([
                SelectFilter::make('name')
                    ->label('Name')
                    ->options(
                        fn () => User::query()
                            ->whereNotNull('name')
                            ->groupBy('name')
                            ->orderByRaw('LOWER(name)')
                            ->pluck('name')
                            ->mapWithKeys(fn (?string $option) => $option ? [Str::lower($option) => Str::title($option)] : [])
                            ->all()
                    )
                    ->multiple()
                    ->placeholder('Select Name')
                    ->searchable()
                    ->query(function (Builder $query, array $data) {
                        if (! empty($data['values'])) {
                            $query->whereIn(
                                DB::raw('LOWER(name)'),
                                array_map(fn ($value) => Str::lower($value), $data['values'])
                            );
                        }
                    }),
                SelectFilter::make('job_title')
                    ->label('Job Title')
                    ->options(
                        fn () => User::query()
                            ->whereNotNull('job_title')
                            ->groupBy('job_title')
                            ->orderByRaw('LOWER(job_title)')
                            ->pluck('job_title')
                            ->mapWithKeys(fn (?string $option) => $option ? [Str::lower($option) => Str::title($option)] : [])
                            ->all()
                    )
                    ->multiple()
                    ->placeholder('Select Job Title')
                    ->searchable()
                    ->query(function (Builder $query, array $data) {
                        if (! empty($data['values'])) {
                            $query->whereIn(
                                DB::raw('LOWER(job_title)'),
                                array_map(fn ($value) => Str::lower($value), $data['values'])
                            );
                        }
                    }),
                SelectFilter::make('team')
                    ->label('Team')
                    ->relationship('team', 'name')
                    ->multiple()
                    ->placeholder('Select Team')
                    ->searchable()
                    ->preload(),
            ])
            ->paginated([5])
            ->filtersFormWidth(MaxWidth::Small);
    }
}
