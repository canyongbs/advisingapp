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

use AdvisingApp\CaseManagement\Filament\Resources\CaseResource;
use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

class ProspectCaseTable extends BaseWidget
{
    use InteractsWithPageFilters;

    #[Locked]
    public string $cacheTag;

    protected static ?string $heading = 'Prospect Cases';

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

        return $table
            ->query(
                function () use ($startDate, $endDate): Builder {
                    return CaseModel::query()
                        ->whereHasMorph('respondent', Prospect::class)
                        ->when(
                            $startDate && $endDate,
                            fn (Builder $query): Builder => $query->whereBetween('created_at', [$startDate, $endDate])
                        );
                }
            )
            ->columns([
                TextColumn::make('case_number')
                    ->label('Case Number'),
                TextColumn::make('respondent')
                    ->label('Related To')
                    ->getStateUsing(function (CaseModel $record): string {
                        $respondent = $record->respondent;
                        assert($respondent instanceof Prospect);

                        return $respondent->{Prospect::displayNameKey()};
                    }),
                TextColumn::make('assignedTo.user.name')
                    ->label('Assigned To'),
                TextColumn::make('sla_response_seconds')
                    ->label('Response')
                    ->getStateUsing(function (CaseModel $record) {
                        return $record->getSlaResponseSeconds();
                    }),
                TextColumn::make('sla_resolution_seconds')
                    ->label('Resolution')
                    ->getStateUsing(function (CaseModel $record) {
                        return $record->getSlaResolutionSeconds();
                    }),
            ])
            ->actions([
                ViewAction::make()
                    ->url(fn (CaseModel $record) => CaseResource::getUrl('view', ['record' => $record, 'referrer' => 'respondentReport'])),
            ])
            ->paginated([5])
            ->filtersFormWidth(MaxWidth::Small);
    }
}
