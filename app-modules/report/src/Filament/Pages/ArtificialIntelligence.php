<?php

namespace AdvisingApp\Report\Filament\Pages;

use Laravel\Pennant\Feature;
use Filament\Pages\Dashboard;
use App\Filament\Clusters\ReportLibrary;
use AdvisingApp\Report\Filament\Widgets\AiStats;
use AdvisingApp\Report\Filament\Widgets\RefreshWidget;
use AdvisingApp\Report\Filament\Widgets\PromptsCreatedLineChart;
use AdvisingApp\Report\Filament\Widgets\SavedConversationsLineChart;
use AdvisingApp\Report\Filament\Widgets\SpecialActionsDoughnutChart;
use AdvisingApp\Report\Filament\Widgets\PromptsByCategoryDoughnutChart;

class ArtificialIntelligence extends Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $cluster = ReportLibrary::class;

    protected static ?string $navigationGroup = 'Artificial Intelligence';

    protected static ?string $navigationLabel = 'Artificial Intelligence';

    protected static ?string $title = 'Artificial Intelligence';

    protected static string $routePath = 'artificial-intelligence';

    protected static ?int $navigationSort = 10;

    protected $pagePrefix ='report-artificial-intelligence';

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return Feature::active('ai_utilization') && $user->can('report-library.view-any');
    }

    public function getWidgets(): array
    {
        return [
            RefreshWidget::make(['pagePrefix' => $this->pagePrefix]),
            AiStats::make(['pagePrefix' =>  $this->pagePrefix]),
            SavedConversationsLineChart::make(['pagePrefix' =>  $this->pagePrefix]),
            SpecialActionsDoughnutChart::make(['pagePrefix' =>  $this->pagePrefix]),
            PromptsByCategoryDoughnutChart::make(['pagePrefix' =>  $this->pagePrefix]),
            PromptsCreatedLineChart::make(['pagePrefix' =>  $this->pagePrefix]),
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
