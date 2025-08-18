<?php

namespace AdvisingApp\Project\Filament\Resources;

use AdvisingApp\Project\Filament\Resources\ProjectMilestoneStatusResource\Pages\CreateProjectMilestoneStatus;
use AdvisingApp\Project\Filament\Resources\ProjectMilestoneStatusResource\Pages\EditProjectMilestoneStatus;
use AdvisingApp\Project\Filament\Resources\ProjectMilestoneStatusResource\Pages\ListProjectMilestoneStatuses;
use AdvisingApp\Project\Filament\Resources\ProjectMilestoneStatusResource\Pages\ViewProjectMilestoneStatus;
use AdvisingApp\Project\Models\ProjectMilestoneStatus;
use App\Features\ProjectMilestoneFeature;
use App\Filament\Clusters\ProjectManagement;
use Filament\Resources\Resource;

class ProjectMilestoneStatusResource extends Resource
{
    protected static ?string $model = ProjectMilestoneStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Statuses';

    protected static ?string $cluster = ProjectManagement::class;

    protected static ?int $navigationSort = 20;

    public static function canAccess(): bool
    {
        return ProjectMilestoneFeature::active() && parent::canAccess();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjectMilestoneStatuses::route('/'),
            'create' => CreateProjectMilestoneStatus::route('/create'),
            'edit' => EditProjectMilestoneStatus::route('/{record}/edit'),
            'view' => ViewProjectMilestoneStatus::route('/{record}'),
        ];
    }
}
