<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Project\Filament\Resources\Projects;

use AdvisingApp\Pipeline\Filament\Resources\Pipelines\PipelineResource;
use AdvisingApp\Project\Filament\Resources\Projects\Pages\CreateProject;
use AdvisingApp\Project\Filament\Resources\Projects\Pages\EditProject;
use AdvisingApp\Project\Filament\Resources\Projects\Pages\ListProjects;
use AdvisingApp\Project\Filament\Resources\Projects\Pages\ManageAuditors;
use AdvisingApp\Project\Filament\Resources\Projects\Pages\ManageFiles;
use AdvisingApp\Project\Filament\Resources\Projects\Pages\ManageManagers;
use AdvisingApp\Project\Filament\Resources\Projects\Pages\ManageMilestones;
use AdvisingApp\Project\Filament\Resources\Projects\Pages\ManageProjectPipelines;
use AdvisingApp\Project\Filament\Resources\Projects\Pages\ManageTasks;
use AdvisingApp\Project\Filament\Resources\Projects\Pages\ViewProject;
use AdvisingApp\Project\Models\Project;
use BackedEnum;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use UnitEnum;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string | UnitEnum | null $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 90;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewProject::class,
            EditProject::class,
            ManageTasks::class,
            ManageManagers::class,
            ManageAuditors::class,
            ManageFiles::class,
            ManageProjectPipelines::class,
            ManageMilestones::class,
        ]);
    }

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make(static::getNavigationLabel())
                ->group(static::getNavigationGroup())
                ->parentItem(static::getNavigationParentItem())
                ->icon(static::getNavigationIcon())
                ->activeIcon(static::getActiveNavigationIcon())
                ->isActiveWhen(fn () => request()->routeIs(static::getRouteBaseName() . '.*', PipelineResource::getRouteBaseName() . '.*'))
                ->badge(static::getNavigationBadge(), color: static::getNavigationBadgeColor())
                ->badgeTooltip(static::getNavigationBadgeTooltip())
                ->sort(static::getNavigationSort())
                ->url(static::getNavigationUrl()),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'view' => ViewProject::route('/{record}'),
            'edit' => EditProject::route('/{record}/edit'),
            'files' => ManageFiles::route('/{record}/files'),
            'manage-managers' => ManageManagers::route('/{record}/managers'),
            'manage-auditors' => ManageAuditors::route('/{record}/auditors'),
            'manage-pipelines' => ManageProjectPipelines::route('/{record}/pipelines'),
            'manage-milestones' => ManageMilestones::route('/{record}/milestones'),
            'manage-tasks' => ManageTasks::route('/{record}/tasks'),
        ];
    }
}
