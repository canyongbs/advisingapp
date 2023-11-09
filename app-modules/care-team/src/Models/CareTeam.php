<?php

namespace Assist\CareTeam\Models;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Assist\Campaign\Models\CampaignAction;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Assist\AssistDataModel\Models\Contracts\Educatable;
use Assist\Authorization\Models\Concerns\DefinesPermissions;
use Assist\Campaign\Models\Contracts\ExecutableFromACampaignAction;

/**
 * @mixin IdeHelperCareTeam
 */
class CareTeam extends MorphPivot implements ExecutableFromACampaignAction
{
    use HasFactory;
    use DefinesPermissions;
    use HasUuids;

    public $timestamps = true;

    protected $table = 'care_teams';

    /** @return MorphTo<Educatable> */
    public function educatable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTimestamps();
    }

    public static function executeFromCampaignAction(CampaignAction $action): bool|string
    {
        try {
            DB::beginTransaction();

            $action->campaign->caseload->retrieveRecords()->each(function (Educatable $educatable) use ($action) {
                $educatable->careTeam()->sync(ids: $action->data['user_ids'], detaching: $action->data['remove_prior']);
            });

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();

            return $e->getMessage();
        }

        // Do we need to be able to relate campaigns/actions to the RESULT of their actions?
    }
}
