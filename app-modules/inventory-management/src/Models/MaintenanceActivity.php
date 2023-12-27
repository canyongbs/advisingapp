<?php

namespace AdvisingApp\InventoryManagement\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\InventoryManagement\Enums\MaintenanceActivityStatus;

class MaintenanceActivity extends BaseModel implements Auditable
{
    use AuditableTrait;

    protected $fillable = [
        'asset_id',
        'date',
        'maintenance_provider_id',
        'notes',
        'scheduled_date',
        'status',
    ];

    protected $casts = [
        'date' => 'datetime',
        'scheduled_date' => 'datetime',
        'status' => MaintenanceActivityStatus::class,
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function maintenanceProvider(): BelongsTo
    {
        return $this->belongsTo(MaintenanceProvider::class);
    }
}
