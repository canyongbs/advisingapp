<?php

namespace Assist\Audit\Models;

use Assist\Audit\Settings\AuditSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use OwenIt\Auditing\Models\Audit as BaseAudit;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Assist\Authorization\Models\Concerns\DefinesPermissions;

/**
 * @mixin IdeHelperAudit
 */
class Audit extends BaseAudit
{
    use HasFactory;
    use DefinesPermissions;
    use MassPrunable;
    use HasUuids;

    public function prunable(): Builder
    {
        return static::where(
            'created_at',
            '<=',
            now()->subDays(
                app(AuditSettings::class)
                    ->retention_duration_in_days
            ),
        );
    }
}
