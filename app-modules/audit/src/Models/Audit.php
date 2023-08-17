<?php

namespace Assist\Audit\Models;

use OwenIt\Auditing\Models\Audit as BaseAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Assist\Authorization\Models\Concerns\DefinesPermissions;

class Audit extends BaseAudit
{
    use HasFactory;
    use DefinesPermissions;
}
