<?php

namespace AdvisingApp\Campaign\Jobs;

use AdvisingApp\Notification\Actions\SubscriptionCreate;
use AdvisingApp\Notification\Models\Contracts\Subscribable;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

class SubscriptionCampaignActionJob extends ExecuteCampaignActionOnEducatableJob
{
    public function handle(): void
    {
        $educatable = $this->actionEducatable->educatable;

        throw_if(
            ! $educatable instanceof Subscribable,
            new Exception('The educatable model must implement the Subscribable contract.')
        );
        /** @var Subscribable&Model $educatable */
        $action = $this->actionEducatable->campaignAction;

        DB::beginTransaction();

        try {
            if ($action->data['remove_prior']) {
                $educatable->subscriptions()->delete();
            }

            foreach ($action->data['user_ids'] as $userId) {
                resolve(SubscriptionCreate::class)
                    ->handle(User::find($userId), $educatable, false);
            }
        } catch (Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
