<?php

namespace Assist\Audit\Listeners;

use OwenIt\Auditing\Events\Auditing;

class AuditingListener
{
    public function __construct()
    {
    }

    public function handle(Auditing $event): bool
    {
        //ray($event);
        return true;
    }
}
