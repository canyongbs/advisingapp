<?php

namespace Assist\Prospect\Models;

use DateTimeInterface;
use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * @mixin IdeHelperProspectStatus
 */
class ProspectStatus extends BaseModel implements Auditable
{
    use SoftDeletes;
    use AuditableTrait;

    protected $fillable = [
        'name',
        'color',
    ];

    public function prospects(): HasMany
    {
        return $this->hasMany(Prospect::class, 'status_id');
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
