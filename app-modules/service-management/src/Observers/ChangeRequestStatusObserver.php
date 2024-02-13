<?php

namespace AdvisingApp\ServiceManagement\Observers;

use AdvisingApp\ServiceManagement\Models\ChangeRequestStatus;

class ChangeRequestStatusObserver
{
    public function deleted(ChangeRequestStatus $changeRequestStatus): void
    {
        if ($changeRequestStatus->changeRequests()->doesntExist()) {
            $changeRequestStatus->forceDelete();
        }
    }
}
