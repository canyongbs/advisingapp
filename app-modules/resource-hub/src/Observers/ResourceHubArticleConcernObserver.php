<?php

namespace AdvisingApp\ResourceHub\Observers;

use AdvisingApp\ResourceHub\Models\ResourceHubArticleConcern;
use AdvisingApp\ResourceHub\Notifications\ResourceHubArticleConcernCreated;
use AdvisingApp\ResourceHub\Notifications\ResourceHubArticleConcernStatusChanged;

class ResourceHubArticleConcernObserver
{
    public function created(ResourceHubArticleConcern $resourceHubArticleConcern): void
    {
        $resourceHubArticleConcern->createdBy->notifyNow(new ResourceHubArticleConcernCreated($resourceHubArticleConcern));
    }

    public function updated(ResourceHubArticleConcern $resourceHubArticleConcern): void
    {
        if ($resourceHubArticleConcern->wasChanged('status')) {
            $resourceHubArticleConcern->createdBy->notifyNow(new ResourceHubArticleConcernStatusChanged($resourceHubArticleConcern));
        }
    }
}
