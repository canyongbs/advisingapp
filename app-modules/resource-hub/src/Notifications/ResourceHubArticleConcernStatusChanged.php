<?php

namespace AdvisingApp\ResourceHub\Notifications;

use AdvisingApp\ResourceHub\Filament\Resources\ResourceHubArticles\ResourceHubArticleResource;
use AdvisingApp\ResourceHub\Models\ResourceHubArticleConcern;
use App\Models\User;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class ResourceHubArticleConcernStatusChanged extends Notification
{
    use Queueable;

    public function __construct(public ResourceHubArticleConcern $resourceHubArticleConcern) {}

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

        $url = ResourceHubArticleResource::getUrl('view', ['record' => $resourceHubArticle->getKey()]);

        $link = new HtmlString("<a href='{$url}' target='_blank' class='underline'>{$resourceHubArticle->title}</a>");

        return FilamentNotification::make()
            ->title("The status has changed to {$this->resourceHubArticleConcern->status->getLabel()} on a resource hub article you raised a concern on {$link}.")
            ->success()
            ->getDatabaseMessage();
    }
}
