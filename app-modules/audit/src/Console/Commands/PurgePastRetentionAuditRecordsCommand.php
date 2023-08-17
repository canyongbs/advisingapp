<?php

namespace Assist\Audit\Console\Commands;

use Assist\Audit\Models\Audit;
use Illuminate\Console\Command;
use Assist\Audit\Settings\AuditSettings;

class PurgePastRetentionAuditRecordsCommand extends Command
{
    protected $signature = 'audit:purge-past-retention-audit-records';

    protected $description = 'Purge Audit records that are older than the retention duration.';

    public function handle(AuditSettings $auditSettings): void
    {
        Audit::whereDate(
            column: 'created_at',
            operator: '<',
            value: now()->subDays($auditSettings->retention_duration)
        )->delete();
    }
}
