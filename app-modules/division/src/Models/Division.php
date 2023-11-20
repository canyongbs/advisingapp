<?php

namespace Assist\Division\Models;

use App\Models\User;
use App\Models\BaseModel;
use Assist\Team\Models\Team;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\Concerns\HasEmailTemplates;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\Interaction\Models\Concerns\HasManyInteractions;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * @mixin IdeHelperDivision
 */
class Division extends BaseModel implements Auditable
{
    use AuditableTrait;
    use HasManyInteractions;
    use SoftDeletes;
    use HasEmailTemplates;

    protected $fillable = [
        'name',
        'code',
        'description',
        'header',
        'footer',
    ];

    public function createdBy(): BelongsTo
    {
        return $this
            ->belongsTo(User::class);
    }

    public function lastUpdatedBy(): BelongsTo
    {
        return $this
            ->belongsTo(User::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }
}
