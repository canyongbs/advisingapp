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

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Assist\Campaign\Enums\CampaignActionType;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * @mixin IdeHelperCampaignAction
 */
class CampaignAction extends BaseModel implements Auditable
{
    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = [
        'campaign_id',
        'type',
        'data',
        'execute_at',
        'last_execution_attempt_at',
        'last_execution_attempt_error',
        'successfully_executed_at',
    ];

    protected $casts = [
        'type' => CampaignActionType::class,
        'data' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function execute(): void
    {
        $response = $this->type->executeAction($this);

        $response === true ? $this->markAsSuccessfullyExecuted() : $this->markAsUnsuccessfullyExecuted($response);
    }

    public function markAsSuccessfullyExecuted(): void
    {
        $this->update([
            'last_execution_attempt_at' => now(),
            'successfully_executed_at' => now(),
        ]);
    }

    public function markAsUnsuccessfullyExecuted(string $response): void
    {
        $this->update([
            'last_execution_attempt_at' => now(),
            'last_execution_attempt_error' => $response,
        ]);
    }

    public function scopeHasNotBeenExecuted(Builder $query): void
    {
        $query->whereNull('successfully_executed_at');
    }

    public function scopeCampaignEnabled(Builder $query): void
    {
        $query->whereRelation('campaign', 'enabled', true);
    }

    public function hasBeenExecuted(): bool
    {
        return ! is_null($this->successfully_executed_at);
    }
}
