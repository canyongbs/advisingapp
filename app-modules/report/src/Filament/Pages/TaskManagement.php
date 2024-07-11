<?php

namespace AdvisingApp\Report\Filament\Pages;

use App\Filament\Pages\Dashboard;
use App\Filament\Clusters\ReportLibrary;
use AdvisingApp\Report\Filament\Widgets\TaskStats;
use AdvisingApp\Report\Filament\Widgets\RefreshWidget;
use AdvisingApp\Report\Filament\Widgets\MostRecentTasksTable;
use AdvisingApp\Report\Filament\Widgets\TaskCumulativeCountLineChart;

class TaskManagement extends Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $cluster = ReportLibrary::class;

    protected static ?string $navigationGroup = 'Engagement Features';

    protected static ?string $navigationLabel = 'Tasks (Overview)';

    protected static ?string $title = 'Tasks (Overview)';

    protected static string $routePath = 'tasks';

    protected static ?int $navigationSort = 10;

    protected $cacheTag = 'report-tasks';

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('report-library.view-any');
    }

    public function getWidgets(): array
    {
        return [
            RefreshWidget::make(['cacheTag' => $this->cacheTag]),
            TaskStats::make(['cacheTag' => $this->cacheTag]),
            TaskCumulativeCountLineChart::make(['cacheTag' => $this->cacheTag]),
            MostRecentTasksTable::make(['cacheTag' => $this->cacheTag]),
        ];
    }

    public function getColumns(): int | string | array
    {
        return [
            'sm' => 2,
            'md' => 4,
            'lg' => 4,
        ];
    }
}
