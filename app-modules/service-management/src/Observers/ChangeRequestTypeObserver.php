<?php

namespace AdvisingApp\ServiceManagement\Observers;

use AdvisingApp\ServiceManagement\Models\ChangeRequestType;

class ChangeRequestTypeObserver
{
    public function deleted(ChangeRequestType $changeRequestType): void
    {
        if ($changeRequestType->changeRequests()->doesntExist()) {
            $changeRequestType->forceDelete();
        }
    }
}
