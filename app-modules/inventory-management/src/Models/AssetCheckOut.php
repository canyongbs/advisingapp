<?php

namespace AdvisingApp\InventoryManagement\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;

class AssetCheckOut extends BaseModel implements Auditable
{
    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = [
        'asset_id',
        'asset_check_in_id',
        'checked_out_by_type',
        'checked_out_by_id',
        'checked_out_to_type',
        'checked_out_to_id',
        'checked_out_at',
        'expected_check_in_at',
        'notes',
    ];

    protected $casts = [
        'checked_out_at' => 'datetime',
        'expected_check_in_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function checkedOutBy(): MorphTo
    {
        return $this->morphTo(
            name: 'checked_out_by',
            type: 'checked_out_by_type',
            id: 'checked_out_by_id',
        );
    }

    public function checkedOutTo(): MorphTo
    {
        return $this->morphTo(
            name: 'checked_out_to',
            type: 'checked_out_to_type',
            id: 'checked_out_to_id',
        );
    }

    public function checkIn(): BelongsTo
    {
        return $this->belongsTo(AssetCheckIn::class);
    }
}
