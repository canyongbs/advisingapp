<?php

namespace AdvisingApp\Report\Filament\Pages;

use AdvisingApp\Report\Abstract\StudentReport;
use AdvisingApp\Report\Filament\Widgets\RefreshWidget;
use AdvisingApp\Report\Filament\Widgets\StudentDeliverableTable;
use AdvisingApp\Report\Filament\Widgets\StudentEmailOptInOptOutPieChart;
use AdvisingApp\Report\Filament\Widgets\StudentSmsOptInOptOutPieChart;
use App\Filament\Clusters\ReportLibrary;

class StudentDeliverabilityReport extends StudentReport
{
    protected static ?string $title = 'Deliverability';

    protected static ?string $cluster = ReportLibrary::class;

    protected static string $routePath = 'student-deliverability-report';

    protected static ?string $navigationGroup = 'Students';

    protected $cacheTag = 'report-student-deliverability';

    protected static ?int $navigationSort = 5;

    public function getColumns(): int | string | array
    {
        return [
            'sm' => 2,
            'md' => 2,
            'lg' => 2,
        ];
    }

    public function getWidgets(): array
    {
        return [
            RefreshWidget::make(['cacheTag' => $this->cacheTag]),
            StudentEmailOptInOptOutPieChart::make(['cacheTag' => $this->cacheTag]),
            StudentSmsOptInOptOutPieChart::make(['cacheTag' => $this->cacheTag]),
            StudentDeliverableTable::make(['cacheTag' => $this->cacheTag]),
        ];
    }
}
