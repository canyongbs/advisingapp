<?php

namespace AdvisingApp\InventoryManagement\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;

class Asset extends BaseModel implements Auditable
{
    use AuditableTrait;
}
