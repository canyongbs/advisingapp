<?php

namespace AdvisingApp\Engagement\Observers;

use AdvisingApp\Engagement\Models\EngagementFile;
use App\Features\EngagementFilesCreatedByFeature;
use App\Models\User;

class EngagementFileObserver
{
    public function saving(EngagementFile $engagementFile): void
    {
        $user = auth()->user();

        if (EngagementFilesCreatedByFeature::active() && $user instanceof User && is_null($engagementFile->created_by_id)) {
            $engagementFile->created_by_id = $user->getKey();
            $engagementFile->created_by_type = $user->getMorphClass();
        }
    }
}
