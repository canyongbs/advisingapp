<?php

namespace AdvisingApp\Project\Models;

use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\Project\Database\Factories\ProjectMilestoneStatusFactory;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperProjectMilestoneStatus
 */
class ProjectMilestoneStatus extends Model implements Auditable
{
    /** @use HasFactory<ProjectMilestoneStatusFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;
    use AuditableTrait;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * @return HasMany<ProjectMilestone, $this>
     */
    public function milestones(): HasMany
    {
        return $this->hasMany(ProjectMilestone::class, 'status_id');
    }
}
