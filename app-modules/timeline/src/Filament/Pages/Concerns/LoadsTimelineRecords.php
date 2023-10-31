<?php

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
            ->forEducatable($this->recordModel)
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
