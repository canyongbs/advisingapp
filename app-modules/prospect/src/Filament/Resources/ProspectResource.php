<?php

namespace Assist\Prospect\Filament\Resources;

use Filament\Pages\Page;
use Filament\Resources\Resource;
use Assist\Prospect\Models\Prospect;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages\EditProspect;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages\ViewProspect;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages\ListProspects;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages\CreateProspect;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectFiles;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectTasks;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectAlerts;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectEngagement;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectInteractions;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages\ProspectEngagementTimeline;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectSubscriptions;

class ProspectResource extends Resource
{
    protected static ?string $model = Prospect::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Records';

    protected static ?int $navigationSort = 2;

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewProspect::class,
            EditProspect::class,
            ManageProspectEngagement::class,
            ManageProspectFiles::class,
            ManageProspectAlerts::class,
            ManageProspectTasks::class,
            ManageProspectSubscriptions::class,
            ManageProspectInteractions::class,
            ProspectEngagementTimeline::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProspects::route('/'),
            'create' => CreateProspect::route('/create'),
            'edit' => EditProspect::route('/{record}/edit'),
            'manage-alerts' => ManageProspectAlerts::route('/{record}/alerts'),
            'manage-engagement' => ManageProspectEngagement::route('/{record}/engagement'),
            'manage-files' => ManageProspectFiles::route('/{record}/files'),
            'manage-interactions' => ManageProspectInteractions::route('/{record}/interactions'),
            'manage-subscriptions' => ManageProspectSubscriptions::route('/{record}/subscriptions'),
            'manage-tasks' => ManageProspectTasks::route('/{record}/tasks'),
            'view' => ViewProspect::route('/{record}'),
            'engagement-timeline' => ProspectEngagementTimeline::route('/{record}/engagement-timeline'),
        ];
    }
}
