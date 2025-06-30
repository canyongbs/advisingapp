<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Campaign\Models;

use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\Campaign\Enums\CampaignActionType;
use App\Models\BaseModel;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @mixin IdeHelperCampaignAction
 */
class CampaignAction extends BaseModel implements Auditable, HasMedia
{
    use AuditableTrait;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'campaign_id',
        'type',
        'data',
        'execute_at',
        'cancelled_at',
        'execution_dispatched_at',
        'execution_finished_at',
    ];

    protected $casts = [
        'type' => CampaignActionType::class,
        'data' => 'array',
        'execute_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'execution_dispatched_at' => 'datetime',
        'execution_finished_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<Campaign, $this>
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * @return HasMany<CampaignActionEducatable, $this>
     */
    public function campaignActionEducatables(): HasMany
    {
        return $this->hasMany(CampaignActionEducatable::class);
    }

    public function scopeCampaignEnabled(Builder $query): void
    {
        $query->whereRelation('campaign', 'enabled', true);
    }

    public function hasBeenExecuted(): bool
    {
        return ! is_null($this->execution_finished_at);
    }
}
