<?php

namespace Assist\Audit\Models;

use Assist\Audit\Settings\AuditSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use OwenIt\Auditing\Models\Audit as BaseAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Assist\Authorization\Models\Concerns\DefinesPermissions;

/**
 * Assist\Audit\Models\Audit
 *
 * @property int $id
 * @property string|null $change_agent_type
 * @property string|null $change_agent_id
 * @property string $event
 * @property string $auditable_type
 * @property string $auditable_id
 * @property array|null $old_values
 * @property array|null $new_values
 * @property string|null $url
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $tags
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $auditable
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $user
 *
 * @method static \Assist\Audit\Database\Factories\AuditFactory factory($count = null, $state = [])
 * @method static Builder|Audit newModelQuery()
 * @method static Builder|Audit newQuery()
 * @method static Builder|Audit query()
 * @method static Builder|Audit whereAuditableId($value)
 * @method static Builder|Audit whereAuditableType($value)
 * @method static Builder|Audit whereChangeAgentId($value)
 * @method static Builder|Audit whereChangeAgentType($value)
 * @method static Builder|Audit whereCreatedAt($value)
 * @method static Builder|Audit whereEvent($value)
 * @method static Builder|Audit whereId($value)
 * @method static Builder|Audit whereIpAddress($value)
 * @method static Builder|Audit whereNewValues($value)
 * @method static Builder|Audit whereOldValues($value)
 * @method static Builder|Audit whereTags($value)
 * @method static Builder|Audit whereUpdatedAt($value)
 * @method static Builder|Audit whereUrl($value)
 * @method static Builder|Audit whereUserAgent($value)
 *
 * @mixin \Eloquent
 */
class Audit extends BaseAudit
{
    use HasFactory;
    use DefinesPermissions;
    use MassPrunable;

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
