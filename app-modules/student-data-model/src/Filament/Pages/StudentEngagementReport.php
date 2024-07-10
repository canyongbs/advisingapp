<?php

namespace AdvisingApp\StudentDataModel\Filament\Pages;

use Filament\Pages\Dashboard;
use App\Filament\Clusters\ReportLibrary;
use AdvisingApp\Report\Filament\Widgets\RefreshWidget;
use AdvisingApp\StudentDataModel\Filament\Widgets\StudentEngagementStats;
use AdvisingApp\StudentDataModel\Filament\Widgets\MostEngagedStudentsTable;
use AdvisingApp\StudentDataModel\Filament\Widgets\StudentEngagementLineChart;

class StudentEngagementReport extends Dashboard
{
    protected static ?string $title = 'Student Engagement';

    protected static ?string $cluster = ReportLibrary::class;

    protected static string $routePath = 'student-engagement-report';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Students';

    protected $cacheTag = 'prospect-enagement-cache';

    protected static ?int $navigationSort = 30;

    public function getColumns(): int | string | array
    {
        return [
            'sm' => 2,
            'md' => 4,
            'lg' => 4,
        ];
    }

    public function getWidgets(): array
    {
        return [
            RefreshWidget::make(['cacheTag' => $this->cacheTag]),
            StudentEngagementStats::make(['cacheTag' => $this->cacheTag]),
            StudentEngagementLineChart::make(['cacheTag' => $this->cacheTag]),
            MostEngagedStudentsTable::make(['cacheTag' => $this->cacheTag]),
        ];
    }
}
