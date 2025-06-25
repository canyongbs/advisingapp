<?php

namespace AdvisingApp\Report\Filament\Widgets\Concerns;

use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters as InteractsWithPageFiltersBase;
use Illuminate\Support\Collection;

trait InteractsWithPageFilters
{
    use InteractsWithPageFiltersBase;

    public function getStartDate(): ?Carbon
    {
        $startDate = $this->filters['startDate'] ?? null;

        return filled($startDate) ? Carbon::parse($startDate)->startOfDay() : null;
    }

    public function getEndDate(): ?Carbon
    {
        $endDate = $this->filters['endDate'] ?? null;

        return filled($endDate) ? Carbon::parse($endDate)->endOfDay() : null;
    }

    /**
     * @return Collection<int, Carbon>
     */
    public function getMonthRange(Carbon $startDate, Carbon $endDate): Collection
    {
        $monthStart = $startDate->copy()->startOfMonth();
        $monthEnd = $endDate->copy()->startOfMonth();

        $months = collect();
        $current = $monthStart->copy();

        while ($current->lte($monthEnd)) {
            $months->push($current->copy());
            $current->addMonth();
        }

        return $months;
    }
}
