<?php

namespace Assist\Notifications\Models;

use Exception;
use App\Models\User;
use DateTimeInterface;
use Illuminate\Support\Facades\DB;
use Assist\Campaign\Models\CampaignAction;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Assist\Notifications\Actions\SubscriptionCreate;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Assist\Notifications\Models\Contracts\Subscribable;
use Assist\Authorization\Models\Concerns\DefinesPermissions;

/**
 * @mixin IdeHelperSubscription
 */
class Subscription extends MorphPivot
{
    use HasFactory;
    use DefinesPermissions;
    use HasUuids;

    public $timestamps = true;

    protected $table = 'subscriptions';

    protected $fillable = [
        'user_id',
        'subscribable_id',
        'subscribable_type',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscribable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function executeFromCampaignAction(CampaignAction $action): bool|string
    {
        try {
            DB::beginTransaction();

            $action->campaign->caseload->retrieveRecords()->each(function (Subscribable $subscribable) use ($action) {
                if ($action->data['remove_prior']) {
                    $subscribable->subscriptions()->delete();
                }

                collect($action->data['user_ids'])
                    ->each(
                        fn ($userId) => resolve(SubscriptionCreate::class)
                            ->handle(User::find($userId), $subscribable, false)
                    );
            });

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();

            return $e->getMessage();
        }

        // Do we need to be able to relate campaigns/actions to the RESULT of their actions?
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
