<?php

namespace Assist\Campaign\Models;

use App\Models\User;
use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Assist\CaseloadManagement\Models\Caseload;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * @mixin IdeHelperCampaign
 */
class Campaign extends BaseModel implements Auditable
{
    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = [
        'caseload_id',
        'name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function caseload(): BelongsTo
    {
        return $this->belongsTo(Caseload::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(CampaignAction::class);
    }

    public function scopeHasNotBeenExecuted(Builder $query): void
    {
        $query->whereDoesntHave('actions', function (Builder $query) {
            $query->whereNotNull('successfully_executed_at');
        });
    }

    public function hasBeenExecuted(): bool
    {
        return $this->actions->contains(fn (CampaignAction $action) => $action->hasBeenExecuted());
    }
}
