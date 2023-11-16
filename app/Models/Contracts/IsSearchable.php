<?php

namespace App\Models\Contracts;

use OpenSearch\ScoutDriverPlus\Builders\SearchParametersBuilder;

interface IsSearchable
{
    public function getKeyName();

    public static function searchQuery($query = null): SearchParametersBuilder;
}
