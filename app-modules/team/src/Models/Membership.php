<?php

namespace Assist\Team\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Membership extends Pivot
{
    use HasUuids;

    protected $table = 'team_user';
}
