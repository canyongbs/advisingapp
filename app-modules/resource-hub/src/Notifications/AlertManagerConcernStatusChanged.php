<?php

namespace AdvisingApp\ResourceHub\Notifications;

use AdvisingApp\Notification\Notifications\Messages\MailMessage;
use AdvisingApp\ResourceHub\Enums\ConcernStatus;
use AdvisingApp\ResourceHub\Filament\Resources\ResourceHubArticles\ResourceHubArticleResource;
use AdvisingApp\ResourceHub\Models\ResourceHubArticleConcern;
use App\Models\EmailTemplate;
use App\Models\User;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class AlertManagerConcernStatusChanged extends Notification
{
    use Queueable;

    public function __construct(public ResourceHubArticleConcern $resourceHubArticleConcern, public ConcernStatus $oldStatus) {}

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
    public function toDatabase(User $notifiable): array
    {
        $resourceHubArticle = $this->resourceHubArticleConcern->resourceHubArticle;
        //TODO: link to the concerns tab
        $url = ResourceHubArticleResource::getUrl('view', ['record' => $resourceHubArticle->getKey()]);

        $link = new HtmlString("<a href='{$url}' target='_blank' class='underline'>{$resourceHubArticle->title}</a>");

        return FilamentNotification::make()
            ->title("The status of a concern has changed from {$this->oldStatus->getLabel()} to {$this->resourceHubArticleConcern->status->getLabel()} by {$this->resourceHubArticleConcern->lastUpdatedBy->name} on the resource hub article {$link}.")
            ->success()
            ->getDatabaseMessage();
    }
}
