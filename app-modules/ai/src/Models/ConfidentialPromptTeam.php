<?php

namespace AdvisingApp\Ai\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ConfidentialPromptTeam extends Pivot
{
    use HasUuids;
}
