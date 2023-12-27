<?php

namespace AdvisingApp\InventoryManagement\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;

class MaintenanceProvider extends BaseModel implements Auditable
{
    use AuditableTrait;

    protected $fillable = [
        'name',
    ];

    public function maintenanceActivities(): HasMany
    {
        return $this->hasMany(MaintenanceActivity::class, 'asset_id');
    }
}
