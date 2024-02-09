<?php

namespace App\Models\Contracts;

interface Archivable
{
    public function archive(): void;

    public function unarchive(): void;

    public function isArchived(): bool;

    public function isUnarchived(): bool;

    public function isArchivable(): bool;

    public function isUnarchivable(): bool;
}
