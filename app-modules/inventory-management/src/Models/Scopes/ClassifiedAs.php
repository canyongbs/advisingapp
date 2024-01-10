<?php

namespace AdvisingApp\InventoryManagement\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use AdvisingApp\InventoryManagement\Enums\SystemAssetStatusClassification;

// TODO Make this somewhat re-usable across any system classifications
class ClassifiedAs
{
    public function __construct(
        protected SystemAssetStatusClassification $classification,
    ) {}

    public function __invoke(Builder $query): void
    {
        $query->where('classification', $this->classification);
    }
}
