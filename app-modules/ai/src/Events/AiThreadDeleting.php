<?php

namespace AdvisingApp\Ai\Events;

use AdvisingApp\Ai\Models\AiThread;
use Illuminate\Foundation\Events\Dispatchable;

class AiThreadDeleting
{
    use Dispatchable;

    public function __construct(public AiThread $aiThread) {}
}
