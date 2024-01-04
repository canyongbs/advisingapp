<?php

namespace App\Models\Scopes;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;
use AdvisingApp\Authorization\Enums\LicenseType;

class HasLicense
{
    /**
     * @param LicenseType | string | array<LicenseType | string> | null $type
     */
    public function __construct(
        protected LicenseType | string | array | null $type,
    ) {}

    public function __invoke(Builder $query): void
    {
        if (blank($this->type)) {
            return;
        }

        foreach (Arr::wrap($this->type) as $type) {
            $query->whereRelation('licenses', 'type', $type);
        }
    }
}
