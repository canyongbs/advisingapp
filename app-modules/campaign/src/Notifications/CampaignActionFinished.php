<?php

namespace AdvisingApp\Campaign\Notifications;

use AdvisingApp\Campaign\Filament\Resources\CampaignResource;
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
            $totalExecutions === $totalSucceeded => "{$campaignLink} successfully completed the {$actionLabel} journey step for {$totalExecutions} {$this->action->campaign->segment->model->getLabel()}s.",
            $totalExecutions === $totalFailed => "{$campaignLink} could not complete the {$actionLabel} journey step for {$totalExecutions} {$this->action->campaign->segment->model->getLabel()}s.",
            default => "{$campaignLink} completed the {$actionLabel} journey step for {$totalExecutions} {$this->action->campaign->segment->model->getLabel()}s. {$totalSucceeded} were successful and {$totalFailed} were unable to be executed.",
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
