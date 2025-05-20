<?php

namespace AdvisingApp\Report\Filament\Widgets;

use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Prospect\Models\Prospect;
use App\Models\User;
use Carbon\Carbon;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

class ProspectInteractionUsersTable extends BaseWidget
{
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

    public function mount(string $cacheTag)
    {
        $this->cacheTag = $cacheTag;
    }

    #[On('refresh-widgets')]
    public function refreshWidget()
    {
        $this->dispatch('$refresh');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                function () {
                    return User::query()
                        ->whereHas('interactions')
                        ->with([
                            'interactions' => function ($query) {
                                $query->whereHasMorph('interactable', Prospect::class);
                            },
                            'team',
                        ]);
                }
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->description(function ($record) {
                        $jobTitle = $record->job_title ?? null;
                        $teamName = $record->team?->name ?? null;

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
                    ->getStateUsing(function ($record) {
                        $first = $record->interactions->sortBy('created_at')->first();

                        return $first ? $first->created_at->format('M d, Y') : null;
                    }),
                TextColumn::make('most_recent_interaction_at')
                    ->label('Most Recent')
                    ->getStateUsing(function ($record) {
                        $last = $record->interactions->sortByDesc('created_at')->first();

                        return $last ? $last->created_at->format('M d, Y') : null;
                    }),
                TextColumn::make('total_interactions')
                    ->label('Total')
                    ->getStateUsing(function ($record) {
                        return $record->interactions->count();
                    }),
                TextColumn::make('total_interactions_percent')
                    ->label('Total %')
                    ->getStateUsing(function ($record) {
                        $allInteractions = Interaction::whereHasMorph('interactable', Prospect::class)->count();
                        $userInteractionsCount = $record->interactions->count();

                        if ($allInteractions > 0) {
                            $percent = round(($userInteractionsCount / $allInteractions) * 100);

                            return "{$percent}%";
                        }

                        return '0%';
                    }),
                TextColumn::make('avg_interaction_duration')
                    ->label('Avg. Duration')
                    ->getStateUsing(function ($record) {
                        $durations = $record->interactions->map(function ($interaction) {
                            return Carbon::parse($interaction->end_datetime)
                                ->diffInMinutes(Carbon::parse($interaction->start_datetime), Carbon::DIFF_ABSOLUTE);
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
                            $query->whereRaw('LOWER(name) IN (?)', array_map(fn ($value) => Str::lower($value), $data['values']));
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
                            $query->whereRaw('LOWER(job_title) IN (?)', array_map(fn ($value) => Str::lower($value), $data['values']));
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
