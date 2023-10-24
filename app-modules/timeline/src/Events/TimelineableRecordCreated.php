<?php

namespace Assist\Timeline\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Assist\AssistDataModel\Models\Contracts\Educatable;

class TimelineableRecordCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public Educatable $educatable,
        public Model $model
    ) {}
}
