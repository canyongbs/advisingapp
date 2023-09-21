<?php

namespace App\Models;

use App\Imports\Importer;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperImport
 */
class Import extends BaseModel
{
    protected $casts = [
        'completed_at' => 'timestamp',
        'processed_rows' => 'integer',
        'total_rows' => 'integer',
        'successful_rows' => 'integer',
    ];

    public function failedRows(): HasMany
    {
        return $this->hasMany(FailedImportRow::class);
    }

    /**
     * @return BelongsTo<User, Import>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param array<string, string> $columnMap
     * @param array<string, mixed> $options
     */
    public function getImporter(
        array $columnMap,
        array $options,
    ): Importer {
        return app($this->importer, [
            'import' => $this,
            'columnMap' => $columnMap,
            'options' => $options,
        ]);
    }

    public function getFailedRowsCount(): int
    {
        return $this->total_rows - $this->successful_rows;
    }
}
