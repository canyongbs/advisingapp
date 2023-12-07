<?php

namespace Assist\LaravelAuditing\Listeners;

use Assist\LaravelAuditing\Facades\Auditor;

class RecordCustomAudit
{
    public function handle(\Assist\LaravelAuditing\Contracts\Auditable $model)
    {
        Auditor::execute($model);
    }
}
