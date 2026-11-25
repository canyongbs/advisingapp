<?php

namespace AdvisingApp\Organization\Models\Scopes;

use AdvisingApp\Organization\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class HasDomain
{
    public function __construct(
        protected string $domain,
    ) {}

    /**
     * @param Builder<Organization> $query
     */
    public function __invoke(Builder $query): void
    {
        $query->whereRaw(
            "exists (select 1 from jsonb_array_elements(domains) as element where lower(element->>'domain') = ?)",
            [Str::lower($this->domain)],
        );
    }
}
