<?php

namespace AdvisingApp\Project\Observers;

use AdvisingApp\Project\Models\ProjectMilestone;

class ProjectMilestoneObserver
{
    public function creating(ProjectMilestone $projectMilestone): void
    {
        if (blank($projectMilestone->created_by_id)) {
            $projectMilestone->created_by_id = auth()->id();
        }
    }
}
