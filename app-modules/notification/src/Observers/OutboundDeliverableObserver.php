<?php

namespace AdvisingApp\Notification\Observers;

use AdvisingApp\Notification\Models\OutboundDeliverable;
use AdvisingApp\ServiceManagement\Models\ServiceRequest;
use AdvisingApp\Timeline\Events\TimelineableRecordCreated;
use AdvisingApp\Timeline\Events\TimelineableRecordDeleted;

class OutboundDeliverableObserver
{
    public function created(OutboundDeliverable $outboundDeliverable): void
    {
        $timelinable = [
            ServiceRequest::class,
        ];

        if (in_array($outboundDeliverable->related::class, $timelinable)) {
            TimelineableRecordCreated::dispatch($outboundDeliverable->related, $outboundDeliverable);
        }
    }

    public function deleted(OutboundDeliverable $outboundDeliverable): void
    {

        $timelinable = [
            ServiceRequest::class,
        ];

        if (in_array($outboundDeliverable->related::class, $timelinable)) {
            TimelineableRecordDeleted::dispatch($outboundDeliverable->related, $outboundDeliverable);
        }
    }
}
