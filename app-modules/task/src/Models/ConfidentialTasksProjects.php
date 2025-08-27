<?php

namespace AdvisingApp\Task\Models;

use AdvisingApp\Project\Models\Project;
use AdvisingApp\Task\Database\Factories\ConfidentialTasksProjectsFactory;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperConfidentialTasksProjects
 */
class ConfidentialTasksProjects extends Pivot
{
    /** @use HasFactory<ConfidentialTasksProjectsFactory> */
    use HasFactory;

    use HasUuids;

    /**
     * @return BelongsTo<Task, $this>
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
