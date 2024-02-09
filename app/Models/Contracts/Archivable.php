<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface Archivable
{
    public function archive(): void;

    public function unarchive(): void;

    public function isArchived(): bool;

    public function isUnarchived(): bool;

    public function isArchivable(): bool;

    public function isUnarchivable(): bool;

    public function scopeArchived(Builder $query): void;

    public function scopeUnarchived(Builder $query): void;
}
