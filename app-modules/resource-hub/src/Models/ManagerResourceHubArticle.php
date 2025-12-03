<?php

namespace AdvisingApp\ResourceHub\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperManagerResourceHubArticle
 */
class ManagerResourceHubArticle extends Pivot
{
    use HasUuids;

    /**
     * @return BelongsTo<User, $this>
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * @return BelongsTo<ResourceHubArticle, $this>
     */
    public function resourceHubArticle(): BelongsTo
    {
        return $this->belongsTo(ResourceHubArticle::class);
    }
}
