<?php

namespace AdvisingApp\InventoryManagement\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;

class AssetType extends BaseModel implements Auditable
{
    use AuditableTrait;
}
