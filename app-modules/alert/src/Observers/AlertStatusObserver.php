<?php

namespace AdvisingApp\Alert\Observers;

use AdvisingApp\Alert\Models\AlertStatus;

class AlertStatusObserver
{
    public function creating(AlertStatus $alertStatus): void
    {
        $alertStatus->sort = AlertStatus::query()->max('sort') + 1;
    }
}
