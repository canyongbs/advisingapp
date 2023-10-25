<?php

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

    public static function executeFromCampaignAction(CampaignAction $action): bool
    {
        try {
            CreateEngagementBatch::dispatch(EngagementBatchCreationData::from([
                'user' => $action->campaign->user,
                'records' => $action->campaign->caseload->retrieveRecords(),
                'subject' => $action->data['subject'],
                'body' => $action->data['body'],
                'deliveryMethods' => $action->data['delivery_methods'],
            ]));

            return true;
        } catch (Exception $e) {
            return false;
        }

        // Do we need to be able to relate campaigns/actions to the RESULT of their actions?
    }
}
