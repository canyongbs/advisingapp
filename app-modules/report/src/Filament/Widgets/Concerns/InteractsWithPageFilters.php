<?php

namespace AdvisingApp\Report\Filament\Widgets\Concerns;

use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters as InteractsWithPageFiltersBase;

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

    // $startDate = filled($this->filters['startDate'] ?? null)
    //     ? Carbon::parse($this->filters['startDate'])->startOfDay()
    //     : null;

    // $endDate = filled($this->filters['endDate'] ?? null)
    //     ? Carbon::parse($this->filters['endDate'])->endOfDay()
    //     : null;
}
