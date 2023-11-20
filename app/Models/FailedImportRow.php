<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperFailedImportRow
 */
class FailedImportRow extends BaseModel
{
    use Prunable;

    protected $casts = [
        'data' => 'array',
    ];

    public function import(): BelongsTo
    {
        return $this->belongsTo(Import::class);
    }

    public function prunable(): Builder
    {
        return static::where(
            'created_at',
            '<=',
            now()->subMonth(),
        );
    }
}
