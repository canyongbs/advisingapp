<?php

namespace AdvisingApp\Prospect\Observers;

use Illuminate\Support\Facades\Schema;
use AdvisingApp\Prospect\Models\ProspectStatus;

class ProspectStatusObserver
{
    public function creating(ProspectStatus $prospectStatus): void
    {
        if (Schema::hasColumn($prospectStatus->getTable(), 'sort')) {
            $prospectStatus->sort = ProspectStatus::query()->max('sort') + 1;
        }
    }
}
