<?php

namespace Assist\Audit\Overrides;

use Assist\Audit\Overrides\Concerns\AttachOverrides;
use Illuminate\Database\Eloquent\Relations\MorphToMany as IlluminateMorphToMany;

class MorphToMany extends IlluminateMorphToMany
{
    use AttachOverrides;
}
