<?php

namespace AdvisingApp\StudentDataModel\Observers;

use App\Models\Tag;

class StudentTagObserver
{
    public function creating(Tag $tag): void
    {
        $tag->type = 'Student';
    }
}
