<?php

namespace AdvisingApp\InventoryManagement\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;

class AssetCheckIn extends BaseModel implements Auditable
{
    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = [
        'asset_id',
        'checked_in_by_type',
        'checked_in_by_id',
        'checked_in_from_type',
        'checked_in_from_id',
        'checked_in_at',
        'notes',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function checkedInBy(): MorphTo
    {
        return $this->morphTo(
            name: 'checked_in_by',
            type: 'checked_in_by_type',
            id: 'checked_in_by_id',
        );
    }

    public function checkedInFrom(): MorphTo
    {
        return $this->morphTo(
            name: 'checked_in_from',
            type: 'checked_in_from_type',
            id: 'checked_in_from_id',
        );
    }

    public function checkOut(): HasOne
    {
        return $this->hasOne(AssetCheckOut::class);
    }
}
