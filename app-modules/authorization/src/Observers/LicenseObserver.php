<?php

namespace AdvisingApp\Authorization\Observers;

use Exception;
use AdvisingApp\Authorization\Models\License;

class LicenseObserver
{
    public function creating(License $license): void
    {
        if (! $license->type->hasAvailableLicenses()) {
            throw new Exception("There are no available {$license->type->getLabel()} licenses.");
        }
    }
}
