<?php

namespace AdvisingApp\Task\Models;

use AdvisingApp\Task\Database\Factories\ConfidentialTasksTeamsFactory;
use AdvisingApp\Team\Models\Team;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperConfidentialTasksTeams
 */
class ConfidentialTasksTeams extends Pivot
{
    /** @use HasFactory<ConfidentialTasksTeamsFactory> */
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
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
