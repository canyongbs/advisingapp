<?php

namespace AdvisingApp\Timeline\Policies;

use AdvisingApp\Prospect\Models\Prospect;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class TimelinePolicy
{

    public function create(Authenticatable $authenticatable, $prospect = null): Response
    {
        if ($prospect instanceof Prospect && $prospect->student()->exists()) {
            return Response::deny('You cannot create engagement as Prospect has been converted to a Student.');
        }

        return $authenticatable->canOrElse(
            abilities: 'engagement.create',
            denyResponse: 'You do not have permission to create engagements.'
        );
    }

}