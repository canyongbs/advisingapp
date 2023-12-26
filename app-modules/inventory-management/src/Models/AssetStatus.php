<?php

namespace AdvisingApp\InventoryManagement\Models;

use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class AssetStatus extends BaseModel implements Auditable
{
    use AuditableTrait;

    protected $fillable = [
        'name',
    ];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'status_id');
    }
}
