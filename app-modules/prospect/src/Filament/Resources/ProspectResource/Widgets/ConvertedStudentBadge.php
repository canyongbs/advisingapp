<?php

namespace AdvisingApp\Prospect\Filament\Resources\ProspectResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class ConvertedStudentBadge extends BaseWidget
{
    public $student;

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.student-converted-badge';
}
