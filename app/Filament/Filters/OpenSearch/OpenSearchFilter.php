<?php

namespace App\Filament\Filters\OpenSearch;

use OpenSearch\ScoutDriverPlus\Builders\QueryBuilderInterface;

interface OpenSearchFilter
{
    public function openSearchQuery(mixed $state): ?QueryBuilderInterface;
}
