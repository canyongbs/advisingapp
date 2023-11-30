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

namespace Assist\Engagement\Models;

use Exception;
use App\Models\User;
use App\Models\BaseModel;
use Assist\Campaign\Models\CampaignAction;
use Assist\Engagement\Actions\CreateEngagementBatch;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\Engagement\Models\Concerns\HasManyEngagements;
use Assist\Campaign\Models\Contracts\ExecutableFromACampaignAction;
use Assist\Engagement\DataTransferObjects\EngagementBatchCreationData;

/**
 * @mixin IdeHelperEngagementBatch
 */
class EngagementBatch extends BaseModel implements ExecutableFromACampaignAction
{
    use HasManyEngagements;

    protected $fillable = [
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function executeFromCampaignAction(CampaignAction $action): bool|string
    {
        try {
            CreateEngagementBatch::dispatch(EngagementBatchCreationData::from([
                'user' => $action->campaign->user,
                'records' => $action->campaign->caseload->retrieveRecords(),
                'subject' => $action->data['subject'],
                'body' => $action->data['body'],
                'body_json' => $action->data['body_json'],
                'deliveryMethod' => $action->data['delivery_method'],
            ]));

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Do we need to be able to relate campaigns/actions to the RESULT of their actions?
    }
}
