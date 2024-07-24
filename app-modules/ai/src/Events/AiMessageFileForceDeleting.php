<?php

namespace AdvisingApp\Ai\Events;

use AdvisingApp\Ai\Models\AiMessageFile;
use Illuminate\Foundation\Events\Dispatchable;
use AdvisingApp\Ai\Listeners\DeleteExternalAiMessageFile;

class AiMessageFileForceDeleting
{
    use Dispatchable;

    public const LISTENERS = [
        DeleteExternalAiMessageFile::class,
    ];

    public function __construct(public AiMessageFile $aiMessageFile) {}
}
