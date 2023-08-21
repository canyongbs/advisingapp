<?php

namespace Assist\Audit\Overrides;

use Assist\Audit\Overrides\Concerns\AttachOverrides;
use Illuminate\Database\Eloquent\Relations\BelongsToMany as IlluminateBelongsToMany;

class BelongsToMany extends IlluminateBelongsToMany
{
    use AttachOverrides;
}
