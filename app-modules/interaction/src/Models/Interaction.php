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

namespace Assist\Interaction\Models;

use Exception;
use App\Models\BaseModel;
use Illuminate\Support\Collection;
use Assist\Division\Models\Division;
use OwenIt\Auditing\Contracts\Auditable;
use Assist\Campaign\Models\CampaignAction;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\AssistDataModel\Models\Contracts\Educatable;
use Assist\Notifications\Models\Contracts\Subscribable;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\Campaign\Models\Contracts\ExecutableFromACampaignAction;
use Assist\Notifications\Models\Contracts\CanTriggerAutoSubscription;

/**
 * @mixin IdeHelperInteraction
 */
class Interaction extends BaseModel implements Auditable, CanTriggerAutoSubscription, ExecutableFromACampaignAction
{
    use AuditableTrait;

    protected $fillable = [
        'user_id',
        'interactable_id',
        'interactable_type',
        'interaction_campaign_id',
        'interaction_driver_id',
        'division_id',
        'interaction_outcome_id',
        'interaction_relation_id',
        'interaction_status_id',
        'interaction_type_id',
        'start_datetime',
        'end_datetime',
        'subject',
        'description',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];

    public function getWebPermissions(): Collection
    {
        return collect(['import', ...$this->webPermissions()]);
    }

    public function getSubscribable(): ?Subscribable
    {
        return $this->interactable instanceof Subscribable ? $this->interactable : null;
    }

    public function interactable(): MorphTo
    {
        return $this->morphTo(
            name: 'interactable',
            type: 'interactable_type',
            id: 'interactable_id',
        );
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(InteractionCampaign::class, 'interaction_campaign_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(InteractionDriver::class, 'interaction_driver_id');
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function outcome(): BelongsTo
    {
        return $this->belongsTo(InteractionOutcome::class, 'interaction_outcome_id');
    }

    public function relation(): BelongsTo
    {
        return $this->belongsTo(InteractionRelation::class, 'interaction_relation_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(InteractionStatus::class, 'interaction_status_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(InteractionType::class, 'interaction_type_id');
    }

    public static function executeFromCampaignAction(CampaignAction $action): bool|string
    {
        try {
            $action->campaign->caseload->retrieveRecords()->each(function (Educatable $educatable) use ($action) {
                Interaction::create([
                    'user_id' => $action->campaign->user_id,
                    'interactable_type' => $educatable->getMorphClass(),
                    'interactable_id' => $educatable->getKey(),
                    'interaction_type_id' => $action->data['interaction_type_id'],
                    'interaction_relation_id' => $action->data['interaction_relation_id'],
                    'interaction_campaign_id' => $action->data['interaction_campaign_id'],
                    'interaction_driver_id' => $action->data['interaction_driver_id'],
                    'interaction_status_id' => $action->data['interaction_status_id'],
                    'interaction_outcome_id' => $action->data['interaction_outcome_id'],
                    'division_id' => $action->data['division_id'],
                ]);
            });

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Do we need to be able to relate campaigns/actions to the RESULT of their actions?
    }
}
