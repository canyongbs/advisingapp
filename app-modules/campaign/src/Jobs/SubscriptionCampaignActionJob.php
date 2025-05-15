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
        try {
            DB::beginTransaction();

            $educatable = $this->actionEducatable->educatable;

            throw_if(
                ! $educatable instanceof Subscribable,
                new Exception('The educatable model must implement the Subscribable contract.')
            );
            /** @var Subscribable&Model $educatable */
            $action = $this->actionEducatable->campaignAction;

            if ($action->data['remove_prior']) {
                $educatable->subscriptions()->delete();
            }

            foreach ($action->data['user_ids'] as $userId) {
                resolve(SubscriptionCreate::class)
                    ->handle(User::find($userId), $educatable, false);
            }

            // Because we are attaching multiple Subscriptions, which just creates pivot Models,
            // we don't need to relate any records.
            $this->actionEducatable->markSucceeded();

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            $this->actionEducatable->markFailed();

            throw $e;
        }
    }
}
