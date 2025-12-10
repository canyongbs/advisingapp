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

use AdvisingApp\StudentDataModel\Models\Student;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StudentOverviewGenderDoughnutChart extends ChartReportWidget
{
    protected ?string $heading = 'Gender';

    protected int | string | array $columnSpan = [
        'sm' => 2,
        'md' => 2,
        'lg' => 2,
    ];

    protected ?string $maxHeight = '240px';

    public function render(): View
    {
        $data = $this->getData()['datasets'][0]['data'];

        $data = $data->filter();

        if (! count($data)) {
            return view('livewire.no-widget-data');
        }

        return view($this->view, $this->getViewData());
    }

    public function getData(): array
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        $groupId = $this->getSelectedGroup();

        $shouldBypassCache = filled($startDate) || filled($endDate) || filled($groupId);

        $genderData = $shouldBypassCache
            ? $this->getGenderData($startDate, $endDate, $groupId)
            : Cache::tags(["{{$this->cacheTag}}"])->remember('student_by_gender', now()->addHours(24), function () {
                return $this->getGenderData();
            });

        return [
            'labels' => $genderData->pluck('gender'),
            'datasets' => [
                [
                    'data' => $genderData->pluck('count'),
                    'backgroundColor' => $genderData->pluck('bg_color'),
                    'hoverOffset' => 4,
                ],
            ],
        ];
    }

    /**
    * @return array<string, mixed>
    */
    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => false,
                ],
                'y' => [
                    'display' => false,
                ],
            ],
        ];
    }

    protected function getRgbString(): string
    {
        return 'rgb(' . random_int(0, 255) . ',' . random_int(0, 255) . ',' . random_int(0, 255) . ')';
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    /**
     * @return Collection<int, Student>
     */
    protected function getGenderData(?Carbon $startDate = null, ?Carbon $endDate = null, ?string $groupId = null): Collection
    {
        return Student::query()
            ->select(
                DB::raw('LOWER(gender) as gender_lower'),
                DB::raw('MIN(gender) as gender'),
                DB::raw('COUNT(*) as count')
            )
            ->whereNotNull('gender')
            ->where('gender', '!=', '')
            ->when(
                $startDate && $endDate,
                fn (Builder $query): Builder => $query->whereBetween('created_at_source', [$startDate, $endDate])
            )
            ->when(
                $groupId,
                fn (Builder $query) => $this->groupFilter($query, $groupId)
            )
            ->groupBy('gender_lower')
            ->orderBy('gender_lower')
            ->get()->map(function (Student $student) {
                $student['bg_color'] = $this->getRgbString();

                return $student;
            });
    }
}
