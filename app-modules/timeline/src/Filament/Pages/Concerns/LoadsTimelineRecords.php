<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Timeline\Filament\Pages\Concerns;

use Illuminate\Pagination\Cursor;
use Illuminate\Support\Collection;
use Assist\Timeline\Models\Timeline;

trait LoadsTimelineRecords
{
    public int $recordsPerPage = 5;

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
