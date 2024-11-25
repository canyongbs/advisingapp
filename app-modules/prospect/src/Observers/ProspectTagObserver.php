<?php

namespace AdvisingApp\Prospect\Observers;

use App\Models\Tag;

class ProspectTagObserver
{
    public function creating(Tag $tag): void
    {
        $tag->type = 'Prospect';
    }
}
