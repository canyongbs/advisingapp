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

namespace AdvisingApp\Prospect\Filament\Widgets;

use AdvisingApp\Alert\Enums\SystemAlertStatusClassification;
use AdvisingApp\CaseManagement\Enums\SystemCaseClassification;
use AdvisingApp\Engagement\Enums\EngagementResponseStatus;
use AdvisingApp\Prospect\Filament\Resources\Prospects\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Widgets\Concerns\InteractsWithPageFilters;
use AdvisingApp\Task\Enums\TaskStatus;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Reactive;

class ProspectsActionCenterWidget extends TableWidget
{
    use InteractsWithPageFilters;

    /**
     * @var int | string | array<string, int | null>
     */
    protected int | string | array $columnSpan = 'full';

    #[Reactive]
    public string $activeTab;

    public function table(Table $table): Table
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        $groupId = $this->getSelectedGroup();

        return $table
            ->heading('Action Center Records')
            ->query(function () use ($groupId, $startDate, $endDate) {
                $query = Prospect::query()->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('created_at', [$startDate, $endDate])
                );

                if ($groupId) {
                    $this->groupFilter($query, $groupId);
                }

                return $query;
            })
            ->columns([
                TextColumn::make('full_name')
                    ->label('Prospect Name')
                    ->searchable(),
                TextColumn::make('engagement_responses_count')
                    ->label('New Messages')
                    ->counts(['engagementResponses' => fn (Builder $query) => $query->where('status', EngagementResponseStatus::New)])
                    ->sortable(),
                TextColumn::make('alerts_count')
                    ->label('Open Alerts')
                    ->counts(['alerts' => fn (Builder $query) => $query->whereHas('status', fn (Builder $query) => $query->whereNotIn('classification', [SystemAlertStatusClassification::Resolved, SystemAlertStatusClassification::Canceled]))])
                    ->sortable(),
                TextColumn::make('tasks_count')
                    ->label('Open Tasks')
                    ->counts(['tasks' => fn (Builder $query) => $query->whereNotIn('status', [TaskStatus::Completed, TaskStatus::Canceled])])
                    ->sortable(),
                TextColumn::make('cases_count')
                    ->label('Open Cases')
                    ->counts(['cases' => fn (Builder $query) => $query->whereRelation('status', 'classification', '!=', SystemCaseClassification::Closed)])
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('messages')
                    ->options(EngagementResponseStatus::class)
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        return $query->whereRelation('engagementResponses', 'status', $data['value']);
                    }),
                SelectFilter::make('cases')
                    ->options(['open' => 'Open', 'closed' => 'Closed'])
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        return $query->whereHas('cases', function (Builder $query) use ($data): Builder {
                            if ($data['value'] === 'open') {
                                return $query->whereRelation('status', 'classification', '!=', SystemCaseClassification::Closed);
                            }

                            if ($data['value'] === 'closed') {
                                return $query->whereRelation('status', 'classification', SystemCaseClassification::Closed);
                            }

                            return $query;
                        });
                    }),
                SelectFilter::make('alerts')
                    ->options(['open' => 'Open', 'closed' => 'Closed'])
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        return $query->whereHas('alerts', function (Builder $query) use ($data): Builder {
                            if ($data['value'] === 'open') {
                                return $query->whereHas('status', fn (Builder $query) => $query->whereNotIn('classification', [SystemAlertStatusClassification::Resolved, SystemAlertStatusClassification::Canceled]));
                            }

                            if ($data['value'] === 'closed') {
                                return $query->whereHas('status', fn (Builder $query) => $query->whereIn('classification', [SystemAlertStatusClassification::Resolved, SystemAlertStatusClassification::Canceled]));
                            }

                            return $query;
                        });
                    }),
                SelectFilter::make('tasks')
                    ->options(['open' => 'Open', 'closed' => 'Closed'])
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        return $query->whereHas('tasks', function (Builder $query) use ($data): Builder {
                            if ($data['value'] === 'open') {
                                return $query->whereNotIn('status', [TaskStatus::Completed, TaskStatus::Canceled]);
                            }

                            if ($data['value'] === 'closed') {
                                return $query->whereIn('status', [TaskStatus::Completed, TaskStatus::Canceled]);
                            }

                            return $query;
                        });
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Go to Prospect')
                    ->url(fn (Prospect $record): string => ProspectResource::getUrl('view', ['record' => $record]), shouldOpenInNewTab: true)
                    ->icon('heroicon-m-arrow-top-right-on-square'),
            ])
            ->paginationMode(PaginationMode::Default);
    }
}
