<?php

namespace Assist\CaseloadManagement\Observers;

use Assist\CaseloadManagement\Models\Caseload;

class CaseloadObserver
{
    public function creating(Caseload $caseload): void
    {
        $caseload->user()->associate($caseload->user ?? auth()->user());
    }
}
