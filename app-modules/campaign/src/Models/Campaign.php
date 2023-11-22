<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
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
