<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Campaign\Jobs;

use AdvisingApp\Campaign\Enums\CampaignActionType;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Campaign\Models\CampaignActionEducatable;
use AdvisingApp\Segment\Actions\TranslateSegmentFilters;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use App\Models\Tenant;
use App\Models\User;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Support\Facades\Auth;

class ExecuteCampaignAction implements ShouldQueue, ShouldBeUnique
{
    use Batchable;
    use Queueable;

    public int $tries = 3;

    public int $timeout = 600;

    public int $uniqueFor = 600 * 3;

    public function __construct(
        public CampaignAction $action
    ) {}

    public function uniqueId(): string
    {
        return Tenant::current()->getKey() . ':' . $this->action->getKey();
    }

    /**
     * @return array<object>
     */
    public function middleware(): array
    {
        return [new SkipIfBatchCancelled()];
    }

    public function handle(): void
    {
        if ($this->action->cancelled_at !== null) {
            return;
        }

        // Required as some segment filters apply based on the logged in User.
        // The campaign creator is the one who will be logged in.
        if ($this->action->campaign->createdBy instanceof User) {
            Auth::setUser($this->action->campaign->createdBy);
        }

        app(TranslateSegmentFilters::class)
            ->applyFilterToQuery(
                $this->action->campaign->segment,
                $this->action->campaign->segment->model->query()
            )
            ->lazyById(
                1000,
                $this->action->campaign->segment->model->instance()->getKeyName(),
            )
            ->each(function (Model $educatable) {
                throw_if(
                    ! $educatable instanceof Educatable,
                    new Exception('Educatable is not an instance of ' . Educatable::class)
                );

                $campaignActionEducatable = CampaignActionEducatable::query()
                    ->firstOrCreate([
                        'campaign_action_id' => $this->action->getKey(),
                        'educatable_id' => $educatable->getKey(),
                        'educatable_type' => $educatable->getMorphClass(),
                    ]);

                $job = match ($this->action->type) {
                    CampaignActionType::Tags => new TagCampaignActionJob($campaignActionEducatable),
                };

                $this->batch()->add($job);
            });
    }
}
