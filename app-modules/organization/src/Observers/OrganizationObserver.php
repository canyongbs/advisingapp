<?php

namespace AdvisingApp\Organization\Observers;

use AdvisingApp\Organization\Models\Organization;
use AdvisingApp\Prospect\Models\Prospect;
use Illuminate\Support\Collection;

class OrganizationObserver
{
    public function deleted(Organization $organization): void
    {
        $organization->prospects()->chunkById(100, function (Collection $prospects): void {
            $prospects->each(fn (Prospect $prospect) => $prospect->organization()->dissociate()->save());
        });
    }
}
