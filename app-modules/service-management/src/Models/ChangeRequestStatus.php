<?php

namespace AdvisingApp\ServiceManagement\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;

class ChangeRequestStatus extends BaseModel implements Auditable
{
    use AuditableTrait;
}
