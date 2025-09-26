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

namespace AdvisingApp\Report\Abstract\Concerns;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use AdvisingApp\Report\Abstract\Contracts\HasSegmentModel;
use AdvisingApp\Report\Filament\Pages\ProspectCaseReport;
use AdvisingApp\Report\Filament\Pages\StudentCaseReport;
use AdvisingApp\Segment\Enums\SegmentModel;
use AdvisingApp\Segment\Models\Segment;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm as ConcernsHasFiltersForm;
use Illuminate\Database\Eloquent\Builder;

trait HasFiltersForm
{
    use ConcernsHasFiltersForm;

    public function filtersForm(Schema $schema): Schema
    {
        $heading = ($this instanceof StudentCaseReport || $this instanceof ProspectCaseReport) ? 'Date Created' : null;

        $segmentModel = $this instanceof HasSegmentModel ? $this->segmentModel() : null;

        return $schema
            ->components([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate')
                            ->maxDate(fn (Get $get) => $get('endDate') ?: now())
                            ->afterStateUpdated(function (callable $set, mixed $state, Get $get) {
                                if (blank($get('endDate')) && filled($state)) {
                                    $set('endDate', $state);
                                }
                            }),
                        DatePicker::make('endDate')
                            ->minDate(fn (Get $get) => $get('startDate') ?: now())
                            ->maxDate(now())
                            ->afterStateUpdated(function (callable $set, mixed $state, Get $get) {
                                if (blank($get('startDate')) && filled($state)) {
                                    $set('startDate', $state);
                                }
                            }),
                    ])
                    ->heading($heading)
                    ->columns(2),
                Section::make()
                    ->schema([
                        Select::make('populationSegment')
                            ->label('Select Segment')
                            ->options(fn (): array => $this->getSegmentOptions($segmentModel))
                            ->getSearchResultsUsing(fn (string $search): array => $this->getSegmentOptions($segmentModel, $search))
                            ->searchable(),
                    ])
                    ->heading('Advanced Filtering')
                    ->visible($this instanceof HasSegmentModel)
                    ->columns(1),
            ]);
    }

    /**
     * @return array<int, string>
     */
    protected function getSegmentOptions(?SegmentModel $model, ?string $search = null): array
    {
        if (! $model) {
            return [];
        }

        return Segment::query()
            ->where('model', $model)
            ->when($search, fn (Builder $query) => $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']))
            ->orderByDesc('created_at')
            ->limit(15)
            ->pluck('name', 'id')
            ->all();
    }
}
