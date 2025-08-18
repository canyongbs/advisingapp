<?php

namespace AdvisingApp\Project\Models;

use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\Project\Database\Factories\ProjectMilestoneFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperProjectMilestone
 */
class ProjectMilestone extends Model implements Auditable
{
    /** @use HasFactory<ProjectMilestoneFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;
    use AuditableTrait;

    protected $fillable = [
        'title',
        'description',
        'status_id',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * @return BelongsTo<ProjectMilestoneStatus, $this>
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(ProjectMilestoneStatus::class, 'status_id');
    }

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
