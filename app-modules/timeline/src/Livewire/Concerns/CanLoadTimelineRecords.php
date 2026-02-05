<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Timeline\Livewire\Concerns;

use AdvisingApp\Timeline\Models\Timeline;
use Illuminate\Pagination\Cursor;
use Illuminate\Support\Collection;

trait CanLoadTimelineRecords
{
    public int $recordsPerPage = 5;

    /** @var Collection<int, Timeline> $timelineRecords */
    public Collection $timelineRecords;

    public ?string $nextCursor = null;

    public bool $initialLoad = false;

    public bool $hasMorePages = false;

    public function loadTimelineRecords(): void
    {
        // For some reason, the intersection observer still seems to be present
        // Even though it's in a conditional block that should not be rendered
        // This is an additional protection to prevent loading more records
        if ($this->initialLoad === true && $this->hasMorePages === false) {
            return;
        }

        if ($this->initialLoad === false) {
            $this->initialLoad = true;
        }

        $records = Timeline::query()
            ->forEntity($this->recordModel)
            ->whereHas('timelineable')
            ->whereIn(
                'timelineable_type',
                collect($this->modelsToTimeline)->map(fn ($model) => resolve($model)->getMorphClass())->toArray()
            )
            ->orderBy('record_sortable_date', 'desc')
            ->cursorPaginate(
                $this->recordsPerPage,
                ['*'],
                'cursor',
                Cursor::fromEncoded($this->nextCursor)
            );

        $this->timelineRecords->push(...$records->items());

        $this->hasMorePages = $records->hasMorePages();

        if ($this->hasMorePages === true) {
            $this->nextCursor = $records->nextCursor()->encode();
        }
    }
}
