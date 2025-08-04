<?php

namespace AdvisingApp\Project\Observers;

use AdvisingApp\Project\Models\Project;

class ProjectObserver
{
  public function creating(Project $project): void
  {
    if (is_null($project->createdBy)) {
      $user = auth()->user();
      $project->createdBy()->associate($user);
    }
  }
}
