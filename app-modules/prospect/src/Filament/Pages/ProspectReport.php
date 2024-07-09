<?php

namespace AdvisingApp\Prospect\Filament\Pages;

use AdvisingApp\Report\Filament\Widgets\ProspectReportLineChart;
use AdvisingApp\Report\Filament\Widgets\ProspectReportStats;
use AdvisingApp\Report\Filament\Widgets\RefreshWidget;
use Filament\Pages\Dashboard;
use Filament\Pages\Page;

class ProspectReport extends Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Recruitment CRM';

    protected static string $routePath = 'prospect-report';

    protected static ?string $title = 'Prospects (Overview)';

    // protected static string $view = 'advising-prospect.filament.pages.prospect-report';

    protected $cacheTag = 'prospect-report-cache';

    public function getWidgets(): array
    {
        return [
            RefreshWidget::make(['cacheTag' => $this->cacheTag]),
            ProspectReportStats::make(['cacheTag' => $this->cacheTag]),
            ProspectReportLineChart::make(['cacheTag' => $this->cacheTag]),
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
