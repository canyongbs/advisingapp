<?php

namespace AdvisingApp\DataMigration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use AdvisingApp\DataMigration\OneTimeOperationManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use AdvisingApp\DataMigration\Database\Factories\OperationFactory;

class Operation extends Model
{
    use HasFactory;
    use HasUuids;

    public const DISPATCHED_ASYNC = 'async';

    public const DISPATCHED_SYNC = 'sync';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'dispatched',
        'processed_at',
        'completed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = OneTimeOperationManager::getTableName();
    }

    public static function storeOperation(string $operation, bool $async): self
    {
        return self::firstOrCreate([
            'name' => $operation,
            'dispatched' => $async ? self::DISPATCHED_ASYNC : self::DISPATCHED_SYNC,
            'processed_at' => now(),
        ]);
    }

    public function getFilePathAttribute(): string
    {
        return OneTimeOperationManager::pathToFileByName($this->name);
    }

    protected static function newFactory(): OperationFactory
    {
        return new OperationFactory();
    }
}
