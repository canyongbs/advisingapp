<?php

namespace AdvisingApp\InventoryManagement\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;

class AssetLocation extends BaseModel implements Auditable
{
    use AuditableTrait;

    protected $fillable = [
        'name',
    ];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'location_id');
    }
}
