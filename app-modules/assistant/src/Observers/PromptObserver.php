<?php

namespace AdvisingApp\Assistant\Observers;

use Laravel\Pennant\Feature;
use AdvisingApp\Assistant\Models\Prompt;

class PromptObserver
{
    public function creating(Prompt $prompt): void
    {
        if (Feature::active('prompt-user')) {
            $prompt->user()->associate(auth()->user());
        }
    }
}
