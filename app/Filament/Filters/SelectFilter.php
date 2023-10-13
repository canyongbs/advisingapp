<?php

namespace App\Filament\Filters;

use OpenSearch\ScoutDriverPlus\Support\Query;
use OpenSearch\ScoutDriverPlus\Builders\QueryBuilderInterface;

class SelectFilter extends \Filament\Tables\Filters\SelectFilter implements OpenSearchFilter
{
    public function openSearchQuery(mixed $state): ?QueryBuilderInterface
    {
        return ! empty($state['values']) ? Query::terms()
            ->field($this->getName())
            ->values($state['values']) : null;
    }
}
