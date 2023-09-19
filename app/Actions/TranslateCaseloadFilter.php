<?php

namespace App\Actions;

use App\Models\Caseload;
use Filament\Tables\Filters\Filter;
use Assist\Prospect\Models\Prospect;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use Illuminate\Database\Eloquent\Collection;

class TranslateCaseloadFilter
{
    //TODO: make command pass id
    public function __invoke(Caseload $caseload): Collection|array
    {
        $filters = [
            Filter::make('sap')
                ->query(fn (Builder $query) => $query->where('sap', true)),
        ];

        $query = match ($caseload->model) {
            'student' => Student::query(),
            'prospect' => Prospect::query(),
            default => null,
        };

        foreach ($filters as $filter) {
            $filter->apply(
                $query,
                $caseload->filters[$filter->getName()] ?? [],
            );
        }

        return $query->get();
    }
}
