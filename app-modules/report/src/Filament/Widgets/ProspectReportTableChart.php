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

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Query\Builder;
use Livewire\Attributes\On;

class ProspectReportTableChart extends TableWidget
{
    use InteractsWithPageFilters;

    public string $cacheTag;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Most Recent Prospects Added';

    protected static ?string $pollingInterval = null;

    protected static bool $isLazy = false;

    public function mount(string $cacheTag)
    {
        $this->cacheTag = $cacheTag;
    }

    #[On('refresh-widgets')]
    public function refreshWidget() {}

    public function table(Table $table): Table
    {
        return $table
            ->query(
                function () {
                    $key = (new Prospect())->getKeyName();

                    $startDate = $this->getStartDate();
                    $endDate = $this->getEndDate();

                    return Prospect::whereIn($key, function ($query) use ($key, $startDate, $endDate) {
                        $query->select($key)
                            ->from((new Prospect())->getTable())
                            ->when($startDate && $endDate, function (Builder $query) use ($startDate, $endDate): Builder {
                                return $query->whereBetween('created_at', [$startDate, $endDate]);
                            })
                            ->orderBy('created_at', 'desc')
                            ->take(100);
                    })->orderBy('created_at', 'desc');
                }
            )
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('full_name')
                    ->label('Name'),
                TextColumn::make('primaryEmailAddress.address')
                    ->label('Email'),
                TextColumn::make('status.name')
                    ->badge()
                    ->color(fn (Prospect $record) => $record->status->color->value),
                TextColumn::make('createdBy.name')
                    ->label('Created By'),
                TextColumn::make('created_at')
                    ->label('Created Date'),
            ])
            ->paginated([10]);
    }
}
