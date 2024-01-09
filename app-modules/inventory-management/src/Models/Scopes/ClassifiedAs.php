<?php

namespace AdvisingApp\InventoryManagement\Models\Scopes;

use Illuminate\Contracts\Database\Eloquent\Builder;
use AdvisingApp\InventoryManagement\Enums\SystemAssetStatusClassification;

class ClassifiedAs
{
    public function __construct(
        protected SystemAssetStatusClassification $classification
    ) {}

    public function __invoke(Builder $query): void
    {
        $query->where('classification', $this->classification);
    }
}
