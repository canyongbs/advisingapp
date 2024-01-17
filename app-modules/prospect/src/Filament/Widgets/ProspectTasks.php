<?php

namespace AdvisingApp\Prospect\Filament\Widgets;

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Task\Filament\Widgets\TasksWidget;

class ProspectTasks extends TasksWidget
{
    public function title(): string
    {
        return 'My Tasks for Prospects';
    }

    public function concern(): string
    {
        return Prospect::class;
    }
}
