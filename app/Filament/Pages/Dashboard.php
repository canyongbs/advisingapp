<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Filament\Widgets\MyTasks;
use App\Filament\Widgets\MyStudents;
use App\Filament\Widgets\MyProspects;
use App\Filament\Widgets\TotalStudents;
use App\Filament\Widgets\WelcomeWidget;
use App\Filament\Widgets\TotalProspects;
use App\Filament\Widgets\RecentProspectsList;
use Filament\Pages\Dashboard as BasePage;
use App\Filament\Widgets\MyServiceRequests;
use App\Filament\Widgets\ProspectGrowthChart;
use App\Filament\Widgets\RecentKnowledgeBaseArticlesList;

class Dashboard extends BasePage
{
    protected static ?string $navigationLabel = 'My Dashboard';

    protected ?string $heading = 'My Dashboard';

    public function getWidgets(): array
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('authorization.view_dashboard') ? [
            //1
            WelcomeWidget::class,
            //2
            TotalStudents::class,
            TotalProspects::class,
            MyStudents::class,
            MyProspects::class,
            //3
            ProspectGrowthChart::class,
            //4
            RecentProspectsList::class,
            RecentKnowledgeBaseArticlesList::class,
            //5
            MyServiceRequests::class,
            MyTasks::class,
        ] : [
            WelcomeWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'lg' => 4,
        ];
    }
}
