<?php

namespace AdvisingApp\Ai\Events;

use AdvisingApp\Ai\Models\AiMessageFile;
use Illuminate\Foundation\Events\Dispatchable;

class AiMessageFileDeleted
{
    use Dispatchable;

    public function __construct(public AiMessageFile $aiMessageFile) {}
}
