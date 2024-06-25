<?php

namespace AdvisingApp\BasicNeeds\Observers;

use AdvisingApp\BasicNeeds\Models\BasicNeedsCategory;
use App\Exceptions\SoftDeleteContraintViolationException;

class BasicNeedsCategoryObserver
{
    public function deleting(BasicNeedsCategory $basicNeedsCategory): void
    {
        if ($basicNeedsCategory->basicNeedsProgram()->count()) {
            throw new SoftDeleteContraintViolationException();
        }
    }
}
