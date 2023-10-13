<?php

namespace App\Filament\Filters;

use OpenSearch\ScoutDriverPlus\Builders\QueryBuilderInterface;

interface OpenSearchFilter
{
    public function openSearchQuery(mixed $state): ?QueryBuilderInterface;
}
