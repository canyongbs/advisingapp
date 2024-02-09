<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
