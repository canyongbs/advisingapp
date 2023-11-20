<?php

namespace App\Filament\Columns\OpenSearch;

use OpenSearch\ScoutDriverPlus\Builders\QueryBuilderInterface;

interface OpenSearchColumn
{
    public function openSearchQuery(string $search): ?QueryBuilderInterface;
}
