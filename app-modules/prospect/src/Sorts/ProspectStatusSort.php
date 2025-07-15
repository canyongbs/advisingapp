<?php

namespace AdvisingApp\Prospect\Sorts;

use AdvisingApp\Prospect\Models\Prospect;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class ProspectStatusSort implements Sort
{
    /**
     * @param Builder<Prospect> $query
     * @param bool $descending
     * @param string $property
     *
     * @return Builder<Prospect>
     */
    public function __invoke(Builder $query, bool $descending, string $property): Builder
    {
        $direction = $descending ? 'DESC' : 'ASC';

        return $query
            ->leftJoin('prospect_statuses as ps', 'prospects.status_id', '=', 'ps.id')
            ->orderBy('ps.name', $direction)
            ->select('prospects.*');
    }
}
