<?php

namespace App\Models\Concerns;

use Exception;
use Throwable;
use App\Models\Scopes\Archived;
use Illuminate\Database\Eloquent\Builder;

trait IsArchivable
{
    public function initializeArchivable(): void
    {
        $this->casts['archived_at'] = 'datetime';
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

    public function scopeArchived(Builder $query): void
    {
        $query->tap(new Archived());
    }

    public function scopeUnarchived(Builder $query): void
    {
        $query->whereNot->tap(new Archived());
    }
}
