<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Campaign\Notifications;

use AdvisingApp\Campaign\Filament\Resources\Campaigns\CampaignResource;
use AdvisingApp\Campaign\Models\CampaignAction;
use App\Models\User;
use Exception;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class CampaignActionFinished extends Notification
{
    use Queueable;

    public function __construct(
        public CampaignAction $action,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $campaignUrl = CampaignResource::getUrl('view', ['record' => $this->action->campaign]);

        $campaignLink = new HtmlString("<a href='{$campaignUrl}' target='_blank' class='underline'>{$this->action->campaign->name}</a>");

        $counts = $this->action->campaignActionEducatables()
            ->selectRaw('
                COUNT(*) as total_executions,
                COUNT(CASE WHEN succeeded_at IS NOT NULL THEN 1 END) as total_succeeded,
                COUNT(CASE WHEN last_failed_at IS NOT NULL AND succeeded_at IS NULL THEN 1 END) as total_failed
            ')
            ->first();

        $totalExecutions = $counts->total_executions; // @phpstan-ignore property.notFound
        $totalSucceeded = $counts->total_succeeded; // @phpstan-ignore property.notFound
        $totalFailed = $counts->total_failed; // @phpstan-ignore property.notFound

        throw_if(
            $totalExecutions != $totalSucceeded + $totalFailed,
            new Exception('Total executions do not match the sum of succeeded and failed executions.')
        );

        $actionLabel = $this->action->type->getLabel();

        $message = match (true) {
            $totalExecutions === $totalSucceeded => "{$campaignLink} successfully completed the {$actionLabel} journey step for {$totalExecutions} {$this->action->campaign->group->model->getLabel()}s.",
            $totalExecutions === $totalFailed => "{$campaignLink} could not complete the {$actionLabel} journey step for {$totalExecutions} {$this->action->campaign->group->model->getLabel()}s.",
            default => "{$campaignLink} completed the {$actionLabel} journey step for {$totalExecutions} {$this->action->campaign->group->model->getLabel()}s. {$totalSucceeded} were successful and {$totalFailed} were unable to be executed.",
        };

        $notification = FilamentNotification::make()->title($message);

        if ($totalExecutions === $totalSucceeded) {
            $notification->success();
        } elseif ($totalExecutions === $totalFailed) {
            $notification->danger();
        } else {
            $notification->warning();
        }

        return $notification->getDatabaseMessage();
    }
}
