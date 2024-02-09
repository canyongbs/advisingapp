<?php

namespace App\Models\Concerns;

use Exception;
use Throwable;
use App\Models\Scopes\ArchivedScope;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

/**
 * @method static EloquentBuilder|QueryBuilder withArchived()
 * @method static EloquentBuilder|QueryBuilder onlyArchived()
 * @method static EloquentBuilder|QueryBuilder withoutArchived()
 */
trait IsArchivable
{
    public function initializeArchivable(): void
    {
        $this->casts['archived_at'] = 'datetime';
        $this->fillable[] = 'archived_at';
    }

    public static function bootIsArchivable(): void
    {
        static::addGlobalScope(new ArchivedScope());
    }

    /**
     * @throws Throwable
     */
    public function archive(): void
    {
        if ($this->isArchived()) {
            return;
        }

        throw_unless($this->isArchivable(), new Exception('This model is not archivable.'));

        $this->archived_at = now();
        $this->save();
    }

    /**
     * @throws Throwable
     */
    public function unarchive(): void
    {
        if ($this->isUnarchived()) {
            return;
        }

        throw_unless($this->isUnarchivable(), new Exception('This model is not unarchivable.'));

        $this->archived_at = null;
        $this->save();
    }

    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }

    public function isUnarchived(): bool
    {
        return ! $this->isArchived();
    }

    public function isArchivable(): bool
    {
        if (method_exists($this, 'trashed')) {
            return ! $this->trashed() && $this->isUnarchived();
        }

        return $this->isUnarchived();
    }

    public function isUnarchivable(): bool
    {
        if (method_exists($this, 'trashed')) {
            return ! $this->trashed() && $this->isArchived();
        }

        return $this->isArchived();
    }
}
