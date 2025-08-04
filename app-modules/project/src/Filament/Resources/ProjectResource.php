<?php

namespace AdvisingApp\Project\Filament\Resources;

use AdvisingApp\Project\Filament\Resources\ProjectResource\Pages\CreateProject;
use AdvisingApp\Project\Filament\Resources\ProjectResource\Pages\EditProject;
use AdvisingApp\Project\Filament\Resources\ProjectResource\Pages\ListProjects;
use AdvisingApp\Project\Filament\Resources\ProjectResource\Pages\ViewProject;
use AdvisingApp\Project\Models\Project;
use App\Features\ProjectPageFeature;
use App\Models\User;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Project Management';

    protected static ?int $navigationSort = 10;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return ProjectPageFeature::active() && $user->can('project.view-any');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewProject::class,
            EditProject::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'view' => ViewProject::route('/{record}'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }
}
