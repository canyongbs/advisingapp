<?php

namespace AdvisingApp\Report\Filament\Pages;

use AdvisingApp\Report\Abstract\StudentReport;
use AdvisingApp\Report\Filament\Widgets\RefreshWidget;
use AdvisingApp\Report\Filament\Widgets\StudentInteractionLineChart;
use AdvisingApp\Report\Filament\Widgets\StudentInteractionStats;
use AdvisingApp\Report\Filament\Widgets\StudentInteractionStatusPolarAreaChart;
use AdvisingApp\Report\Filament\Widgets\StudentInteractionTypeDoughnutChart;
use AdvisingApp\Report\Filament\Widgets\StudentInteractionUsersTable;
use App\Filament\Clusters\ReportLibrary;

class StudentInteractionReport extends StudentReport
{
    protected static ?string $title = 'Interactions';

    protected static ?string $cluster = ReportLibrary::class;

    protected static string $routePath = 'student-interaction-report';

    protected static ?string $navigationGroup = 'Students';

    protected $cacheTag = 'report-student-interaction';

    protected static ?int $navigationSort = 3;

    public function getColumns(): int | string | array
    {
        return [
            'sm' => 12,
            'md' => 12,
            'lg' => 12,
        ];
    }

    public function getWidgets(): array
    {
        return [
            RefreshWidget::make(['cacheTag' => $this->cacheTag]),
            StudentInteractionStats::make(['cacheTag' => $this->cacheTag]),
            StudentInteractionLineChart::make(['cacheTag' => $this->cacheTag]),
            StudentInteractionTypeDoughnutChart::make(['cacheTag' => $this->cacheTag]),
            StudentInteractionStatusPolarAreaChart::make(['cacheTag' => $this->cacheTag]),
            StudentInteractionUsersTable::make(['cacheTag' => $this->cacheTag]),
        ];
    }
}
