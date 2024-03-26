<?php

namespace AdvisingApp\Assistant\Observers;

use AdvisingApp\Assistant\Models\Prompt;

class PromptObserver
{
    public function creating(Prompt $prompt): void
    {
        $prompt->user()->associate(auth()->user());
    }
}
