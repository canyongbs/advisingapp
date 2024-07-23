<?php

namespace AdvisingApp\Ai\Events;

use AdvisingApp\Ai\Models\AiThread;
use Illuminate\Foundation\Events\Dispatchable;

class AiThreadForceDeleting
{
    use Dispatchable;

    public const LISTENERS = [];

    public function __construct(public AiThread $aiThread) {}
}
