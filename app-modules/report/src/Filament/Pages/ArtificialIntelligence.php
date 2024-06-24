<?php

namespace AdvisingApp\Report\Filament\Pages;

use Filament\Pages\Dashboard;
use App\Filament\Clusters\ReportLibrary;
use AdvisingApp\Report\Filament\Widgets\AiStats;
use AdvisingApp\Report\Filament\Widgets\PromptsByCategoryDoughnutChart;
use AdvisingApp\Report\Filament\Widgets\PromptsCreatedLineChart;
use AdvisingApp\Report\Filament\Widgets\SavedConversationsLineChart;
use AdvisingApp\Report\Filament\Widgets\SpecialActionsDoughnutChart;

class ArtificialIntelligence extends Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $cluster = ReportLibrary::class;

    protected static ?string $navigationGroup = 'Artificial Intelligence';

    protected static ?string $navigationLabel = 'Artificial Intelligence';

    protected static ?string $title = 'Artificial Intelligence';

    protected static string $routePath = 'artificial-intelligence';

    protected static ?int $navigationSort = 10;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('report-library.view-any');
    }

    public function getWidgets(): array
    {
        return [
            AiStats::class,
            SavedConversationsLineChart::class,
            SpecialActionsDoughnutChart::class,
            PromptsByCategoryDoughnutChart::class,
            PromptsCreatedLineChart::class
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
