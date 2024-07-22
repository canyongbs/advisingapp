<?php

namespace AdvisingApp\Ai\Events;

use AdvisingApp\Ai\Models\AiMessageFile;
use Illuminate\Foundation\Events\Dispatchable;
use AdvisingApp\Ai\Listeners\DispatchDeleteExternalAiMessageFile;

class AiMessageFileDeleted
{
    use Dispatchable;

    public const LISTENERS = [
        DispatchDeleteExternalAiMessageFile::class,
    ];

    public function __construct(public AiMessageFile $aiMessageFile) {}
}
