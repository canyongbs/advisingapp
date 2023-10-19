<?php

namespace Assist\Timeline\Filament\Pages\Concerns;

trait LoadsRecords
{
    public int $recordsPerPage = 3;

    public function loadMoreRecords(): void
    {
        $this->recordsPerPage += 3;
    }
}
