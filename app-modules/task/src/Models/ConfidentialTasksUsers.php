<?php

namespace AdvisingApp\Task\Models;

use AdvisingApp\Task\Database\Factories\ConfidentialTasksUsersFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperConfidentialTasksUsers
 */
class ConfidentialTasksUsers extends Pivot
{
    /** @use HasFactory<ConfidentialTasksUsersFactory> */
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
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
