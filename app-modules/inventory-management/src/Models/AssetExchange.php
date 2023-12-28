<?php

namespace AdvisingApp\InventoryManagement\Models;

use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\InventoryManagement\Enums\AssetExchangeType;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class AssetExchange extends BaseModel implements Auditable
{
    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = [
        'asset_id',
        'for_id',
        'for_type',
        'performed_by_id',
        'performed_by_type',
        'type',
    ];

    protected $casts = [
        'type' => AssetExchangeType::class,
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function scopeCheckIn(Builder $query): void
    {
        $query->where('type', AssetExchangeType::CheckIn);
    }

    public function scopeCheckOut(Builder $query): void
    {
        $query->where('type', AssetExchangeType::CheckOut);
    }
}
