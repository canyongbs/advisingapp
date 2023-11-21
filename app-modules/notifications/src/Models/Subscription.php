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
use Assist\Campaign\Models\Contracts\ExecutableFromACampaignAction;

/**
 * @mixin IdeHelperSubscription
 */
class Subscription extends MorphPivot implements ExecutableFromACampaignAction
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
