<?php

namespace Assist\Auditing\Listeners;

use Assist\Auditing\Facades\Auditor;

class RecordCustomAudit
{
    public function handle(\Assist\Auditing\Contracts\Auditable $model)
    {
        Auditor::execute($model);
    }
}
