<?php

namespace App\Filament\Columns\OpenSearch\Concerns;

use OpenSearch\ScoutDriverPlus\Support\Query;
use OpenSearch\ScoutDriverPlus\Builders\QueryBuilderInterface;

trait OpenSearchQueryDefault
{
    public function openSearchQuery(string $search): ?QueryBuilderInterface
    {
        return Query::multiMatch()
            ->fields($this->getSearchColumns())
            ->type('bool_prefix')
            ->query($search)
            ->fuzziness('AUTO')
            ->analyzer('standard');
    }
}
