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

namespace Assist\Alert\Models;

use Exception;
use App\Models\BaseModel;
use Assist\Alert\Enums\AlertStatus;
use Assist\Prospect\Models\Prospect;
use Assist\Alert\Enums\AlertSeverity;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use Assist\Campaign\Models\CampaignAction;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Assist\AssistDataModel\Models\Contracts\Educatable;
use Assist\Notifications\Models\Contracts\Subscribable;
use Assist\AssistDataModel\Models\Traits\EducatableScopes;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\Campaign\Models\Contracts\ExecutableFromACampaignAction;
use Assist\Notifications\Models\Contracts\CanTriggerAutoSubscription;

/**
 * @property-read Student|Prospect $concern
 *
 * @mixin IdeHelperAlert
 */
class Alert extends BaseModel implements Auditable, CanTriggerAutoSubscription, ExecutableFromACampaignAction
{
    use SoftDeletes;
    use AuditableTrait;
    use EducatableScopes;

    protected $fillable = [
        'concern_id',
        'concern_type',
        'description',
        'severity',
        'status',
        'suggested_intervention',
    ];

    protected $casts = [
        'severity' => AlertSeverity::class,
        'status' => AlertStatus::class,
    ];

    public function concern(): MorphTo
    {
        return $this->morphTo();
    }

    public function getSubscribable(): ?Subscribable
    {
        return $this->concern instanceof Subscribable ? $this->concern : null;
    }

    public function scopeStatus(Builder $query, AlertStatus $status)
    {
        $query->where('status', $status);
    }

    public static function executeFromCampaignAction(CampaignAction $action): bool|string
    {
        try {
            $action->campaign->caseload->retrieveRecords()->each(function (Educatable $educatable) use ($action) {
                Alert::create([
                    'concern_type' => $educatable->getMorphClass(),
                    'concern_id' => $educatable->getKey(),
                    'description' => $action->data['description'],
                    'severity' => $action->data['severity'],
                    'status' => $action->data['status'],
                    'suggested_intervention' => $action->data['suggested_intervention'],
                ]);
            });

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Do we need to be able to relate campaigns/actions to the RESULT of their actions?
    }
}
