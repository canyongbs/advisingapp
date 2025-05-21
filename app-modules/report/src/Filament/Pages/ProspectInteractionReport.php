<?php

namespace AdvisingApp\Report\Filament\Pages;

use AdvisingApp\Report\Abstract\ProspectReport;
use AdvisingApp\Report\Filament\Widgets\ProspectInteractionLineChart;
use AdvisingApp\Report\Filament\Widgets\ProspectInteractionStats;
use AdvisingApp\Report\Filament\Widgets\ProspectInteractionStatusPolarAreaChart;
use AdvisingApp\Report\Filament\Widgets\ProspectInteractionTypeDoughnutChart;
use AdvisingApp\Report\Filament\Widgets\ProspectInteractionUsersTable;
use AdvisingApp\Report\Filament\Widgets\RefreshWidget;
use App\Filament\Clusters\ReportLibrary;

class ProspectInteractionReport extends ProspectReport
{
    protected static ?string $title = 'Interactions';

    protected static ?string $cluster = ReportLibrary::class;

    protected static string $routePath = 'prospect-interaction-report';

    protected static ?string $navigationGroup = 'Prospects';

    protected string $cacheTag = 'report-prospect-interaction';

    protected static ?int $navigationSort = 20;

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
            ProspectInteractionStats::make(['cacheTag' => $this->cacheTag]),
            ProspectInteractionLineChart::make(['cacheTag' => $this->cacheTag]),
            ProspectInteractionTypeDoughnutChart::make(['cacheTag' => $this->cacheTag]),
            ProspectInteractionStatusPolarAreaChart::make(['cacheTag' => $this->cacheTag]),
            ProspectInteractionUsersTable::make(['cacheTag' => $this->cacheTag]),
        ];
    }
}
