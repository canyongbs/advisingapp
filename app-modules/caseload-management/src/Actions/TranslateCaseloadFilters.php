<?php

namespace Assist\CaseloadManagement\Actions;

use Illuminate\Database\Eloquent\Collection;
use Assist\CaseloadManagement\Models\Caseload;
use Assist\CaseloadManagement\Enums\CaseloadType;
use Assist\CaseloadManagement\Filament\Resources\CaseloadResource;

class TranslateCaseloadFilters
{
    public function handle(Caseload $caseload): Collection|array
    {
        if ($caseload->type === CaseloadType::Dynamic) {
            $filters = CaseloadResource::filters($caseload->model);

            $query = $caseload->model->query();

            foreach ($filters as $filter) {
                $filter->apply(
                    $query,
                    $caseload->filters[$filter->getName()] ?? [],
                );
            }
        } elseif ($caseload->type === CaseloadType::Static) {
            $query = $caseload->subjects();
        }

        /** @phpstan-ignore-next-line */
        return $query->get();
    }
}
