<?php

namespace Assist\CaseloadManagement\Actions;

use Illuminate\Database\Eloquent\Collection;
use Assist\CaseloadManagement\Models\Caseload;
use Assist\CaseloadManagement\Filament\Resources\CaseloadResource;

class TranslateCaseloadFilters
{
    public function handle(Caseload $caseload): Collection|array
    {
        $filters = CaseloadResource::filters($caseload->model);

        $query = $caseload->model->query();

        foreach ($filters as $filter) {
            $filter->apply(
                $query,
                $caseload->filters[$filter->getName()] ?? [],
            );
        }

        return $query->get();
    }
}
